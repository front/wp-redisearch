<?php

namespace WpRediSearch;

use FKRediSearch\Query\Query;
use WpRediSearch\RediSearch\Client;
use WpRediSearch\Admin;
use WpRediSearch\RediSearch\Index;
use WpRediSearch\RediSearch\Search;
use WpRediSearch\Features;
use WpRediSearch\Features\Synonym;
use WpRediSearch\Features\LiveSearch;
use WpRediSearch\Features\WooCommerce;
use WpRediSearch\Features\Document;
use WpRediSearch\RedisRaw\PredisAdapter;
use WpRediSearch\Settings;

/**
 * WpRediSearch Class.
 *
 * This is main class called to initiate all functionalities this plugin provides.
 *
 */
class WpRediSearch {

  /**
   * The redisearch client connection.
   *
   * @var PredisAdapter
   */
  private $client;

	/**
   * Set this if there is any kind of errors.
	 * @param object $redisearchException
	 */
  public static $redisearchException = false;

	/**
   * Set this if redis server not running or can't connect to.
	 * @param object $serverException
	 */
  public static $serverException = false;

	/**
   * Set this if redisearch module not loaded.
	 * @param object $moduleException
	 */
  public static $moduleException = false;

	/**
   * Set this if index not exist.
	 * @param object $indexException
	 */
  public static $indexException = false;

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
  private $searchQueryPosts = array();

  public function __construct() {
    $this->redisearchAdminNotice();
    // First, initiate features
    if ( !self::$serverException && !self::$moduleException ) {
      Features::init();
      new LiveSearch;
      new Synonym;
      new WooCommerce;
      new Document;
    }

    $this->admin = new Admin;
    $this->handleAjaxRequests();
    // Do the search
    if ( !self::$redisearchException ) {
      add_filter( 'posts_request', array( $this, 'redisearchPostsRequest' ), 10, 2 );
      add_filter( 'the_posts', array( $this, 'filterThePosts' ), 10, 2 );
      add_action( 'wp_insert_post', array( $this->admin, 'wp_redisearch_index_post_on_publish' ), 10, 3 );
      add_action( 'save_post', array( $this->admin, 'wp_redisearch_index_post_on_publish' ), 10, 3 );
      
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
  public function redisearchAdminNotice() {
    try {
      $this->client = (new Client())->return();
    } catch (\Exception $e) {
      if ( isset( $e ) )  {
        self::$serverException = true;
      }
    }
    if ( self::$serverException ) {
      self::$redisearchException = true;
      add_action( 'admin_notices', array(__CLASS__, 'redisServerConnectionNotice' ) );
    } else {
      // Check if RediSearch module is loaded.
      try {
        $loaded_modules = $this->client->rawCommand('MODULE', ['LIST']);
        if ( isset( $loaded_modules ) && !empty( $loaded_modules ) ) {
          foreach ($loaded_modules as $module) {
            if ( !in_array( 'search', $module ) ) {
              self::$moduleException = true;
            }
          }
        } else {
          self::$moduleException = true;
        }
      } catch (\Exception $e) {
        if ( isset( $e ) )  {
          self::$moduleException = true;
        }
      }
      if ( self::$moduleException ) {
        self::$redisearchException = true;
        add_action( 'admin_notices', array(__CLASS__, 'redisearchNotLoadedNotice' ) );
      } else {
        $index_name = Settings::indexName();
        // Check if index exists.
        try {
          self::$indexInfo = $this->client->rawCommand('FT.INFO', [$index_name]);
        } catch (\Exception $e) {
          if ( isset( $e ) )  {
            $indexNotFound = true;
          }
        }
        if ( self::$indexInfo === 'Unknown Index name' || isset( $indexNotFound ) ) {
          self::$indexException = true;
          self::$redisearchException = true;
          add_action( 'admin_notices', array(__CLASS__, 'redisearchIndexNotExistNotice' ) );
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
  public static function redisServerConnectionNotice() {
    $redis_settings_page = admin_url('admin.php?page=redisearch');
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
  public static function redisearchNotLoadedNotice() {
    $redis_settings_page = admin_url('admin.php?page=redisearch');
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
  public static function redisearchIndexNotExistNotice() {
    $redis_settings_page = admin_url('admin.php?page=redisearch');
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
  public function handleAjaxRequests() {
    add_action('wp_ajax_wp_redisearch_add_to_index', array( $this->admin, 'wp_redisearch_add_to_index' ) );
    add_action('wp_ajax_nopriv_wp_redisearch_add_to_index', array( $this->admin, 'wp_redisearch_add_to_index' ) );
    
    add_action('wp_ajax_wp_redisearch_write_to_disk', array( $this->admin, 'wp_redisearch_write_to_disk' ) );
    add_action('wp_ajax_nopriv_wp_redisearch_write_to_disk', array( $this->admin, 'wp_redisearch_write_to_disk' ) );
    
    add_action('wp_ajax_wp_redisearch_drop_index', array( $this->admin, 'wp_redisearch_drop_index' ) );
    add_action('wp_ajax_nopriv_wp_redisearch_drop_index', array( $this->admin, 'wp_redisearch_drop_index' ) );

    add_action('wp_ajax_wp_redisearch_save_feature', array( Features::init(), 'wp_redisearch_save_feature' ) );
    add_action('wp_ajax_nopriv_wp_redisearch_save_feature', array( Features::init(), 'wp_redisearch_save_feature' ) );
  }
  
  /**
   * Filter the posts to return redisearch posts.
   * @since    0.1.0
   * @param array $posts
	 * @param object $query
	 * @return array $new_posts
   */
	public function filterThePosts( $posts, $query ) {
    if ( !Settings::SearchInAdmin() ||
        !$query->is_main_query() ||
        ( method_exists( $query, 'is_search' ) && ! $query->is_search() ) ||
        empty( $query->query_vars['s'] )
      ) {
      return $posts;
    }
    return $this->searchQueryPosts;
	}

  /**
   * Filter search query and return posts found in redisearch.
   * Reset query to return nothing.
   *
   * @param string $request
   * @param object $query
   *
   * @return string
   * @since    0.1.0
   */
  public function redisearchPostsRequest( string $request, $query ) {
    global $wpdb;
    if ( self::$redisearchException ) {
      $query->redisearch_success = false;
      return $request;
    }
    
    if ( !Settings::SearchInAdmin() ||
        !$query->is_main_query() ||
        ( method_exists( $query, 'is_search' ) && ! $query->is_search() ) ||
        empty( $query->query_vars['s'] )
      ) {
      return $request;
    }

    $search = new Query( $this->client, Settings::indexName() );

    // Offset search results based on pagination
    $from = 0;
    $offset = $query->query_vars['posts_per_page'];
    if ( isset( $query->query_vars['paged'] ) && $query->query_vars['paged'] > 1 ) {
      $from = $query->query_vars['posts_per_page'] * ( $query->query_vars['paged'] - 1 );
    }
    $results = $search
      ->limit( $from, $offset )
      ->return( array( 'post_id' ) )
      ->search( $query->query_vars['s'] );
    $searchResults = $results->getDocuments();

    $searchCount = $results->getCount();
    
    if ( $searchCount == 0 ) {
      $query->redisearch_success = true;
      return $request;
    }

    $searchResults = array_map( function( $res ) {
      return $res->post_id;
    }, $searchResults );
    $args = array(
      'post_type'     => 'any',
      'post_status'   => 'any',
      'orderby'       => 'post__in',
      'post__in'      => $searchResults
    );

    /**
     * filter wp_redisearch_before_search_wp_query
     * Fires before wp_query. This is useful if you want for some reasons, manipulate WP_Query
     * 
     * @since 0.2.2
     * @param array $args             Array of arguments passed to WP_Query
     * @return array $args            Array of manipulated arguments
		 */
    $args = apply_filters( 'wp_redisearch_before_search_wp_query', $args );
    
    $searched_posts = new \WP_Query( $args );
    /**
     * filter wp_redisearch_after_search_wp_query
     * Fires after wp_query. This is useful if you want to manipulate results of WP_Query
     * 
     * @since 0.2.2
     * @param object $query             WP_Query
     * @param array $args               Array of arguments passed to WP_Query
     * @param object $searched_posts    Result object of WP_Query
     */
    $query = apply_filters( 'wp_redisearch_after_search_wp_query', $query, $searched_posts, $args );
    
    $this->searchQueryPosts = $searched_posts->posts;
    $query->found_posts = $searchCount;
    $query->redisearch_success = true;
    $query->max_num_pages = ceil( $searchCount / $query->get( 'posts_per_page' ) );

    
    return "SELECT * FROM $wpdb->posts WHERE 1=0";
  }

}
