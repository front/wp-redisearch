<?php
/*
Plugin Name: WP RediSearch
Version: 1.0.0
Description: Replace Wordpress search by RediSearch.
Author: Foad Yousefi
Author URI: https://www.wp-redisearch.com
*/


require_once  __DIR__ . '/vendor/autoload.php';

new WPRedisearch\WPRedisearch;
