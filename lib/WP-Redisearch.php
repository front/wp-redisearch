<?php

namespace WPRedisearch;

use WPRedisearch\Admin;

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
    $this->wp_redisearch_handle_ajax_requests();
  }

  /**
  * Ajax actions listeners.
  * @since    0.1.0
  * @param
  * @return object $fields
  */
  public function wp_redisearch_handle_ajax_requests() {
    add_action('wp_ajax_wp_redisearch_add_to_index', array( $this->admin, 'wp_redisearch_add_to_index' ) );
    add_action('wp_ajax_nopriv_wp_redisearch_add_to_index', array( $this->admin, 'wp_redisearch_add_to_index' ) );
  }

}
