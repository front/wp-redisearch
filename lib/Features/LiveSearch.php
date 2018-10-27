<?php

namespace WPRedisearch\Features;

use WPRedisearch\Redisearch\Setup;
use WPRedisearch\Settings;
use WPRedisearch\Features;

class LiveSearch {

	/**
   * Redis client.
   * @since 0.2.0
	 * @var object
	 */
  public static $client;

	/**
   * Index name for this website.
   * @since 0.2.0
	 * @var string
	 */
  public static $index_name;

  /**
  * Initiate synonym terms to be added to the index
  * @since 0.2.0
  * @param
  * @return
  */
  public function __construct() {
    self::$client = Setup::connect();
    self::$index_name = Settings::indexName();
    Features::init()->register_feature( 'live-search', array(
      'title' => 'Live Search',
      'setup_cb' => array( $this, 'setup' ),
      'requirements_cb' => array( $this, 'requirements' ),
      'activation_cb' => array( $this, 'activated' ),
      'deactivation_cb' => array( $this, 'deactivated' ),
      'feature_desc_cb' => array( $this, 'feature_desc' ),
      'feature_options_cb' => array( $this, 'feature_options' ),
      'requires_reindex' => false,
      'deactivation_requires_reindex' => false,
    ) );
  }

	/**
	 * This method will run on each page load.
   * You can hook functions which must run always.
	 *
	 * @since 0.2.0
	 */
  public function requirements() {
    $status = new \stdClass();
    $status->code = 2;
    $status->message = array();
    $status->message[] = __( 'Re-indexing is highly recommended.', 'wp-redisearch' );
    
    return $status;
  }

	/**
	 * This method will run on each page load.
   * You can hook functions which must run always.
	 *
	 * @since 0.2.0
	 */
  public function setup () {
    add_action( 'wp_redisearch_after_post_indexed', array( __CLASS__, 'add_loop' ), 10, 3 );
    add_action( 'wp_redisearch_after_post_published', array( __CLASS__, 'add_post' ), 10, 3 );
    add_action( 'wp_redisearch_after_post_deleted', array( __CLASS__, 'delete' ), 10, 2 );
  }
  
	/**
	 * Fires after feature activation.
	 *
	 * @since 0.2.0
	 */
  public function activated () {
  }
  
	/**
	 * Fires after feature deactivation.
	 *
	 * @since 0.2.0
	 */
  public function deactivated () {
  }

	/**
	 * Feature description.
   * This will be added to feature setting box.
	 *
	 * @since 0.2.0
	 */
  public function feature_desc () {
    ?>
      <p><?php esc_html_e( 'Auto suggestion (live search or search as you type) is a redisearch built-in feature. It only adds the post title to suggestion group.', 'wp-redisearch' ) ?></p>
      <p><?php esc_html_e( 'Enabling auto suggestion doesn\'t necessary require re-indexing, but only those posts added after enabling this feature, will appear in live searches.', 'wp-redisearch' ) ?></p>
    <?php
  }

	/**
	 * Feature option/settings.
   * Here we're adding fields to plugin options page.
	 *
	 * @since 0.2.0
	 */
  public function feature_options () {
    // \SevenFields\Fields\Fields::add( 'header', null, __( 'Synonyms support', 'wp-redisearch' ) );
    // \SevenFields\Fields\Fields::add( 'textarea', 'wp_redisearch_synonyms_list', __( 'Synonym words list.', 'wp-redisearch' ), __('Add each group on a line and separate terms by comma. <br /><b>For example: </b><br />boy, child, baby<br />girl, child, baby<br />man, person, adult<br /><br />When these three groups are located inside the synonym data structure, it is possible to search for \'child\' and receive documents contains \'boy\', \'girl\', \'child\' and \'baby\'. <br />Keep in mined, only those posts indexed after adding synonyms list will be affected.', 'wp-redisearch' ) );
  }

  /**
  * Add auto suggestion.
  * This method called from the post loop
  *
  * @since 0.2.0
  * @param object $client            Created redis client instance
  * @param string $index_name        Index name
  * @param array $indexing_options   Posts extra options like language and fields
  * @return
  */
  public static function add_loop( $client, $index_name, $indexing_options ) {
    $post_title = \get_the_title();
    $post_permalink = \get_permalink();
    self::add( $index_name, $post_title, $post_permalink, 1 );
  }

  /**
  * Add auto suggestion.
  * This method called from the post edit.
  *
  * @since 0.2.0
  * @param string $index_name        Index name
  * @param object $post              The post object
  * @param array $indexing_options   Posts extra options like language and fields
  * @return
  */
  public static function add_post( $index_name, $post, $indexing_options ) {
    $post_title = $post->post_title;
    $post_permalink = $permalink = get_permalink( $post->ID );
    self::add( $index_name, $post_title, $post_permalink, 1 );
  }

  /**
  * Add auto suggestion to the index.
  *
  * @since 0.2.0
  * @param string $index_name        Index name
  * @param string $post_title        Post title
  * @param string $post_permalink    Post permalink
  * @param float $score              Score for the term
  * @return
  */
  public static function add( $index_name, $post_title, $post_permalink, $score ) {
    // Prepare command for adding post
    $command = array_merge( [$index_name . 'Sugg', $post_title , $score, 'PAYLOAD', $post_permalink] );
    self::$client->rawCommand('FT.SUGADD', $command);
  }

  /**
  * Remove synonym terms from the index
  * @since 0.2.0
  * @param string $index_name        Index name
  * @param object $post              The post object
  * @return
  */
  public static function delete( $index_name, $post ) {
    $post_title = $post->post_title;
    $command = array_merge( [$index_name . 'Sugg', $post_title] );
    self::$client->rawCommand('FT.SUGDEL', $command);
  }

}