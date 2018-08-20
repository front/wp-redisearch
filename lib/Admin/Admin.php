<?php

namespace WPRedisearch;

use Carbon_Fields\Container;
use Carbon_Fields\Field;
use WPRedisearch\RediSearch\Index;
use WPRedisearch\RediSearch\Setup;

class Admin {

  public function __construct() {
    add_action( 'admin_enqueue_scripts', array( $this, 'wp_redisearch_enqueue_scripts' ) );
    self::init();
  }


  /**
  * Initiate admin.
  * @since    0.1.0
  * @param
  * @return 
  */
  public static function init() {
    add_action( 'carbon_fields_register_fields', array(__CLASS__, 'wp_redisearch_options' ) );
    add_action( 'after_setup_theme', array(__CLASS__, 'wp_redisearch_load' ) );
  }

  public static function wp_redisearch_options() {
    $wp_redisearch_options = Container::make( 'theme_options', __( 'WP redisearch', 'wp-redisearch' ) )
      ->set_page_menu_position( 20 )
      ->set_icon( 'dashicons-search' )
      ->add_fields( self::wp_redisearch_status_page() );

    // Redis server configurations.
    Container::make( 'theme_options', __( 'Redis Server', 'wp-redisearch' ) )
    ->set_page_parent( $wp_redisearch_options )
    ->add_fields( self::wp_redisearch_redis_server_conf() );

    // Indexable post types and fields
    Container::make( 'theme_options', __( 'Indexables', 'wp-redisearch' ) )
    ->set_page_parent( $wp_redisearch_options )
    ->add_fields( self::wp_redisearch_custom_fields() );
  }

  public static function wp_redisearch_load() {
    \Carbon_Fields\Carbon_Fields::boot();
  }

  /**
  * Fields for Redis Status option page.
  * @since    0.1.0
  * @param
  * @return object $fields
  */
  public static function wp_redisearch_status_page() {
    $fields = array(
      Field::make( 'separator', 'wp_redisearch_redis_status_separator', __( 'Redisearch Status', 'wp-redisearch' ) ),
        Field::make( 'html', 'wp_redisearch_index_posts' )->set_html( __( '<button id="wpRediSearchIndexBtn" class="button button-primary button-large">Index posts</button>', 'wp-redisearch' ) )
    );
    return $fields;
  }

  /**
  * Fields for Redis Server Configuration option page.
  * @since    0.1.0
  * @param
  * @return object $fields
  */
  public static function wp_redisearch_redis_server_conf() {
    $fields = array(
      Field::make( 'separator', 'wp_redisearch_redis_server', __( 'Redis server configurations', 'wp-redisearch' ) ),
      Field::make( 'text', 'wp_redisearch_server', __( 'Redis server', 'wp-redisearch' ) ),
      Field::make( 'text', 'wp_redisearch_port', __( 'Redis port', 'wp-redisearch' ) ),
      Field::make( 'text', 'wp_redisearch_index_name', __( 'Redisearch index name', 'wp-redisearch' ) ),
      Field::make( 'separator', 'wp_redisearch_redis_suggestion_separator', __( 'Auto suggestion | Live search', 'wp-redisearch' ) ),
      Field::make( 'checkbox', 'wp_redisearch_redis_suggestion', __( 'Enable auto suggestion | Live search', 'wp-redisearch' ) )
    );
    return $fields;
  }

  
  /**
  * Fields for indexable stuff options page.
  * @since    0.1.0
  * @param
  * @return object $fields
  */
  public static function wp_redisearch_custom_fields() {

    $post_types = get_post_types([
      'public' => true,
      'exclude_from_search' => false,
      'show_ui' => true,
      ]);
    $fields = array(
        Field::make( 'separator', 'wp_redisearch_redis_post_types_separator', 'Post types to index' ),
        Field::make( 'set', 'wp_redisearch_redis_post_types',  __( 'Post types', 'wp-redisearch' ) )->add_options( $post_types ),
        Field::make( 'separator', 'wp_redisearch_redis_fields', __( 'Custom fields', 'wp-redisearch' ) )
    );

    return $fields;
  }

  /**
  * Enqueue admin scripts.
  * @since    0.1.0
  * @param
  * @return
  */
  public function wp_redisearch_enqueue_scripts() {
    wp_enqueue_script( 'wp_redisearch_admin_js', WPRS_URL . 'lib/admin/js/admin.js', array( 'jquery' ), WPRS_VERSION, true );
    $localized_data = array(
			'ajaxUrl' 				=> admin_url( 'admin-ajax.php' )
		);
		wp_localize_script( 'wp_redisearch_admin_js', 'wpRds', $localized_data );
  }

  /**
  * action for "index it" ajax call to start indexing selected posts.
  * @since    0.1.0
  * @param
  * @return
  */
  public static function wp_redisearch_add_to_index() {
    $client = Setup::connect();
    $index = new Index( $client );
    $index->create()->add();
    print_r($index);
    wp_die();
  }

}