<?php

namespace WPRedisearch;

use WPRedisearch\Admin;
use WPRedisearch\RediSearch\Index;
use WPRedisearch\RediSearch\AddToIndex;
use WPRedisearch\RedisRaw\PredisAdapter;

/**
 * WPRedisearch Class.
 *
 * This is main class called to initiate all functionalities this plugin provides.
 *
 */

class WPRedisearch {

  public function __construct() {
    self::init();
  }

  public static function init() {
    new Admin;
    $index = new Index;
    $index->connect();
    $index->create()->add();

  }
}
