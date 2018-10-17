<?php

namespace WPRedisearch\Features;

class Features {

	/**
	 * Stores all features that have been included (both active and inactive)
	 *
	 * @since 0.1.2
	 * @var array
	 */
	public $features = array();

	/**
	 * Initiate class actions
	 *
	 * @since 0.1.2
	 */
	public function setup() {
		// add_action( 'init', array( $this, 'feature_activation' ), 0 );
		add_action( 'init', array( $this, 'setup_features' ), 0 );
	}


	/**
	 * Save individual feature settings
	 *
	 * @since 0.1.2
	 */
	public static function wp_redisearch_save_feature() {
		if ( empty( $_POST['feature'] ) || empty( $_POST['settings'] ) || ! check_ajax_referer( 'wprds_dashboard_nonce', 'nonce', false ) ) {
			wp_send_json_error();
			exit;
		}

		$data = $this->update_feature( $_POST['feature'], $_POST['settings'] );

		// Since we deactivated, delete auto activate notice
		if ( empty( $_POST['settings']['active'] ) ) {
			delete_option( 'wp_redisearch_feature_requires_reindex' );
		}

		wp_send_json_success( $data );
	}

	/**
	 * Registers a feature
	 *
	 * @param  string $slug
	 * @param  array  $args
	 *
	 *  Supported parameters:
	 *  "title" (string) - Human readable title
	 *  "default_settings" (array) - Array of default settings.
	 *  "setup_cb" (callback) - Callback function when the feature is activated
	 *  "requirements_cb" (callback) - Callback function to check feature requirements
	 *  "activation_cb" (callback) - Callback function after feature activation
	 *  "feature_desc_cb" (callback) - Callback function that outputs HTML for feature description
	 *  "feature_settings_cb" (callback) - Callback function that outputs custom feature settings
	 *
	 * @since 0.1.2
	 * @return boolean
	 */
	public function register_feature( $slug, $args ) {
		if ( empty( $slug ) || empty( $args ) || ! is_array( $args ) ) {
			return false;
		}
		$args['slug'] = $slug;
    $this->features[ $slug ] = new Feature( $args );
    
		return true;
	}

	/**
	 * Activate or deactivate a feature
	 *
	 * @param  string  $slug
	 * @param  array   $settings
	 * @param  bool    $force
	 * @since 0.1.2
	 * @return array|bool
	 */
	public function update_feature( $slug, $settings ) {
		$feature = $this->get_registered_feature( $slug );

		if ( empty( $feature ) ) {
			return false;
		}

		$original_state = $feature->is_active();

		$feature_settings = get_option( 'wp_redisearch_feature_settings', array() );

		if ( empty( $feature_settings[ $slug ] ) ) {
			// If doesn't exist, merge with feature defaults
			$feature_settings[ $slug ] = wp_parse_args( $settings, $feature->default_settings );
		} else {
			// If exist just merge changed values into current
			$feature_settings[ $slug ] = wp_parse_args( $settings, $feature_settings[ $slug ] );
		}

		// Make sure active is a proper bool
		$feature_settings[ $slug ]['active'] = (bool) $feature_settings[ $slug ]['active'];

		$sanitize_feature_settings = apply_filters( 'wp_redisearch_sanitize_feature_settings', $feature_settings, $feature );

		update_option( 'wp_redisearch_feature_settings', $sanitize_feature_settings );

		$data = array(
			'reindex' => false,
		);

		if ( $feature_settings[ $slug ]['active'] && ! $original_state ) {
			if ( ! empty( $feature->requires_reindex ) ) {
				$data['reindex'] = true;
			}
			$feature->activation();
		}

		return $data;
	}

	/**
	 * When plugins are adjusted, we need to determine how to activate/deactivate features
	 *
	 * @since 0.1.2
	 */
	public function feature_activation() {
		$feature_settings = get_option( 'wp_redisearch_feature_settings', false );

		if ( false === $feature_settings ) {
			$features = $this->features;

			foreach ( $features as $slug => $feature ) {
				$this->update_feature( $slug, array( 'active' => true ) );
        
        if ( $feature->requires_reindex ) {
          update_option( 'wp_redisearch_feature_requires_reindex', sanitize_text_field( $slug ) );
        }
			}
			return;
		}
	}

	/**
	 * Setup all active features
	 *
	 * @since 0.1.2
	 */
	public function setup_features() {
		foreach ( $this->features as $feature_slug => $feature ) {
			if ( $feature->is_active() ) {
				$feature->setup();
			}
		}
	}

	/**
	 * Return instance of the class
	 *
	 * @return object
	 * @since 0.1.2
	 */
	public static function factory() {
		static $instance = false;

		if ( ! $instance  ) {
			$instance = new self();
			$instance->setup();
		}
		return $instance;
  }
  

  /**
   * Easy access function to get a Feature object from a slug
   * @param  string $slug
   * @since 0.1.2
   * @return Feature
   */
  function get_registered_feature( $slug ) {
    if ( empty( $this->features[ $slug ] ) ) {
      return false;
    }
    return $this->features[ $slug ];
  }
}




/**
 * Main function for registering new feature. Since comment above for details
 *
 * @param  string $slug
 * @param  array $args
 * @since 0.1.2
 * @return bool
 */
// function wprds_register_feature( $slug, $args ) {
// 	return Features::factory()->register_feature( $slug, $args );
// }

/**
 * Update a feature
 *
 * @param  string $slug
 * @param  array $settings
 * @param  bool  $force
 * @since 0.1.2
 * @return array
 */
// function wprds_update_feature( $slug, $settings ) {
// 	return Features::factory()->update_feature( $slug, $settings );
// }

// /**
//  * Activate a feature
//  *
//  * @param  string $slug
//  * @since 0.1.2
//  */
// function wprds_activate_feature( $slug ) {
// 	Features::factory()->update_feature( $slug, array( 'active' => true ) );
// }

// /**
//  * Dectivate a feature
//  *
//  * @param  string $slug
//  * @param  bool  $force
//  * @since 0.1.2
//  */
// function wprds_deactivate_feature( $slug ) {
// 	Features::factory()->update_feature( $slug, array( 'active' => false ) );
// }
