<?php

namespace WPRedisearch;

use WPRedisearch\Fields;

class Options {

	/**
	 * @param object $page_title
	 */
  public $page_title = 'WP Redisearch';

	/**
	 * @param object $options_slug
	 */
  public $options_slug = 'wp-redisearch';

	/**
	 * @param object $options_group
	 */
  public static $options_group = 'wp_redisearch_options';

	/**
	 * @param object $options_name
	 */
  public static $options_name = 'wp_redisearch';

  /**
   * Start things up.
   *
   * @since 0.1.0
   * @param
   * @return
   */
  public function __construct() {
    // We make sure to register the admin panel only on the back-end.
    if ( is_admin() ) {
      add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
      add_action( 'admin_init', array( $this, 'register_settings' ) );
    }
  }

  /**
   * Add Redisearch options menu page.
   *
   * @since 0.1.0
   * @param
   * @return
   */
  public static function add_admin_menu() {
    add_menu_page( 
      esc_html__( $this->page_title, 'wp-redisearch' ),
      esc_html__( $this->page_title, 'wp-redisearch' ),
      'manage_options',
      $this->options_slug,
      array( $this, 'create_admin_page' ),
      $icon_url = 'dashicons-search'
    );
  }

  /**
   * Register a setting and its sanitization callback.
   * We are only registering one setting so we can store all options in a single option as an array.
   *
   * @since 0.1.0
   * @param
   * @return
   */
  public static function register_settings() {
    register_setting(
      self::$options_group,
      self::$options_name,
      array( $this, 'sanitize' )
    );
  }

  /**
   * Sanitization callback.
   * This function sanitize and stores options in database
   *
   * @since 0.1.0
   * @param array $options
   * @return array $options
   */
  public static function sanitize( $options ) {
    // If there is options, lets sanitize them.
    if ( $options ) {
      $new_options = array();

      foreach ($options as $key => $value) {
        $type = explode('__', $key);

        switch ($type[0]) {
          case 'text':
            if ( isset( $value ) ){
              $new_options[ $type[1] ] = sanitize_text_field( $value );
            }
            break;
          case 'textarea':
            if ( isset( $value ) ){
              $new_options[ $type[1] ] = sanitize_textarea_field( $value );
            }
            break;
          case 'multiselect':
            if ( isset( $value ) ){
              $new_options[ $type[1] ] = $value;
            }
            break;
          case 'checkbox':
            $new_options[ $type[1] ] = 'on';
            break;
        }
      }
    }
    return $new_options;
  }

  /**
   * Settings page output.
   *
   * @since 0.1.0
   * @param
   * @return
   */
  public static function create_admin_page() {
    ?>

    <div class="wrap">
      <h1><?php esc_html_e( $this->page_title, 'wp-redisearch' ); ?></h1>
      <form method="post" action="options.php">
        <div class="wprds-form-wraper">
          <div class="fields">
          <?php
            $post_types = get_post_types([
              'public'              => true,
              'exclude_from_search' => false,
              'show_ui'             => true,
            ]);
            $post_types = array_values( $post_types );
            
            settings_fields( self::$options_group );
            Fields::make( self::$options_group, self::$options_name );
            Fields::add('header', null, 'Redis server configurations');
            Fields::add('text', 'example_text_field', 'Redis server', 'And this is field description.');
            Fields::add('textarea', 'example_textarea_field', 'Redis port', 'And this is field description.');
            Fields::add('checkbox', 'example_checkbox', 'This is checkbox', 'Checkbox desc.');
            Fields::add('multiselect', 'example_multiselect', 'Indexable post types', 'Post types to be indexed and also searched through.', $post_types);
            ?>
          </div>
          <div class="form-actions">
            <?php submit_button(); ?>
          </div>
        </div>
      </form>
    </div>
  <?php }
}
