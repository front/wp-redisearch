<?php

namespace WPRedisearch\RediSearch;

use WPRedisearch\Settings;
use WPRedisearch\RedisRaw\PredisAdapter;
use WPRedisearch\RediSearch\Setup;

class Index {

	/**
	 * @param object $client
	 */
  private $client;

	/**
	 * @param object $index
	 */
  private $index;

  public function __construct() {
  }

  /**
  * Create connection to redis server.
  * @since    0.1.0
  * @param
  * @return
  */
  public function connect() {
    $redis_server = Settings::RedisServer();
    $redis_port = Settings::RedisPort();
    // Connect to server
    $this->client = ( new PredisAdapter() )->connect( $redis_server, $redis_port );
    return $this;
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

  public function add() {
    $args = array(
      'posts_per_page'     => 10,
      'paged'              => 0
    );
    $posts = get_posts( $args );

    $index_name = Settings::indexName();
    foreach ($posts as $post) {
      $title = $post->post_title;
      $content = $post->post_content;
      $id = $post->ID;
      $fields = array('postTitle', $title, 'postContent', $content, 'postId', $id);
      $command = array_merge( [$index_name, $id , 1, 'LANGUAGE', 'norwegian', 'FIELDS'], $fields );
      $index = $this->client->rawCommand('FT.ADD', $command);
    }
  }
}