<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

use WPRedisearch\Settings;
use WPRedisearch\WPRedisearch;
use WPRedisearch\Features;
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
	 * @since 0.2.0
	 */
	private $transient_expiration = 900; // 15 min

	/**
	 * Holds temporary wp_actions when indexing with pagination
	 *
	 * @since 0.2.0
	 */
	private $temporary_wp_actions = array();

  /**
	 * Get status of index.
	 *
	 * @since 0.2.0
	 */
	public function info() {
		$this->_connect_check();
    
    try {
			$client = Setup::connect();
			$index_name = Settings::indexName();
      $info = $client->rawCommand('FT.INFO', [ $index_name ]);

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
	 * Creates the index with the settings provided.
	 *
	 * @subcommand create-index
	 * @since 0.2.0
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
	 * Drop (deletes) current index. 
	 * Warning! This will remove your existing index for the entire site.
	 *
	 * @subcommand drop-index
	 * @since 0.2.0
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
	 * ## OPTIONS
	 *
	 * [--setup]
	 * Drops existing index, creates new index then indexes the posts
	 * 
	 * [--posts-per-page]
	 * Sets number of posts to be indexed in a batch
	 * 
	 * [--offset]
	 * Bypasses passed number of posts, then indexes them
	 * 
	 * [--post-type]
	 * Sets post type to be indexed
	 * 
	 * [--post-ids]
	 * Only indexes posts from comma separated list
	 * 
	 * [--write-to-disk]
	 * If this option passed, created index, will be written to the disk
	 * This is usefull in case there was some sort of network issues, you don't need to re-index the whole website
	 *
	 * ## EXAMPLES
   *
	 *  $ wp redisearch index --setup
	 *  Drops, creates and indexes
   *
	 *  $ wp redisearch index --posts-per-page=20
	 *  Indexes only 20 posts at a time
   *
	 *  $ wp redisearch index --offset=30
	 *  Starts indexing from post 31
   *
	 *  $ wp redisearch index --post-type=page
	 *  Only indexes pages
   *
	 *  $ wp redisearch index --post-ids=10,11,12,14
	 *  Only indexes posts with which have given ids
   *
	 *  $ wp redisearch index --write-to-disk
	 *  After finished with indexing, writes the index into the disk as a file
	 * 
	 * @synopsis [--setup] [--posts-per-page] [--offset] [--post-type] [--post-ids] [--write-to-disk]
	 * 
	 * @param array $args
	 * @since 0.2.0
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

		if ( empty( $assoc_args['write-to-disk'] ) ) {
			$assoc_args['write-to-disk'] = null;
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
	 * @since 0.2.0
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

		$post_types = Settings::get( 'wp_redisearch_post_types', 'post' );

		if ( ! empty( $args['post-type'] ) ) {
			$post_types = explode( ',', $args['post-type'] );
			$post_types = array_map( 'trim', $post_types );
		} elseif ( isset( $post_types ) && !empty( $post_types ) ) {
      $post_types = array_keys( $post_types );
    } elseif ( !isset( $post_types ) || empty( $post_types ) ) {
      $post_types = array( 'post' );
		}

    /**
     * Modify indexable post types
     * 
     * @since 0.2.1
     * @param array $post_types        Default terms list
     * @return array $post_types       Modified taxobomy terms list
     */
    $post_types = apply_filters( 'wp_redisearch_indexable_post_types', $post_types );

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
			$default_args['post_type'] = $post_types;
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

		// If --write-to-disk flag passed or it was enabled in settings, after indexing posts, write document to disk to persist it.
		if ( ! empty( $args['write-to-disk'] ) || Settings::get( 'wp_redisearch_write_to_disk' ) ) {
      $index->writeToDisk();
		}

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

		// It's high memory consuming as WP_Query instance holds all query results inside itself
		// and in theory $wp_filter will not stop growing until Out Of Memory exception occurs.
		if ( isset( $wp_filter['get_term_metadata'] ) ) {
			/*
			 * WordPress 4.7 has a new Hook infrastructure, so we need to make sure
			 * we're accessing the global array properly
			 */
			if ( class_exists( 'WP_Hook' ) && $wp_filter['get_term_metadata'] instanceof WP_Hook ) {
				$filter_callbacks = &$wp_filter['get_term_metadata']->callbacks;
			} else {
				$filter_callbacks = &$wp_filter['get_term_metadata'];
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
	 * Lists all registered features.
	 *
	 * ## OPTIONS
	 * [--all]
	 * Lists all registered features regardless of their active status
	 * 
	 * ## EXAMPLES
	 * 
	 *  $ wp redisearch list-features
	 *  Lists activated features
	 * 
	 *  $ wp redisearch list-features --all
	 *  Lists all regisred features
	 * 
	 * @synopsis [--all]
	 * @subcommand list-features
	 * @since 0.2.0
	 * @param array $args
	 * @param array $assoc_args
	 */
	public function list_features( $args, $assoc_args ) {
		if ( empty( $assoc_args['all'] ) ) {
			$features = get_option( 'wp_redisearch_feature_settings', array() );
			WP_CLI::line( __( 'Active features:', 'wp-redisearch' ) );

			foreach ( $features as $key => $feature ) {
				if( $feature['active'] ) {
					WP_CLI::line( $key );
				}
			}
		} else {
			WP_CLI::line( __( 'Registered features:', 'wp-redisearch' ) );
			$features = wp_list_pluck( Features::init()->features, 'slug' );

			foreach ( $features as $feature ) {
				WP_CLI::line( $feature );
			}
		}
	}


	/**
	 * Activate a feature.
	 *
	 * Activates a feature and returns error, in case there is some
	 * If feature retuires re-index, returns warning
	 * 
	 * ## OPTIONS
	 * 
	 * <feature-slug>
	 * Slug of registered feature to be activated
	 * 
	 * ## EXAMPLES
	 *  
	 *  $ wp redisearch activate-feature synonym
	 *  This will activate synonym feature
	 * 
	 * @synopsis <feature>
	 * @subcommand activate-feature
	 * @since 0.2.0
	 * @param array $args
	 * @param array $assoc_args
	 */
	public function activate_feature( $args, $assoc_args ) {
		$feature = Features::init()->get_registered_feature( $args[0] );

		if ( empty( $feature ) ) {
			WP_CLI::error( __( 'No feature with this slug is registered.', 'wp-redisearch' ) );
		}

		if ( $feature->is_active() ) {
			WP_CLI::error( __( 'This feature is already active.', 'wp-redisearch' ) );
		}

		$status = $feature->requirements_status();

		if ( 1 === $status->code ) {
			WP_CLI::error( sprintf( __( 'Feature requirements are not met: %s', 'wp-redisearch' ), WP_CLI::colorize( '%R' . implode( "\n\n", (array) $status->message ) . '%N' ) ) );
		} elseif ( 2 === $status->code ) {
			WP_CLI::warning( sprintf( __( 'Feature can be used, but there are warnings: %s', 'elasticpress' ), WP_CLI::colorize( '%y' . implode( "\n\n", (array) $status->message ) . '%N' ) ) );
		}

		Features::init()->update_feature( $feature->slug, array( 'active' => true ) );

		if ( $feature->requires_reindex ) {
			WP_CLI::warning( __( 'This feature requires a re-index. It might not work properly until you run the index command.', 'wp-redisearch' ) );
			WP_CLI::line( sprintf( __( 'Just run %s', 'wp-redisearch' ), WP_CLI::colorize( '%G' . 'wp redisearch index --setup' . '%N' ) ) );
		}

		WP_CLI::success( sprintf( __( 'Feature %s activated', 'wp-redisearch' ), WP_CLI::colorize( '%G' . $feature->title . '%N' ) ) );
	}

	/**
	 * Dectivate a feature.
	 *
	 * deactivates a feature and returns error, in case there is some
	 * If feature retuires re-index, returns warning
	 * 
	 * ## OPTIONS
	 * 
	 * <feature-slug>
	 * Slug of registered feature to be deactivated
	 * 
	 * ## EXAMPLES
	 *  
	 *  $ wp redisearch deactivate-feature synonym
	 *  This will deactivate synonym feature
	 * 
	 *
	 * @synopsis <feature>
	 * @subcommand deactivate-feature
	 * @since 0.2.0
	 * @param array $args
	 * @param array $assoc_args
	 */
	public function deactivate_feature( $args, $assoc_args ) {
		$feature = Features::init()->get_registered_feature( $args[0] );

		if ( empty( $feature ) ) {
			WP_CLI::error( __( 'No feature with this slug is registered.', 'wp-redisearch' ) );
		}

		$active_features = get_option( 'wp_redisearch_feature_settings', array() );

		$key = array_search( $feature->slug, array_keys( $active_features ) );

		if ( false === $key || empty( $active_features[ $feature->slug ]['active'] ) ) {
			WP_CLI::error( __( 'This feature is not active', 'wp-redisearch' ) );
		}

		Features::init()->update_feature( $feature->slug, array( 'active' => false ) );

		// Some features like synonym even on deactivation require re-index.
		if ( ! empty( $feature->deactivation_requires_reindex ) ) {
			WP_CLI::warning( __( 'This feature requires a re-index after deactivation also. It might not work properly until you run the index command.', 'wp-redisearch' ) );
			WP_CLI::line( sprintf( __( 'Just run %s', 'wp-redisearch' ), WP_CLI::colorize( '%G' . 'wp redisearch index --setup' . '%N' ) ) );
		}
		WP_CLI::success( sprintf( __( 'Feature %s deactivated', 'wp-redisearch' ), WP_CLI::colorize( '%G' . $feature->title . '%N' ) ) );
	}

	/**
	 * Provide better error messaging for common connection errors
	 *
	 * @since 0.2.0
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
	 * @since 0.2.0
	 */
	private function reset_transient() {
		set_transient( 'wp_redisearch_wpcli_indexing', true, $this->transient_expiration );
	}
}
