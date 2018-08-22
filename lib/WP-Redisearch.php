<?php

namespace WPRedisearch;

use WPRedisearch\Admin;
use WPRedisearch\RediSearch\Setup;
use WPRedisearch\RediSearch\Index;
use WPRedisearch\RediSearch\Search;

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
	 * @param object $admin
	 */
  private $admin;

  public function __construct() {
    add_action( 'wp_enqueue_scripts', array( $this, 'wp_redisearch_public_enqueue_scripts' ) );
    $this->admin = new Admin;
    // Check if Redis server is on and we can connect to it.
    try {
      self::$client = Setup::connect();
    } catch (\Exception $e) {
      if ( isset( $e ) )  {
        $connection_exception = true;
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
        add_action( 'admin_notices', array(__CLASS__, 'redisearch_not_loaded_notice' ) );
      }
    }
    
    $this->wp_redisearch_handle_ajax_requests();

    // Do the search
    add_action( 'pre_get_posts', array( $this, 'wp_redisearch_pre_get_posts' ) );
  }

  /**
  * Show admin notice if error in redis server connection.
  * @since    0.1.0
  * @param
  * @return
  */
  public static function redis_server_connection_notice() {
    $redis_settings_page = admin_url('admin.php?page=crb_carbon_fields_container_redis_server.php');
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
    $redis_settings_page = admin_url('admin.php?page=crb_carbon_fields_container_redis_server.php');
    ?>
    <div class="notice notice-error is-dismissible">
      <p><?php printf( __( 'RediSearch module not loaded! go to <a href="%s">settings</a>', 'wp-redisearch' ), $redis_settings_page); ?></p>
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
    $localized_data = array(
			'ajaxUrl' 				=> admin_url( 'admin-ajax.php' )
		);
		wp_localize_script( 'wp_redisearch_public_js', 'wpRds', $localized_data );
    wp_enqueue_style( 'wp_redisearch_public_css', WPRS_URL . 'lib/Public/css/wp-redisearch.css', array(), WPRS_VERSION );
  }
  

  public function wp_redisearch_pre_get_posts( $query ) {
    if ( $query->is_home() && $query->is_search() && $query->is_main_query() ) {
      $search = new Search( self::$client );
      $search_results = $search->search();
      unset( $search_results[0] );
      unset( $query->query_vars['s'] );
		  $query->set('post__in', $search_results );
    }
  }

  public function wp_redisearch_get_suggestion() {
    $search = new Search( self::$client );
    $search_results = $search->suggest( $_POST['term'] );
    echo json_encode( $search_results );
    wp_die();
  }

}
