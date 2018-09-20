<?php

namespace WPRedisearch;

use WPRedisearch\Admin;
use WPRedisearch\RediSearch\Setup;
use WPRedisearch\RediSearch\Index;
use WPRedisearch\RediSearch\Search;
use WPRedisearch\Settings;

/**
 * WPRedisearch Class.
 *
 * This is main class called to initiate all functionalities this plugin provides.
 *
 */
class WPRedisearch {

	/**
   * Redis client to be used through entire website.
	 * @param object $client
	 */
  public static $client;

	/**
   * Set this if there is any kind of errors.
	 * @param object $redisearchException
	 */
  public static $redisearchException = false;

	/**
   * Set this if suggestion is not added.
	 * @param object $suggestionException
	 */
  public static $suggestionException = false;

	/**
   * Redisearch index info.
	 * @param object $indexInfo
	 */
  public static $indexInfo = null;

	/**
	 * @param object $admin
	 */
  private $admin;

	/**
	 * @param object $admin
	 */
  private $search_query_posts = array();

  public function __construct() {
    add_action( 'wp_enqueue_scripts', array( $this, 'wp_redisearch_public_enqueue_scripts' ) );
    $this->admin = new Admin;
    $this->wp_redisearch_handle_ajax_requests();
    $this->wp_redisearch_admin_notice();
    // Do the search
    if ( !self::$redisearchException ) {
      add_filter( 'posts_request', array( $this, 'wp_redisearch_posts_request' ), 10, 2 );
      add_filter( 'the_posts', array( $this, 'filter_the_posts' ), 10, 2 );
      add_action( 'wp_insert_post', array( $this->admin, 'wp_redisearch_index_post_on_publish' ), 10, 3 );
    }
  }

  /**
  * Check for errors like:
  * - Redis server
  * - Redisearch module
  * - If index exists
  * @since    0.1.0
  * @param
  * @return
  */
  public function wp_redisearch_admin_notice() {
    try {
      self::$client = Setup::connect();
    } catch (\Exception $e) {
      if ( isset( $e ) )  {
        $connection_exception = true;
        self::$redisearchException = true;
      }
    }
    if ( isset( $connection_exception ) ) {
      add_action( 'admin_notices', array(__CLASS__, 'redis_server_connection_notice' ) );
    } else {
      // Check if RediSearch module is loaded.
      try {
        $loaded_modules = self::$client->rawCommand('MODULE', ['LIST']);
        foreach ($loaded_modules as $module) {
          if ( in_array( 'ft', $module ) ) {
            $ft_module = true;
          }
        }
      } catch (\Exception $e) {
        if ( isset( $e ) )  {
          $module_exception = true;
        }
      }
      if ( isset( $module_exception ) || !isset( $ft_module )  ) {
        self::$redisearchException = true;
        add_action( 'admin_notices', array(__CLASS__, 'redisearch_not_loaded_notice' ) );
      } else {
        $index_name = Settings::indexName();
        // Check if index exists.
        try {
          self::$indexInfo = self::$client->rawCommand('FT.INFO', [$index_name]);
        } catch (\Exception $e) {
          if ( isset( $e ) )  {
            $index_not_exist = true;
          }
        }
        if ( self::$indexInfo == 'Unknown Index name' || isset( $index_not_exist ) ) {
          self::$redisearchException = true;
          add_action( 'admin_notices', array(__CLASS__, 'redisearch_index_not_exist_notice' ) );
        }
        // Check if suggestion exists.
        if ( Settings::get( 'wp_redisearch_suggestion' )) {
          try {
            $suggestion = self::$client->rawCommand('FT.SUGGET', [$index_name . 'Sugg', 'a', 'FUZZY', 'MAX', '2']);
          } catch (\Exception $e) {
            if ( isset( $e ) )  {
              self::$suggestionException = true;
            }
          }
          if ( is_null($suggestion) ) {
            self::$suggestionException = true;
          }
        }
      }
    }
  }
  
  /**
  * Show admin notice if error in redis server connection.
  * @since    0.1.0
  * @param
  * @return
  */
  public static function redis_server_connection_notice() {
    $redis_settings_page = admin_url('admin.php?page=wp-redisearch');
    ?>
    <div class="notice notice-error is-dismissible">
      <p><?php printf( __( 'Something went wrong while conencting to Redis Server! go to <a href="%s">settings</a>', 'wp-redisearch' ), $redis_settings_page); ?></p>
    </div>
    <?php
  }

  /**
  * Show admin notice RediSearch module not loaded.
  * @since    0.1.0
  * @param
  * @return
  */
  public static function redisearch_not_loaded_notice() {
    $redis_settings_page = admin_url('admin.php?page=wp-redisearch');
    ?>
    <div class="notice notice-error is-dismissible">
      <p><?php printf( __( 'RediSearch module not loaded! go to <a href="%s">settings</a>', 'wp-redisearch' ), $redis_settings_page); ?></p>
    </div>
    <?php
  }

  /**
  * Show admin notice RediSearch module not loaded.
  * @since    0.1.0
  * @param
  * @return
  */
  public static function redisearch_index_not_exist_notice() {
    $redis_settings_page = admin_url('admin.php?page=wp-redisearch');
    ?>
    <div class="notice notice-error is-dismissible">
      <p><?php printf( __( 'Redis server is running and RediSearch module is loaded! But your index not exist. This mean your site never been indexed or for some reasons, the index have been deleted. Please go to <a href="%s">settings page</a> and re-index your website.', 'wp-redisearch' ), $redis_settings_page); ?></p>
    </div>
    <?php
  }

  /**
  * Ajax actions listeners.
  * @since    0.1.0
  * @param
  * @return
  */
  public function wp_redisearch_handle_ajax_requests() {
    add_action('wp_ajax_wp_redisearch_add_to_index', array( $this->admin, 'wp_redisearch_add_to_index' ) );
    add_action('wp_ajax_nopriv_wp_redisearch_add_to_index', array( $this->admin, 'wp_redisearch_add_to_index' ) );
    
    add_action('wp_ajax_wp_redisearch_drop_index', array( $this->admin, 'wp_redisearch_drop_index' ) );
    add_action('wp_ajax_nopriv_wp_redisearch_drop_index', array( $this->admin, 'wp_redisearch_drop_index' ) );

    add_action('wp_ajax_wp_redisearch_get_suggestion', array( $this, 'wp_redisearch_get_suggestion' ) );
    add_action('wp_ajax_nopriv_wp_redisearch_get_suggestion', array( $this, 'wp_redisearch_get_suggestion' ) );
  }

  /**
  * Ajax actions listeners.
  * @since    0.1.0
  * @param
  * @return
  */
  public function wp_redisearch_public_enqueue_scripts() {
    wp_enqueue_script( 'wp_redisearch_public_js', WPRS_URL . 'lib/Public/js/wp-redisearch.js', array( 'jquery' ), WPRS_VERSION, true );
    $suggestion = Settings::get( 'wp_redisearch_suggestion' ) && !self::$suggestionException;
    $localized_data = array(
			'ajaxUrl' 				    => admin_url( 'admin-ajax.php' ),
			'suggestionEnabled' 	=> $suggestion
		);
		wp_localize_script( 'wp_redisearch_public_js', 'wpRds', $localized_data );
    wp_enqueue_style( 'wp_redisearch_public_css', WPRS_URL . 'lib/Public/css/wp-redisearch.css', array(), WPRS_VERSION );
  }
  
  /**
   * Filter the posts to return redisearch posts.
   * @since    0.1.0
   * @param array $posts
	 * @param object $query
	 * @return array $new_posts
   */
	public function filter_the_posts( $posts, $query ) {
    if ( is_admin() ||
        !$query->is_main_query() ||
        ( method_exists( $query, 'is_search' ) && ! $query->is_search() ) ||
        empty( $query->query_vars['s'] )
      ) {
      return $posts;
    }
		$new_posts = $this->search_query_posts;
    return $new_posts;
	}

  /**
   * Filter search query and return posts found in redisearch.
   * Reset query to return nothing.
   * @since    0.1.0
   * @param string $request
	 * @param object $query
	 * @return string
   */
  public function wp_redisearch_posts_request( $request, $query ) {
    global $wpdb;
    if ( self::$redisearchException ) {
      $query->redisearch_success = false;
      return $request;
    }
    
    if ( is_admin() ||
        !$query->is_main_query() ||
        ( method_exists( $query, 'is_search' ) && ! $query->is_search() ) ||
        empty( $query->query_vars['s'] )
      ) {
      return $request;
    }
    
    $search = new Search( self::$client );
    $search_results = $search->search( $query );
    $search_count = $search_results[0];

    if ( $search_results[0] == 0 ) {
      $query->redisearch_success = true;
      return $request;
    }
    unset( $search_results[0] );

    $args = array(
      'post_type'     => 'any',
      'post_status'   => 'any',
      'orderby'       => 'post__in',
      'post__in'      => $search_results
    );
    $searched_posts = new \WP_Query( $args );
    $this->search_query_posts = $searched_posts->posts;
    $query->found_posts = $search_count;
    $query->redisearch_success = true;
    $query->max_num_pages = ceil( $search_count / $query->get( 'posts_per_page' ) );
    
    return "SELECT * FROM $wpdb->posts WHERE 1=0";
  }

  public function wp_redisearch_get_suggestion() {
    $search = new Search( self::$client );
    $search_results = $search->suggest( $_POST['term'] );
    echo json_encode( $search_results );
    wp_die();
  } 

}
