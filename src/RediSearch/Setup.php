<?php

namespace WpRediSearch\RediSearch;

use WpRediSearch\Settings;
use WpRediSearch\RedisRaw\PredisAdapter;

class Setup {

  public function __construct() {
	}

  /**
   * Create connection to redis server.
   * @return PredisAdapter|\WpRediSearch\RedisRaw\RedisRawClientInterface
   * @since    0.1.0
   */
  public static function connect() {
    $redis_server = Settings::RedisServer();
    $redis_port = Settings::RedisPort();
    $redis_password = Settings::RedisPassword();
    // Connect to server
    return ( new PredisAdapter() )->connect( $redis_server, $redis_port, 0, $redis_password );
  }
  
}