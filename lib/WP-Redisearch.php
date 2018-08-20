<?php

namespace WPRedisearch;

use WPRedisearch\Admin;
use WPRedisearch\RediSearch\Setup;
use WPRedisearch\RediSearch\Index;

/**
 * WPRedisearch Class.
 *
 * This is main class called to initiate all functionalities this plugin provides.
 *
 */

class WPRedisearch {

  private $admin;
  public function __construct() {
    $this->admin = new Admin;
    // Check if Redis server is on and we can connect to it.
    try {
      $client = Setup::connect();
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
        $module_loaded = $client->rawCommand('MODULE', ['LIST']);
        foreach ($module_loaded as $module) {
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
  }

  /**
  * Show admin notice if error in redis server connection.
  * @since    0.1.0
  * @param
  * @return
  */
  public static function redis_server_connection_notice() {
    ?>
    <div class="notice notice-error is-dismissible">
      <p><?php _e( 'Something went wrong while conencting to Redis Server!', 'wp-redisearch' ); ?></p>
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
    ?>
    <div class="notice notice-error is-dismissible">
      <p><?php _e( 'RediSearch module not loaded!', 'wp-redisearch' ); ?></p>
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
  }

}
