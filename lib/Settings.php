<?php

namespace WPRedisearch;


class Settings {

  /**
  * Get index name. 
  * If it was set in the settings page, return it or return site url like `wp_redisearch_com`
  * @since    0.1.0
  * @param
  * @return string    $index_name
  */
  public static function indexName() {
    if ( did_action('carbon_fields_register_fields') && did_action('carbon_fields_fields_registered') ) {
      $index_name = carbon_get_theme_option( 'wp_redisearch_index_name' );
    } else {
      $index_name = get_option( '_wp_redisearch_index_name' );
    }

    if ( !isset( $index_name) ) {
      $site_url = get_bloginfo('wpurl');
      $site_url = preg_replace('#^https?://#', '', $site_url);
      $site_url = str_replace('-', '_', $site_url);
      $site_url = str_replace('.', '_', $site_url);
      $site_url = str_replace('/', '_', $site_url);
      $index_name = $site_url;
    }
    return $index_name;
  }

  /**
  * Return redis server.
  * @since    0.1.0
  * @param
  * @return string    $redis_server
  */
  public static function RedisServer() {
    if ( did_action('carbon_fields_register_fields') && did_action('carbon_fields_fields_registered') ) {
      $redis_server = carbon_get_theme_option( 'wp_redisearch_server' );
    } else {
      $redis_server = get_option( '_wp_redisearch_server' );
    }
    return isset( $redis_server ) ? $redis_server : '127.0.0.1';
  }

  /**
  * Return redis port.
  * @since    0.1.0
  * @param
  * @return string    $redis_port
  */
  public static function RedisPort() {
    if ( did_action('carbon_fields_register_fields') && did_action('carbon_fields_fields_registered') ) {
      $redis_port = carbon_get_theme_option( 'wp_redisearch_port' );
    } else {
      $redis_port = get_option( '_wp_redisearch_port' );
    }
    return isset( $redis_port ) ? $redis_port : '6379';
  }

  /**
  * Get options
  * @since    0.1.0
  * @param
  * @return string    $option_value
  */
  public static function get( $option, $default = null ) {
    $option_value = get_option( $option, $default );
    return $option_value;
  }

  /**
  * args for WP_Query for indexing
  * @since    0.1.0
  * @param
  * @return string    $args
  */
  public static function query_args() {
    return array(
			'post_type'              => array_keys( self::get( 'wp_redisearch_post_types' ) ),
			'post_status'            => array('publish'),
			'ignore_sticky_posts'    => true,
			'orderby'                => 'ID',
			'order'                  => 'DESC',
			'fields'                 => 'all',
    );
  }

}