<?php

namespace WpRediSearch\Features;

use FKRediSearch\Index;
use WpRediSearch\RediSearch\Client;
use WpRediSearch\Settings;
use WpRediSearch\Features;
use Asika\Pdf2text;
use WpRediSearch\Utils\MsOfficeParser;
use Vaites\ApacheTika\Client as TikaClient;

class Document {

  /**
   * The Index object
   * @since 1.0.0
   * @var string
   */
  public $index;

  /**
  * Initiate document terms to be added to the index
  * @since 0.2.2
  * @param
  * @return
  */
  public function __construct() {
    $client = (new Client())->return();
    $this->index = new Index($client);
    $this->index->setIndexName( Settings::indexName() );

    Features::init()->register_feature( 'document', array(
      'title' => 'Document',
      'setup_cb' => array( $this, 'setup' ),
      'activation_cb' => array( $this, 'activated' ),
      'deactivation_cb' => array( $this, 'deactivated' ),
      'feature_desc_cb' => array( $this, 'feature_desc' ),
      'feature_options_cb' => array( $this, 'feature_options' ),
      'requires_reindex' => true,
      'deactivation_requires_reindex' => true,
    ) );
  }

	/**
	 * This method will run on each page load.
   * You can hook functions which must run always.
	 *
	 * @since 0.2.2
	 */
  public function setup () {
    add_filter( 'wp_redisearch_indexable_post_types', array( __CLASS__, 'indexable_post_type' ) );
    add_filter( 'wp_redisearch_indexable_post_status', array( __CLASS__, 'indexable_post_status' ) );
    add_filter( 'wp_redisearch_indexable_meta_keys', array( __CLASS__, 'indexable_meta_keys' ) );
    add_filter( 'wp_redisearch_prepared_post_args', array( __CLASS__, 'index_document' ), 10, 2 );
    add_filter( 'wp_redisearch_before_admin_wp_query', array( __CLASS__, 'before_wp_query' ) );
    add_filter( 'wp_redisearch_before_index_wp_query', array( __CLASS__, 'before_wp_query' ) );
    add_filter( 'wp_redisearch_after_admin_wp_query', array( __CLASS__, 'after_wp_query' ), 10, 2 );
    add_filter( 'wp_redisearch_after_index_wp_query', array( __CLASS__, 'after_wp_query' ), 10, 2 );
  }
  
	/**
	 * Add attachment to indexable post types list.
	 *
   * @param array $post_types     Array of indexable post types
   * @since 0.2.2
   * @return array $post_types
	 */
  public static function indexable_post_type ( $post_types ) {
    return array_merge( $post_types, array( 'attachment' ) );
  }
  
	/**
	 * Add inherit to indexable post types list.
	 *
   * @param array $post_status     Array of indexable post status
   * @since 0.2.2
   * @return array $post_status
	 */
  public static function indexable_post_status ( $post_status ) {
    return array_merge( $post_status, array( 'inherit' ) );
  }

  /**
   * Add document field to the index
   *
   * @param array $meta Existing meta keys
   *
   * @return  array
   * @since 0.2.2
   */
  public static function indexable_meta_keys( array $meta ) {
    $indexable_keys = array( 'document' );

    return array_merge( $meta, $indexable_keys );
  }
  
	/**
	 * add allowed mime types to WP_Query
	 *
	 * @since 0.2.2
	 */
  public static function before_wp_query ( $args ) {
    add_filter( 'posts_where', array( __CLASS__, 'add_mime_types_to_query' ) );
    return $args;
  }
  
	/**
	 * Remove posts_where filter after WP_Query runs
	 *
	 * @since 0.2.2
	 */
  public static function after_wp_query ( $query, $args) {
    remove_filter( 'posts_where', array( __CLASS__, 'add_mime_types_to_query' ) );
    return $query;
  }
  
	/**
	 * Add allowed mime types to the main WP_Query
   * We do this because we only want to query posts with allowed mime types, or no mime type (posts, pages, products and ...)
	 *
	 * @since 0.2.2
   * @param string $where       mysql query string
   * @return string $where      Altered mysql query
	 */
  public static function add_mime_types_to_query ( $where ) {
    $allowed_mime_types = self::allowed_mime_types();

    $where_mime_types = '';
    foreach ( $allowed_mime_types as $key => $value ) {
      $where_mime_types .= '"' . $value .  '", ';
    }

    $where .= ' AND post_mime_type IN( ' . $where_mime_types . '"") ';
    return $where;
  }
  
  
	/**
	 * Fires after feature activation.
	 *
	 * @since 0.2.2
	 */
  public function activated () {
  }
  
	/**
	 * Fires after feature deactivation.
	 *
	 * @since 0.2.2
	 */
  public function deactivated () {
  }

	/**
	 * Feature description.
   * This will be added to feature setting box.
	 *
	 * @since 0.2.2
	 */
  public function feature_desc () {
    ?>
      <p><?php esc_html_e( 'Indexes text inside popular document file types, and adds those files types to search results. Supported file types are: pdf, ppt, pptx, doc, docx, xls, xlsx.', 'wp-redisearch' ) ?></p>
      <p><?php 
      echo sprintf( __( 'For faster file parsing and cleaner indexes, highly recommend to install %s on the server.', 'wp-redisearch' ), '<a href="https://tika.apache.org/index.html" target="_blank">Apache Tika</a>' )
      ?></p>
      
    <?php
  }

	/**
	 * Feature option/settings.
   * Here we're adding fields to plugin options page.
	 *
	 * @since 0.2.2
	 */
  public function feature_options () {
    \SevenFields\Fields\Fields::add( 'header', null, __( 'Apache Tika', 'wp-redisearch' ) );
    \SevenFields\Fields\Fields::add( 'text', 'wp_redisearch_tika_host',  __( 'Apache Tika host', 'wp-redisearch' ), __( 'Default value is 127.0.0.1', 'wp-redisearch') );
    \SevenFields\Fields\Fields::add( 'text', 'wp_redisearch_tika_port',  __( 'Apache Tika port number', 'wp-redisearch' ), __( 'The default port number usually is 9998', 'wp-redisearch' ) );
  }

  /**
  * Read document file content and add it to the index
  * @since 0.2.2
  * @param array $post_args       Prepared post args to be added to the index
  * @param object $post           Post object
  * @return
  */
  public static function index_document($post_args, $post) {
    if ( $post->post_type !== 'attachment' ) {
      return $post_args;
    }

    global $wp_filesystem;

    require_once( ABSPATH . 'wp-admin/includes/file.php' );

    if ( ! WP_Filesystem() ) {
      return $post_args;
    }

    $allowed_mime_types = self::allowed_mime_types();
    
    if ( in_array( $post->post_mime_type, $allowed_mime_types ) ) {
      $file_name = get_attached_file( $post->ID );

      if ( $wp_filesystem->exists( $file_name, false, 'f' ) ) {
        $file_content = '';
        /**
         * Parse file contents base on their file mime_type
         * 
         * First, we will try to use Apache Tika rest server, a toolkit to extract file content from virtually any file types.
         * Apache Tika is faster to read files and output file is way cleaner than pure php method.
         */
        try {
          $tika_host = Settings::get( 'wp_redisearch_tika_host', '127.0.0.1' );
          $tika_port = Settings::get( 'wp_redisearch_tika_port', 9998 );
          $tika_client = TikaClient::make( $tika_host, $tika_port );
          $file_content = $tika_client->getText( $file_name );
          $file_content = str_replace(array("\r", "\\n"), '', $file_content);
        } catch ( \Exception $e ) {
        }
        // If Tika rest server is down or for some reason was not able to do the tricks, we try php methods.
        if ( !$file_content ) {
          // If pdf attachment is pdf
          if ( $post->post_mime_type == 'application/pdf'  ) {
            $pdf2text = new \Asika\Pdf2text;
            $file_content = $pdf2text->decode( $file_name );
          // Or if attachment is word, excel or powepoint
          } elseif ( in_array( $post->post_mime_type, array( 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' ) ) ) {
            $file_content = MsOfficeParser::getText( $file_name );
          // Or if its another allowed mime_type
          } else {
            $file_content = $wp_filesystem->get_contents( $file_name );
          }
        }
        $post_args = array_merge( $post_args, array( 'document' => $file_content ) );
      }
    }

    return $post_args;
    
  }

  /**
  * Remove synonym terms from the index
  * @since 0.2.2
  * @param
  * @return
  */
  public static function delete() {
  }

  /**
   * Get allowed mime types
   *
   * @since 0.2.2
   * @return array
   */
  public static function allowed_mime_types() {
    $mine_types = array(
      'pdf'  => 'application/pdf',
      'ppt'  => 'application/vnd.ms-powerpoint',
      'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
      'xls'  => 'application/vnd.ms-excel',
      'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
      'doc'  => 'application/msword',
      'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    );

    return apply_filters( 'wp_redisearch_allowed_documents_mime_types', $mine_types);
  }
}