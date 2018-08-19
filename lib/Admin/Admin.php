<?php

namespace WPRedisearch;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class Admin {

  public function __construct() {
    self::init();
  }


  public static function init() {
    add_action( 'carbon_fields_register_fields', array(__CLASS__, 'wp_redisearch_options' ) );
    add_action( 'after_setup_theme', array(__CLASS__, 'wp_redisearch_load' ) );
  }

  public static function wp_redisearch_options() {
    $wp_redisearch_options = Container::make( 'theme_options', __( 'WP redisearch', 'wp-redisearch' ) )
      ->set_page_menu_position( 20 )
      ->set_icon( 'dashicons-search' )
      ->add_fields( array(
        Field::make( 'separator', 'wp_redisearch_redis_server_separator', __( 'Redis server configurations', 'wp-redisearch' ) ),
        Field::make( 'text', 'wp_redisearch_server', __( 'Redis server', 'wp-redisearch' ) ),
        Field::make( 'text', 'wp_redisearch_port', __( 'Redis port', 'wp-redisearch' ) ),
        Field::make( 'text', 'wp_redisearch_index_name', __( 'Redisearch index name', 'wp-redisearch' ) ),
        Field::make( 'separator', 'wp_redisearch_redis_suggestion_separator', __( 'Auto suggestion | Live search', 'wp-redisearch' ) ),
        Field::make( 'checkbox', 'wp_redisearch_redis_suggestion', __( 'Enable auto suggestion | Live search', 'wp-redisearch' ) ),
      )
    );

    // Indexable post types and fields
    Container::make( 'theme_options', 'Indexables' )
    ->set_page_parent( $wp_redisearch_options )
    ->add_fields( wp_redisearch_custom_fields() );
  }


  public static function wp_redisearch_load() {
    \Carbon_Fields\Carbon_Fields::boot();
  }


}
function wp_redisearch_custom_fields() {

  $post_types = get_post_types([
    'public' => true,
    'exclude_from_search' => false,
    'show_ui' => true,
    ]);
  $return = array(
      Field::make( 'separator', 'wp_redisearch_redis_post_types_separator', 'Post types to index' ),
      Field::make( 'set', 'wp_redisearch_redis_post_types',  __( 'Post types', 'wp-redisearch' ) )->add_options( $post_types ),
      Field::make( 'separator', 'wp_redisearch_redis_fields', __( 'Custom fields', 'wp-redisearch' ) )
  );

  return $return;
}