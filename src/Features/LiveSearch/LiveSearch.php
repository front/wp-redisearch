<?php

namespace WpRediSearch\Features;

use FKRediSearch\Query\Query;
use WpRediSearch\RediSearch\Client;
use WpRediSearch\Settings;
use WpRediSearch\Features;

class LiveSearch {

  /**
   * The query to get results
   * @since 1.0.0
   * @var string
   */
  public $query;

	/**
   * Set this if suggestion is not added.
	 * @param object $liveSearchException
	 */
  public static $liveSearchException = false;

  /**
  * Initiate synonym terms to be added to the index
  * @since 0.2.0
  * @param
  * @return
  */
  public function __construct() {
    $client = (new Client())->return();
    $this->query = new Query( $client, Settings::indexName() );

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
    add_action('wp_ajax_wp_redisearch_get_suggestion', array( $this, 'get_suggestion' ) );
    add_action('wp_ajax_nopriv_wp_redisearch_get_suggestion', array( $this, 'get_suggestion' ) );
    add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_script' ) );
  }
  
	/**
	 * Enqueue script and styles for live search
	 *
	 * @since 0.2.0
	 */
  public function enqueue_script () {
    wp_enqueue_script( 'wp_redisearch_live_search', WPRS_URL . 'src/Features/LiveSearch/assets/js/live-search.min.js', array( 'jquery' ), WPRS_VERSION, true );
    $localized_data = array(
			'ajaxUrl' 				    => admin_url( 'admin-ajax.php' )
		);
		wp_localize_script( 'wp_redisearch_live_search', 'wpRds', $localized_data );
    wp_enqueue_style( 'wp_redisearch_public_css', WPRS_URL . 'src/Features/LiveSearch/assets/css/live-search.css', array(), WPRS_VERSION );
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
    \SevenFields\Fields\Fields::add( 'header', null, __( 'Auto suggestion | Live search', 'wp-redisearch' ) );
    \SevenFields\Fields\Fields::add( 'text', 'wp_redisearch_suggested_results', __( 'Results count for suggestion.', 'wp-redisearch' ), __( '(something around 5 to 10 is optimal and 10 is maximum allowed number by redisearch.)', 'wp-redisearch' ) );
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
    // First, lets make sure it it does not exists
    try {
      $command = array_merge( [$index_name . 'Sugg', $post_title] );
      self::$client->rawCommand('FT.SUGDEL', $command);
    } catch (\Exception $e) {
    }
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

  /**
  * Ajax callback for getting suggestion results
  * @since 0.2.0
  * @param 
  * @return string $suggestion_results
  */
  public function get_suggestion() {
    $noResults = Settings::get( 'wp_redisearch_suggested_results', 10 );
    $term = $_POST['term'];
    $suggestion_results = $this->query
      ->limit(0, $noResults )
      ->search( $term . '*' );
    wp_send_json( $suggestion_results->getDocuments() );
  }

}