<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

use WPRedisearch\Settings;
use WPRedisearch\WPRedisearch;
use WPRedisearch\Redisearch\Setup;
use WPRedisearch\RediSearch\Index;

// Add commant to wp-cli
WP_CLI::add_command( 'redisearch', 'Redisearch_CLI' );

/**
 * CLI Commands for RediSearch
 *
 */
class Redisearch_CLI extends WP_CLI_Command {

	/**
	 * Holds time until transient expires
	 *
	 * @since 0.1.2
	 */
	private $transient_expiration = 900; // 15 min

	/**
	 * Holds temporary wp_actions when indexing with pagination
	 *
	 * @since 0.1.2
	 */
	private $temporary_wp_actions = array();

  /**
	 * Get status of index.
	 *
	 * @since 0.1.2
	 */
	public function info() {
		$this->_connect_check();
    
    try {
      $client = Setup::connect();
      $info = $client->rawCommand('FT.INFO', ['myIndex']);

			WP_CLI::log( WP_CLI::colorize( '%G' . __( '===== Info =====', 'wp-redisearch' ) . '%N' ) );
			foreach ($info as $key => $value) {
				if ( gettype( $value ) == 'string' || gettype( $value ) == 'object' ) {
					WP_CLI::line( $value );
				} elseif ( gettype( $value ) == 'array' ) {
					foreach ($value as $key => $sub_value) {
						if ( gettype( $sub_value ) == 'string' || gettype( $sub_value ) == 'object' ) {
							WP_CLI::line( $sub_value );
						} elseif ( gettype( $sub_value ) == 'array' ) {
							$child_value_print = '';
							foreach ($sub_value as $key => $child_value) {
								$child_value_print .= '  ' . $child_value;
							}
							WP_CLI::line( '   -' . $child_value_print );
						}
					}
				}
			}
    } catch (\Exception $e) {
      if ( isset( $e ) )  {
        WP_CLI::error( implode( "\n", $e ) );
      }
    }
	}

	/**
	 * Creates the index.
	 *
	 * @subcommand create-index
	 * @since      0.9
	 *
	 * @param array $args
	 * @param array $assoc_args
	 */
	public function create_index( $args, $assoc_args ) {
		$this->_connect_check();

		// First of all, deletes index
		$this->drop_index( $args, $assoc_args );

		$index = new Index( WPRedisearch::$client );
		$result = $index->create();
		
		if ( $result ) {
			WP_CLI::success( __( 'Index created', 'wp-redisearch' ) );
		} else {
			WP_CLI::error( __( 'Index creating failed', 'wp-redisearch' ) );
		}
	}

	/**
	 * Drop current index. 
	 * Warning! This will remove your existing index for the entire site.
	 *
	 * @subcommand drop-index
	 * @since      0.9
	 *
	 * @param array $args
	 * @param array $assoc_args
	 */
	public function drop_index( $args, $assoc_args ) {
		$this->_connect_check();

		WP_CLI::line( __( 'Dropping index...', 'wp-redisearch' ) );
		$index = new Index( WPRedisearch::$client );
		$result = $index->drop();
		if ( $result ) {
			WP_CLI::success( __( 'Index dropped', 'wp-redisearch' ) );
		} else {
			WP_CLI::error( __( 'Index drop failed', 'wp-redisearch' ) );
		}

	}

	/**
	 * Index all posts for the site
	 *
	 * @synopsis [--setup] [--posts-per-page] [--offset] [--post-type] [--post-ids]
	 *
	 * @param array $args
	 * @since 0.1.2
	 * @param array $assoc_args
	 */
	public function index( $args, $assoc_args ) {
		global $wp_actions;

		$this->_connect_check();

		if ( ! empty( $assoc_args['posts-per-page'] ) ) {
			$assoc_args['posts-per-page'] = absint( $assoc_args['posts-per-page'] );
		} else {
			$assoc_args['posts-per-page'] = Settings::get( 'wp_redisearch_indexing_batches', 50 );
		}

		if ( ! empty( $assoc_args['offset'] ) ) {
			$assoc_args['offset'] = absint( $assoc_args['offset'] );
		} else {
			$assoc_args['offset'] = 0;
		}

		if ( empty( $assoc_args['post-type'] ) ) {
			$assoc_args['post-type'] = null;
		}

		$total_indexed = 0;

		//Hold original wp_actions
		$this->temporary_wp_actions = $wp_actions;

		set_transient( 'wp_redisearch_wpcli_indexing', true, $this->transient_expiration );

		timer_start();

		// This clears away dashboard notifications
		update_option( 'wp_redisearch_last_indexed', time() );
		delete_option( 'wp_redisearch_need_upgrade_index' );

		// Run setup if flag was passed.
		if ( isset( $assoc_args['setup'] ) && $assoc_args['setup'] === true ) {
			$this->create_index( $args, $assoc_args );
		}

		WP_CLI::log( WP_CLI::colorize( '%N' . __( 'Indexing posts... ', 'wp-redisearch' ) . '%N' ) );

		$result = $this->_index_helper( $assoc_args );

		WP_CLI::log( sprintf( __( 'Number of posts indexed on site: %d', 'wp-redisearch' ), $result['indexed'] ) );

		if ( !empty( $result['errors'] ) ) {
			WP_CLI::error( sprintf( __( 'Number of post index errors on site: %d', 'wp-redisearch' ), count( $result['errors'] ) ) );
		}

		WP_CLI::log( WP_CLI::colorize( '%Y' . __( 'Total time elapsed: ', 'wp-redisearch' ) . '%N' . timer_stop() ) );

		delete_transient( 'wp_redisearch_wpcli_indexing' );

		WP_CLI::success( __( 'Done!', 'wp-redisearch' ) );
	}

	/**
	 * Helper method for indexing posts
	 *
	 * @param array $args
	 *
	 * @since 0.9
	 * @return array
	 */
	private function _index_helper( $args ) {
		$indexed = 0;
		$errors = array();

		$posts_per_page = Settings::get( 'wp_redisearch_indexing_batches', 50 );

		if ( ! empty( $args['posts-per-page'] ) ) {
			$posts_per_page = absint( $args['posts-per-page'] );
		}

		$offset = 0;

		if ( ! empty( $args['offset'] ) ) {
			$offset = absint( $args['offset'] );
		}

		$post_type = Settings::get( 'wp_redisearch_post_types', 'post' );

		if ( ! empty( $args['post-type'] ) ) {
			$post_type = explode( ',', $args['post-type'] );
			$post_type = array_map( 'trim', $post_type );
		} elseif ( isset( $post_type ) && !empty( $post_type ) ) {
      $post_type = array_keys( $post_type );
    } elseif ( !isset( $post_type ) || empty( $post_type ) ) {
      $post_type = array( 'post' );
		}

		$post_in = null;

		if ( ! empty( $args['post-ids'] ) ) {
			$post_in = explode( ',', $args['post-ids'] );
			$post_in = array_map( 'trim', $post_in );
			$post_in = array_map( 'absint', $post_in );
			$post_in = array_filter( $post_in );

			$posts_per_page = count($post_in);
		}

		/**
		 * Create WP_Query here and reuse it in the loop to avoid high memory consumption.
		 */
		$query = new WP_Query();

		$index = new Index( WPRedisearch::$client );

		while ( true ) {

			$default_args = Settings::query_args();
			$default_args['post_type'] = $post_type;
			$default_args['posts_per_page'] = $posts_per_page;
			$default_args['offset'] = $offset;
	
			$args = apply_filters( 'wp_redisearch_posts_args', $default_args);

			if ( $post_in ) {
				$args['post__in'] = $post_in;
			}

			$query->query( $args );

			if ( $query->have_posts() ) {

				while ( $query->have_posts() ) {
					$query->the_post();

					$indexing_options = array();
					$index_name = Settings::indexName();
					$indexing_options['language'] = apply_filters( 'wp_redisearch_index_language', 'english', get_the_ID() );
					$indexing_options['fields'] = $index->prepare_post( get_the_ID() );
				
					$result = $index->addPosts($index_name, get_the_ID(), $indexing_options);
					$this->reset_transient();

					do_action( 'wp_redisearch_cli_post_index', get_the_ID() );

					if ( ! $result ) {
						$errors[] = get_the_ID();
					} else {
						$indexed ++;
					}
				}
			} else {
				break;
			}

			WP_CLI::log( 'Processed ' . ( $query->post_count + $offset ) . '/' . $query->found_posts . ' entries. . .' );

			$offset += $posts_per_page;

			usleep( 500 );

			// Avoid running out of memory
			$this->stop_the_insanity();
		}

		wp_reset_postdata();

		return array( 'indexed' => $indexed, 'errors' => $errors );
	}

	/**
	 * Resets some values to reduce memory footprint.
	 */
	private function stop_the_insanity() {
		global $wpdb, $wp_object_cache, $wp_actions, $wp_filter;

		$wpdb->queries = array();

		if ( is_object( $wp_object_cache ) ) {
			$wp_object_cache->group_ops = array();
			$wp_object_cache->stats = array();
			$wp_object_cache->memcache_debug = array();

			// Make sure this is a public property, before trying to clear it
			try {
				$cache_property = new ReflectionProperty( $wp_object_cache, 'cache' );
				if ( $cache_property->isPublic() ) {
					$wp_object_cache->cache = array();
				}
				unset( $cache_property );
			} catch ( ReflectionException $e ) {
			}

			/*
			 * In the case where we're not using an external object cache, we need to call flush on the default
			 * WordPress object cache class to clear the values from the cache property
			 */
			if ( ! wp_using_ext_object_cache() ) {
				wp_cache_flush();
			}

			if ( is_callable( $wp_object_cache, '__remoteset' ) ) {
				call_user_func( array( $wp_object_cache, '__remoteset' ) ); // important
			}
		}

		// Prevent wp_actions from growing out of control
		$wp_actions = $this->temporary_wp_actions;

		// WP_Query class adds filter get_term_metadata using its own instance
		// what prevents WP_Query class from being destructed by PHP gc.
		//    if ( $q['update_post_term_cache'] ) {
		//        add_filter( 'get_term_metadata', array( $this, 'lazyload_term_meta' ), 10, 2 );
		//    }
		// It's high memory consuming as WP_Query instance holds all query results inside itself
		// and in theory $wp_filter will not stop growing until Out Of Memory exception occurs.
		if ( isset( $wp_filter['get_term_metadata'] ) ) {
			/*
			 * WordPress 4.7 has a new Hook infrastructure, so we need to make sure
			 * we're accessing the global array properly
			 */
			if ( class_exists( 'WP_Hook' ) && $wp_filter['get_term_metadata'] instanceof WP_Hook ) {
				$filter_callbacks   = &$wp_filter['get_term_metadata']->callbacks;
			} else {
				$filter_callbacks   = &$wp_filter['get_term_metadata'];
			}
			if ( isset( $filter_callbacks[10] ) ) {
				foreach ( $filter_callbacks[10] as $hook => $content ) {
					if ( preg_match( '#^[0-9a-f]{32}lazyload_term_meta$#', $hook ) ) {
						unset( $filter_callbacks[10][ $hook ] );
					}
				}
			}
		}
	}

	/**
	 * Provide better error messaging for common connection errors
	 *
	 * @since 0.9.3
	 */
	private function _connect_check() {
		if ( WPRedisearch::$serverException ) {
			WP_CLI::error( __( 'Redis server is not running.', 'wp-redisearch' ) );
		} elseif ( WPRedisearch::$moduleException ) {
			WP_CLI::error( __( 'Redis server is running but RediSearch module is not loaded.', 'wp-redisearch' ) );
		}
	}

	/**
	 * Reset transient while indexing
	 *
	 * @since 2.2
	 */
	private function reset_transient() {
		set_transient( 'wp_redisearch_wpcli_indexing', true, $this->transient_expiration );
	}
}
