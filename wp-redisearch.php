<?php
/*
Plugin Name: RediSearch
Version: 0.3.2
Description: Replace Wordpress search by RediSearch.
Author: Foad Yousefi
Author URI: https://github.com/7kmCo/wp-redisearch
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}
// Plugin path.
if ( ! defined( 'WPRS_PATH' ) ) {
	define( 'WPRS_PATH', plugin_dir_path( __FILE__ ) );
}
// Plugin URL.
if ( ! defined( 'WPRS_URL' ) ) {
	define( 'WPRS_URL', plugin_dir_url( __FILE__ ) );
}
// Plugin base.
if ( ! defined( 'WPRS_BASE' ) ) {
	define( 'WPRS_BASE', plugin_basename( __FILE__ ) );
}
// Plugin name.
if ( ! defined( 'WPRS_NAME' ) ) {
	define( 'WPRS_NAME', 'wp-redisearch' );
}
// Plugin version .
if ( ! defined( 'WPRS_VERSION' ) ) {
	define( 'WPRS_VERSION', '0.3.2' );
}

/**
 * Register WP-CLI commands.
 */
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once( 'bin/wp-cli.php' );
}

require_once  __DIR__ . '/vendor/autoload.php';

new WpRediSearch\WpRediSearch;
