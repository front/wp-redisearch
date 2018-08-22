<?php

namespace WPRedisearch\RediSearch;

use WPRedisearch\Settings;
use WPRedisearch\RediSearch\Setup;
use WPRedisearch\RedisRaw\PredisAdapter;

class Index {

	/**
	 * @param object $client
	 */
  public $client;

	/**
	 * @param object $index
	 */
  private $index;

  public function __construct( $client ) {
    $this->client = $client;
  }

  /**
  * Create connection to redis server.
  * @since    0.1.0
  * @param
  * @return
  */
  public function create() {
    $index_name = Settings::indexName();
    $title_schema = ['postTitle', 'TEXT', 'WEIGHT', 5.0, 'SORTABLE'];
    $body_schema = ['postContent', 'TEXT'];
    $post_id_schema = ['postID', 'NUMERIC'];
    $schema = array_merge( [$index_name, 'SCHEMA'], $title_schema , $body_schema, $post_id_schema );
    $this->index = $this->client->rawCommand('FT.CREATE', $schema);
    return $this;
  }

  /**
  * Prepare items (posts) to be indexed.
  * @since    0.1.0
  * @param
  * @return object $this
  */
  public function add() {
    $args = array(
      'posts_per_page'     => 10,
      'paged'              => 0
    );
    $posts = get_posts( $args );

    $index_name = Settings::indexName();
    $suggestion = Settings::suggestionEnabled();

    foreach ($posts as $post) {
      $title = $post->post_title;
      $permalink = $post->guid;
      $content = $post->post_content;
      $id = $post->ID;
      $fields = array('postTitle', $title, 'postContent', $content, 'postId', $id);
      $this->addPosts($index_name, $id, $fields);
      if ( $suggestion ) {
        $this->addSuggestion($index_name, $permalink, $title, 1);
      }
    }
  }

  /**
  * Add to index or in other term, index items.
  * @since    0.1.0
  * @param
  * @return object $this
  */
  public function addPosts($index_name, $id, $fields) {
    $command = array_merge( [$index_name, $id , 1, 'LANGUAGE', 'norwegian', 'FIELDS'], $fields );
    $index = $this->client->rawCommand('FT.ADD', $command);
    return $index;
  }

  /**
  * Add to suggestion list.
  * @since    0.1.0
  * @param
  * @return object $this
  */
  public function addSuggestion($index_name, $permalink, $title, $score) {
    $command = array_merge( [$index_name . 'Sugg', $title , $score, 'PAYLOAD', $permalink, 123] );
    $this->client->rawCommand('FT.SUGADD', $command);
  }


}