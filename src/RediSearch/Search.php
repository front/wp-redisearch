<?php

namespace WpRediSearch\RediSearch;

use WpRediSearch\Settings;
use WpRediSearch\RediSearch\Setup;
use WpRediSearch\RedisRaw\PredisAdapter;

class Search {

	/**
	 * @param object $client
	 */
  public $client;

  public function __construct( $client ) {
    $this->client = $client;
  }

  /**
  * Search in the index.
  * @since    0.1.0
  * @param object $query
  * @return
  */
  public function search( $wp_query ) {
    $index_name = Settings::indexName();
    $wprds_query = $wp_query->query_vars['s'];
    // Offset search results based on pagination
    $from = 0;
    $offset = $wp_query->query_vars['posts_per_page'];
    if ( isset( $wp_query->query_vars['paged'] ) && $wp_query->query_vars['paged'] > 1 ) {
      $from = $wp_query->query_vars['posts_per_page'] * ( $wp_query->query_vars['paged'] - 1 );
    }
    $search_results = $this->client->rawCommand('FT.SEARCH', [$index_name, $wprds_query, 'NOCONTENT', 'LIMIT', $from, $offset]);
    return $search_results;
  }
}