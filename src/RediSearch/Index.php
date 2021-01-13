<?php

namespace WpRediSearch\RediSearch;

use WpRediSearch\WpRediSearch;
use WpRediSearch\Settings;
use WpRediSearch\Features;
use WpRediSearch\RediSearch\Setup;
use WpRediSearch\RedisRaw\PredisAdapter;
use FKRediSearch\Index as RedisearchIndex;

class Index {

	/**
	 * @param object $client
	 */
  public $client;

	/**
	 * @param object $index
	 */
  public $index;

  public function __construct( $client ) {
    $this->client = $client;

    $index = new RedisearchIndex($this->client);
    // Set index name.
    $index->setIndexName(Settings::indexName());

    $index->on('HASH');
    $this->index = $index;
  }

  /**
  * Create connection to redis server.
  * @since    0.1.0
  * @param
  * @return
  */
  public function create() {
    // First of all, we reset saved index_meta from options
    $num_docs = 0;
    if ( isset( WpRediSearch::$indexInfo ) && gettype( WpRediSearch::$indexInfo ) == 'array' ) {
      $num_docs_offset = array_search( 'num_docs', WpRediSearch::$indexInfo ) + 1;
      $num_docs = WpRediSearch::$indexInfo[$num_docs_offset];
    }
    if ( $num_docs == 0 ) {
      delete_option( 'wp_redisearch_index_meta' );
    }

    $indexName = Settings::indexName();

    $prefixes = array();

    $postTypes = Settings::get( 'wp_redisearch_post_types' );

    if ( isset( $postTypes ) && !empty( $postTypes ) ) {
      $postTypes = array_keys( $postTypes );
    } elseif ( !isset( $postTypes ) || empty( $postTypes ) ) {
      $postTypes = array( 'post' );
    }

    $postTypes = apply_filters( 'wp_redisearch_indexable_post_types', $postTypes );

    foreach ($postTypes as $postType ) {
      $prefixes[] = $indexName . ':' . $postType;
    }
    $this->index->setPrefix($prefixes);
    // Setting a field name for score so we don't need to parse index info everytime
    $this->index->setScoreField('documentScore');
    // Delete the index.
    $this->index->drop();

    $indexableFields = array(
      'post_title'      => array(
        'type'            => 'TEXT',
        'weight'          => 5.0,
        'sortable'        => TRUE
      ),
      'post_content'    => array(
        'type'            => 'TEXT'
      ),
      'post_content_filtered'  => array(
        'type'            => 'TEXT'
      ),
      'post_excerpt'    => array(
        'type'            => 'TEXT'
      ),
      'post_type'       => array(
        'type'            =>'TEXT'
      ),
      'post_author'     => array(
        'type'            => 'TEXT'
      ),
      'post_id'         => array(
        'type'            => 'NUMERIC',
        'sortable'        => TRUE
      ),
      'menu_order'      => array(
        'type'            => 'NUMERIC'
      ),
      'permalink'       => array(
        'type'            => 'TEXT'
      ),
      'post_date'       => array(
        'type'            => 'NUMERIC',
        'sortable'        => TRUE
      )
    );

		/**
		 * Filter index-able post meta
		 * Allows for specifying public or private meta keys that may be indexed.
		 * @since 0.2.0
		 * @param array Array 
		 */
    $indexableMetaKeys = apply_filters( 'wp_redisearch_indexable_meta_keys', array() );

    $metaSchema = array();

    if ( isset( $indexableMetaKeys ) && !empty( $indexableMetaKeys ) ) {
      foreach ($indexableMetaKeys as $meta) {
        $metaSchema[$meta] = array(
          'type'    => 'TEXT'
        );
      }
    }
    /**
     * Filter index-able post meta schema
     * Allows for manipulating schema of public or private meta keys.
     * @since 0.2.0
     * @param array $metaSchema            Array of index-able meta key schemas.
     * @param array $indexableMetaKeys    Array of index-able meta keys.
		 */
    $metaSchema = apply_filters( 'wp_redisearch_indexable_meta_schema', $metaSchema, $indexableMetaKeys );

    $indexableTerms = array_keys( Settings::get( 'wp_redisearch_indexable_terms', array() ) );
    $termsSchema = array();
    if ( isset( $indexableTerms ) && !empty( $indexableTerms ) ) {
      foreach ($indexableTerms as $term) {
        $termsSchema[$term] = array(
          'type'    => 'TAG'
        );
      }
    }

    $indexableFields = array_merge( $indexableFields, $metaSchema, $termsSchema );

    /**
     * Stop words support.
     * If disabled from settings page, then we will add no stop words.
     * @since 0.2.5
     */
    $stop_words_disabled = Settings::get( 'wp_redisearch_disable_stop_words', false );
    if ( $stop_words_disabled ) {
      $this->index->noStopWords();
    } else {
      $stopWords = Settings::get( 'wp_redisearch_stop_words', null );
      if ( isset( $stopWords ) && $stopWords != null ) {
        $stopWordsArray = explode( ',', $stopWords);
        $stopWordsArray = array_map( 'trim', $stopWordsArray );
        if ( count( $stopWordsArray ) !== 0 ) {
          $this->index->setStopWords( $stopWordsArray );
        }
      }
    }

    // Loop through the fields.
    foreach ( $indexableFields as $name => $field ) {
      $type = $field['type'];
      if (!empty($type)) {
        switch ($type) {
          case 'NUMERIC':
            $this->index->addNumericField( $name, $field['sortable'] ?? FALSE );
            break;

          case 'TAG':
            $this->index->addTagField( $name );
            break;

          case 'GEO':
            $this->index->addGeoField( $name );
            break;

          default:
            $weight = '1.0';
            if ( isset( $field['weight'] ) ) {
              $weight = $field['weight'];
            }
            $this->index->addTextField( $name, $weight, $field['sortable'] ?? FALSE );
        }
      }
    }

    // Save/Create the Index.
    $this->index->create();

    /**
     * Action wp_redisearch_after_index_created fires after index created.
     * Some features need to do something after activation. Some of them trigger re-indexing. 
     * But after they do what they suppose to do with the index, the index will be deleted to re-index the site.
     * So those features can use this filter instead.
     * 
     * @since 0.2.0
     * @param array $client       Created redis client instance
		 */
    do_action( 'wp_redisearch_after_index_created', $this->client);

    return $this;
  }

  /**
  * Prepare items (posts) to be indexed.
  * @since    0.1.0
  * @param
  * @return object $this
  */
  public function add() {
    $index_meta = get_option( 'wp_redisearch_index_meta' );
    if ( empty( $index_meta ) ) {
      $index_meta['offset'] = 0;
    }
    $posts_per_page = apply_filters( 'wp_redisearch_posts_per_page', Settings::get( 'wp_redisearch_indexing_batches', 20 ) );

    $default_args = Settings::query_args();
    $default_args['posts_per_page'] = $posts_per_page;
    $default_args['offset'] = $index_meta['offset'];

    $args = apply_filters( 'wp_redisearch_posts_args', $default_args);

    /**
     * filter wp_redisearch_before_index_wp_query
     * Fires before wp_query. This is useful if you want for some reasons, manipulate WP_Query
     * 
     * @since 0.2.2
     * @param array $args             Array of arguments passed to WP_Query
     * @return array $args            Array of manipulated arguments
		 */
    $args = apply_filters( 'wp_redisearch_before_index_wp_query', $args );

    $query = new \WP_Query( $args );

    /**
     * filter wp_redisearch_after_index_wp_query
     * Fires after wp_query. This is useful if you want to manipulate results of WP_Query
     * 
     * @since 0.2.2
     * @param array $args            Array of arguments passed to WP_Query
     * @param object $query          Result object of WP_Query
		 */
    $query = apply_filters( 'wp_redisearch_after_index_wp_query', $query, $args );

    $index_meta['found_posts'] = $query->found_posts;

    if ( $index_meta['offset'] >= $index_meta['found_posts'] ) {
      $index_meta['offset'] = $index_meta['found_posts'];
    }
    
    if ( $query->have_posts() ) {
      $indexName = Settings::indexName();
      
      while ( $query->have_posts() ) {
        $query->the_post();
        $indexingOptions = array();

        $id = get_the_id();
        // Post language. This could be useful to do some stop word, stemming and etc.
        $indexingOptions['language'] = apply_filters( 'wp_redisearch_index_language', 'english', $id );
        $indexingOptions['fields'] = $this->preparePost( get_the_id() );

        $this->addPosts( $id, $indexingOptions );

        /**
         * Action wp_redisearch_after_post_indexed fires after post added to the index.
         * Since this action called from within post loop, all Wordpress functions for post are available in the calback.
         * Example:
         * To get post title, you can simply call 'get_the_title()' function
         * 
         * @since 0.2.0
         * @param array $client             Created redis client instance
         * @param array $indexName         Index name
         * @param array $indexingOptions   Posts extra options like language and fields
         */
        do_action( 'wp_redisearch_after_post_indexed', $this->client, $indexName, $indexingOptions );
      }
      $index_meta['offset'] = absint( $index_meta['offset'] + $posts_per_page );
      update_option( 'wp_redisearch_index_meta', $index_meta );
    }
    return $index_meta;
  }

  /**
	 * Prepare a post for indexing.
	 *
	 * @param object $post_id
	 * @since 0.1.0
	 * @return bool|array
	 */
	public function preparePost( $post_id ) {
    $post = get_post( $post_id );
		$user = get_userdata( $post->post_author );

		if ( $user instanceof WP_User ) {
			$user_data = $user->display_name;
		} else {
			$user_data = '';
		}

		$post_date = $post->post_date;
		// If date is invalid, set it to null
		if ( ! strtotime( $post_date ) || $post_date === "0000-00-00 00:00:00" ) {
			$post_date = null;
		}
    
    
    $post_categories = get_the_category( $post->ID );

		$post_args = array(
		  'post_id'           => $post->ID,
      'post_author'       => $user_data,
      'post_date'         => strtotime( $post_date ),
      'post_title'        => $post->post_title,
      'post_excerpt'      => $post->post_excerpt,
      'post_content_filtered' => wp_strip_all_tags( apply_filters( 'the_content', $post->post_content ), true ),
			'post_content'      => wp_strip_all_tags( $post->post_content, true ),
			'post_type'         => $post->post_type,
			'permalink'         => get_permalink( $post->ID ),
			'menu_order'        => absint( $post->menu_order )
    );
    
    $post_terms = apply_filters( 'wp_redisearch_prepared_terms', $this->prepare_terms( $post ), $post );

    $prepared_meta = $this->prepare_meta( $post->ID );
    
    $post_args = array_merge( $post_args, $post_terms, $prepared_meta );

		$post_args = apply_filters( 'wp_redisearch_prepared_post_args', $post_args, $post );

		return $post_args;
	}

  /**
  * Prepare post terms.
  * @since    0.1.0
  * @param integer $post
  * @return string
  */
  private function prepare_terms( $post ) {
    $indexableTerms = Settings::get( 'wp_redisearch_indexable_terms' );
    $indexableTerms = isset( $indexableTerms ) ? array_keys( $indexableTerms ) : array();

    /**
     * Filter wp_redisearch_indexable_temrs to manipulate indexable terms list
     * 
     * @since 0.2.1
     * @param array $indexableTerms        Default terms list
     * @param array $post                   The post object
     * @return array $indexableTerms       Modified taxobomy terms list
     */
		$indexableTerms = apply_filters( 'wp_redisearch_indexable_temrs', $indexableTerms, $post );

		if ( empty( $indexableTerms ) ) {
			return array();
		}

		$terms = array();
		foreach ( $indexableTerms as $taxonomy ) {

			$post_terms = get_the_terms( $post->ID, $taxonomy );

			if ( ! $post_terms || is_wp_error( $post_terms ) ) {
				continue;
			}

			$terms_dic = [];

			foreach ( $post_terms as $term ) {
        $terms_dic[] = $term->name;
      }
      $terms_dic = implode( ',', $terms_dic );
			$terms[$taxonomy] = ltrim( $terms_dic );
		}

		return $terms;
	}

  /**
  * Prepare post meta.
  * @since    0.2.1
  * @param integer $post_id
  * @return array $prepared_meta
  */
  public function prepare_meta( $post_id ) {
    $post_meta = (array) get_post_meta( $post_id );
    
		if ( empty( $post_meta ) ) {
      return array();
    }
    
    $prepared_meta = array();
    
		/**
		 * Filter index-able post meta
		 * Allows for specifying public or private meta keys that may be indexed.
		 * @since 0.2.0
		 * @param array Array 
		 */
    $indexableMetaKeys = apply_filters( 'wp_redisearch_indexable_meta_keys', array() );

		foreach( $post_meta as $key => $value ) {
      if ( in_array( $key, $indexableMetaKeys ) ) {
        $extracted_value = maybe_unserialize( $value[0] );
        $prepared_meta[$key] = is_array( $extracted_value ) ? json_encode( maybe_unserialize( $value[0] ) ) : $extracted_value;
			}
		}

		return $prepared_meta;
  }

  /**
   * Add to index or in other term, index items.
   *
   * @param $id
   * @param array $indexingOptions
   *
   * @return object $index
   * @since    0.1.0
   */
  public function addPosts( $id, array $indexingOptions ) {

    $document = new \FKRediSearch\Document;
    $document->setLanguage( $indexingOptions['language'] );

    $documentScore = $indexingOptions['score'] ?? 1;
    $document->setScore( $documentScore );
    $document->setId(  $this->index->getIndexName() . ':' . get_post_type( $id ) . ':' . $id );

    $document->setFields( $indexingOptions['fields'] );

    $this->index->add( $document );

    return $this;
  }

  /**
  * Delete post from index.
  * @since    0.1.0
  * @param
  * @return object $this
  */
  public function deletePost($id) {
    $command = array( $this->index->getIndexName(), $id , 'DD' );
    $this->client->rawCommand('FT.DEL', $command);
    return $this;
  }

  /**
  * Write entire redisearch index to the disk to persist it.
  * @since    0.1.0
  * @param
  * @return
  */
  public function writeToDisk() {
    return $this->client->rawCommand('SAVE', []);
  }

}