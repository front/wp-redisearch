<?php

namespace WpRediSearch;

class Features {

	/**
	 * Stores all features that have been included (both active and inactive)
	 *
	 * @since 0.2.0
	 * @var array
	 */
	public $features = array();

	/**
	 * Return instance of the class
	 *
	 * @return object
	 * @since 0.2.0
	 */
	public static function init() {
		static $instance = false;

		if ( ! $instance  ) {
			$instance = new self();
			$instance->setup();
		}
		return $instance;
	}
	
	/**
	 * Initiate class actions
	 *
	 * @since 0.2.0
	 */
	public function setup() {
		add_action( 'init', array( $this, 'setup_features' ), 0 );
	}


	/**
	 * Save individual feature settings.
	 * This method used to save features settings using ajax request.
	 *
	 * @since 0.2.0
	 */
	public function wp_redisearch_save_feature() {
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
	 *  Parameters:
	 *  "title" (string) - Human readable title
	 *  "default_settings" (array) - Array of default settings.
	 *  "setup_cb" (callback) - Callback function when the feature is activated
	 *  "requirements_cb" (callback) - Callback function to check feature requirements
	 *  "activation_cb" (callback) - Callback function after feature activation
	 *  "feature_desc_cb" (callback) - Callback function that outputs HTML for feature description
	 *  "feature_settings_cb" (callback) - Callback function that outputs custom feature settings
	 *
	 * @since 0.2.0
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
	 * @since 0.2.0
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
    update_option( 'fj_tests_fea', $feature_settings );
    update_option( 'fj_tests_fea_sl', $slug );
    update_option( 'fj_tests_fea_sett', $settings );
		// Make sure active is a bool
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
			$feature->after_activation();
		} elseif ( !$feature_settings[ $slug ]['active'] && $original_state ) {
			if ( ! empty( $feature->deactivation_requires_reindex ) ) {
				$data['reindex'] = true;
			}
			$feature->after_deactivation();
		}
    /**
     * Some features require re-index on deactivatino also.
		 * This filter, forces re-indexing
     * @since 0.2.0
     * @param bool $data['rendex']          reinindex status.
     * @param object $feature    						The featur itself.
		 */
		$data['reindex'] = apply_filters( 'wp_redisearch_feature_reindex', $data['reindex'], $feature );

		return $data;
	}

	/**
	 * Setup all active features
	 *
	 * @since 0.2.0
	 */
	public function setup_features() {
		foreach ( $this->features as $feature_slug => $feature ) {
			if ( $feature->is_active() ) {
				$feature->setup();
			}
		}
	}
  

  /**
   * Get a Feature object from its slug
   * @param  string $slug
   * @since 0.2.0
   * @return Feature
   */
  public function get_registered_feature( $slug ) {
    if ( empty( $this->features[ $slug ] ) ) {
      return false;
    }
    return $this->features[ $slug ];
  }
}
