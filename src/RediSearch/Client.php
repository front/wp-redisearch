<?php

namespace WpRediSearch\RediSearch;

use FKRediSearch\Setup;
use WpRediSearch\Settings;

class Client {

  public $client;
  /**
   * Create connection to redis server.
   * @return object $client
   * @since    1.0.0
   */
  public function __construct() {
    $this->client = Setup::connect(
      Settings::RedisServer(),
      Settings::RedisPort(),
      Settings::RedisPassword(),
      0
    );
  }

  public function return() {
    return $this->client;
  }
  
}