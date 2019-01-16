<?php

namespace WPRedisearch\RediSearch;

use WPRedisearch\Settings;
use WPRedisearch\RedisRaw\PredisAdapter;

class Setup {

	/**
	 * @param object $index
	 */
  private static $index;

  public function __construct() {
	}
	
  /**
  * Create connection to redis server.
  * @since    0.1.0
  * @param
  * @return
  */
  public static function connect() {
    $redis_server = Settings::RedisServer();
    $redis_port = Settings::RedisPort();
    $redis_password = Settings::RedisPassword();
    // Connect to server
    $client = ( new PredisAdapter() )->connect( $redis_server, $redis_port, 0, $redis_password );
    return $client;
  }
  
}