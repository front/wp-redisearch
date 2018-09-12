<?php

namespace WPRedisearch;

use Carbon_Fields\Container;
use Carbon_Fields\Field;
use WPRedisearch\Settings;
use WPRedisearch\WPRedisearch;
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
    Container::make( 'theme_options', __( 'Indexing', 'wp-redisearch' ) )
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
    $default_args = Settings::query_args();
    $default_args['posts_per_page'] = -1;
    $args = apply_filters( 'wp_redisearch_posts_args', $default_args);

    $query = new \WP_Query( $args );
    $num_posts = $query->found_posts;

    $index_options = __( 'Indexing options:', 'wp-redisearch' );
    $index_btn = __( 'Index posts', 'wp-redisearch' );
    $num_docs = 0;
    if ( isset( WPRedisearch::$indexInfo ) && gettype( WPRedisearch::$indexInfo ) == 'array' ) {
      $num_docs_offset = array_search( 'num_docs', WPRedisearch::$indexInfo ) + 1;
      $num_docs = WPRedisearch::$indexInfo[$num_docs_offset];
    }
    $status_html = <<<"EOT"
      <p>This is RediSearch status page.</p>
      <p>Whith the current settings, there is <strong>${num_posts}</strong> to be indexed.</p>
      <p>Right now, ${num_docs} have been indexed.</p>
      <div class="indexing-options">
        <span>${index_options}</spam>
        <a class="dashicons indexing-btn start-indexing dashicons-update"></a>
        <a class="dashicons indexing-btn pause-indexing dashicons-controls-pause"></a>
        <a class="dashicons indexing-btn resume-indexing dashicons-controls-play"></a>
        <a class="dashicons indexing-btn cancel-indexing dashicons-no"></a>
      </div>
      <div id="indexingProgress">
        <div id="indexBar" data-num-posts="${num_posts}" data-num-docs="${num_docs}"></div>
        <span id="indexedStat">
        <span id="statNumDoc">${num_docs}</span>/<span id="statNumPosts">${num_posts}</span></span>
      </div>
      <style>
        .indexing-options{margin-top:20px;}
        .indexing-btn{cursor: pointer}
        #indexingProgress {position: relative;background:#eee;margin-top:30px;height:20px;width: 100%;}
        #indexBar {width: 1%;height: 100%;background-color: #0dbcac;transition: all linear 0.1s;}
        span#indexedStat {position: absolute;bottom: 0;right: 4px;line-height:20px;color: #000000;}
      </style>
EOT;
    $fields = array(
      Field::make( 'separator', 'wp_redisearch_redis_status_separator', __( 'Redisearch Status', 'wp-redisearch' ) ),
        Field::make( 'html', 'wp_redisearch_index_posts' )->set_html( $status_html )
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
      Field::make( 'separator', 'wp_redisearch_suggestion_separator', __( 'Auto suggestion | Live search', 'wp-redisearch' ) ),
      Field::make( 'checkbox', 'wp_redisearch_suggestion', __( 'Enable auto suggestion | Live search', 'wp-redisearch' ) ),
      Field::make( 'text', 'wp_redisearch_suggested_results', __( 'Results count for suggestion. (something around 5 to 10 is optimal)', 'wp-redisearch' ) )
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
        Field::make( 'separator', 'wp_redisearch_redis_general_separator', 'General indexing settings' ),
        Field::make( 'text', 'wp_redisearch_indexing_batches',  __( 'Posts will be indexed in baches of:', 'wp-redisearch' ) ),
        Field::make( 'separator', 'wp_redisearch_post_types_separator', 'Post types to index' ),
        Field::make( 'set', 'wp_redisearch_post_types',  __( 'Post types', 'wp-redisearch' ) )->add_options( $post_types ),
        Field::make( 'separator', 'wp_redisearch_fields', __( 'Custom fields', 'wp-redisearch' ) ),
        Field::make( 'separator', 'wp_redisearch_synonym', __( 'Synonyms support', 'wp-redisearch' ) ),
        Field::make( 'checkbox', 'wp_redisearch_synonym_enable', __( 'Enable synonym support', 'wp-redisearch' ) ),
        Field::make( 'textarea', 'wp_redisearch_synonyms_list', __( 'Synonym words list. Add each group on a line and separate terms by comma. Just keep in mined only those posts indexed after adding synonyms list will be affected.', 'wp-redisearch' ) ),
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
    $index = new Index( WPRedisearch::$client );
    $results = $index->create()->add();
    wp_send_json_success( $results );
  }

  /**
  * action for "index it" ajax call to start indexing selected posts.
  * @since    0.1.0
  * @param
  * @return
  */
  public function wp_redisearch_index_post_on_publish( $post_id, $post, $update ) {
    // If this is a revision, of it is auto save, don't do anything.
    if ( wp_is_post_revision( $post_id ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) )
      return;

    $index = new Index( WPRedisearch::$client );
    $index_name = Settings::indexName();

    // If post is not published or un-published, delete from index then, return.
    if ( $post->post_status != 'publish' ) {
      $index->deletePosts( $index_name, $post_id );
      return;
    }
    
    $title = $post->post_title;
    $content = wp_strip_all_tags( $post->post_content, true );
    $permalink = get_permalink( $post_id );
    $fields = array( 'postTitle', $title, 'postContent', $content, 'postId', $post_id, 'postLink', $permalink );
    
    $indexing_options['language'] = apply_filters( 'wp_redisearch_index_language', 'english', $post_id );
    $indexing_options['fields'] = array( 'postTitle', $title, 'postContent', $content, 'postId', $post_id, 'postLink', $permalink );
    $indexing_options['extra_params'] = array( 'REPLACE' );

    // Finally, add post to index
    $index->addPosts( $index_name, $post_id, $indexing_options );
  }

  /**
  * Drop existing index.
  * @since    0.1.0
  * @param
  * @return
  */
  public static function wp_redisearch_drop_index() {
    $index = new Index( WPRedisearch::$client );
    $results = $index->drop();
    wp_send_json_success( $results );
  }

}