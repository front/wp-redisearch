<?php

namespace WpRediSearch;

class Feature {
	/**
	 * Feature slug
	 *
	 * @var string
	 * @since 0.2.0
	 */
	public $slug;

	/**
	 * Feature title
	 *
	 * @var string
	 * @since 0.2.0
	 */
	public $title;

	/**
	 * Optional feature default settings
	 *
	 * @since 0.2.0
	 * @var  array
	 */
	public $default_settings = array();

	/**
	 * Contains registered callback to execute after setup
	 *
	 * @since 0.2.0
	 * @var callback
	 */
	public $setup_cb;

	/**
	 * Callback function to check feature requirements
	 *
	 * @since 0.2.0
	 * @var callback
	 */
	public $requirements_cb;

	/**
	 * Callback function after feature activation
	 *
	 * @since 0.2.0
	 * @var callback
	 */
	public $activation_cb;

	/**
	 * Callback function after feature de-activation
	 *
	 * @since 0.2.0
	 * @var callback
	 */
	public $deactivation_cb;

	/**
	 * Callback function that outputs HTML for feature description
	 *
	 * @since 0.2.0
	 * @var callback
	 */
	public $feature_desc_cb;

	/**
	 * Callback function that outputs custom feature settings
	 *
	 * @since 0.2.0
	 * @var callback
	 */
	public $feature_settings_cb;

	/**
	 * True if the feature requires content reindexing after activating
	 *
	 * @since 0.2.0
	 * @var bool
	 */
	public $requires_reindex;

	/**
	 * Some features need extra options/settings.
	 * In the main features listing page, we can only have some basic settings.
	 * So in case we need more options, we can create fields and pass via this callback method.
	 *
	 * @since 0.2.0
	 * @var bool
	 */
	public $feature_options_cb;

	/**
	 * Initiate the feature, setting all relevant instance variables
	 *
	 * @since 0.2.0
	 */
	public function __construct( $args ) {
		foreach ( $args as $key => $value ) {
			$this->$key = $value;
		}
	}

	/**
	 * Returns requirements status of the feature
	 *
	 * @since 0.2.0
	 * @return 
	 */
	public function requirements_status() {
		$status = new \stdClass();
		$status->code = 0;
		$status->message = array();

		if ( ! empty( $this->requirements_cb ) ) {
			$status = call_user_func( $this->requirements_cb, $this );
		}

		/**
		 * If feature is active but requirements are not satisfied
		 * deactivate the feature.
		 * This is usefull in case for example, for some reasons WooCommerce plugin being deactivated.
		 * 
		 * @since 0.2.3
		 */
		if ( $status->code == 1 && $this->is_active() ) {
		 	Features::init()->update_feature( $this->slug, array( 'active' => false ) );
		}

		return apply_filters( 'wp_redisearch_feature_requirements_status', $status, $this );
  }
  
	/**
	 * Run on every page load for feature to set itself up
	 *
	 * @since 0.2.0
	 */
	public function setup() {
		if ( ! empty( $this->setup_cb ) ) {
			call_user_func( $this->setup_cb, $this );
		}

		//
		add_action( 'wp_redisearch_settings_indexing_fields', array( $this, 'feature_options_fields' ) );

		do_action( 'wp_redisearch_feature_setup', $this->slug, $this );
	}

	/**
	 * Returns true if feature is active
	 *
	 * @since 0.2.0
	 * @return boolean
	 */
	public function is_active() {
    $feature_settings = get_option( 'wp_redisearch_feature_settings', array() );

		$active = false;

		if ( ! empty( $feature_settings[ $this->slug ] ) && $feature_settings[ $this->slug ]['active'] ) {
			$active = true;
		}

		return apply_filters( 'wp_redisearch_feature_active', $active, $feature_settings, $this );
	}

	/**
	 * Run after a feature is activated
	 *
	 * @since 0.2.0
	 */
	public function after_activation() {
		if ( ! empty( $this->activation_cb ) ) {
			call_user_func( $this->activation_cb, $this );
		}
		do_action( 'wp_redisearch_feature_after_activation', $this->slug, $this );
	}

	/**
	 * Run after a feature is de-activated
	 *
	 * @since 0.2.0
	 */
	public function after_deactivation() {
		if ( ! empty( $this->deactivation_cb ) ) {
			call_user_func( $this->deactivation_cb, $this );
		}
		do_action( 'wp_redisearch_feature_after_deactivation', $this->slug, $this );
	}
	
	/**
	 * Outputs feature box.
	 *
	 * @since 0.2.0
	 */
	public function feature_options_fields() {
		if ( ! empty( $this->feature_options_cb ) ) {
			call_user_func( $this->feature_options_cb, $this );
		}
	}

	/**
	 * Outputs feature box.
	 *
	 * @since 0.2.0
	 */
	public function output_feature_box() {
		$requirements_status = $this->requirements_status();
		if ( ! empty( $requirements_status->message ) ) {
			$messages = (array) $requirements_status->message;
			$notice_class = $requirements_status->code == 1 ? 'error' : 'warning';
			foreach ( $messages as $message ) {
				echo '<div class="wprds-feature-notice notice inline notice-' . $notice_class . ' notice-alt">';
					echo wp_kses_post( $message );
				echo '</div>';
			}
		}
		
		$this->output_desc();
		$this->output_settings_box();
	}

	/**
	 * Outputs feature description
	 *
	 * @since 0.2.0
	 */
	public function output_desc() {
		if ( ! empty( $this->feature_desc_cb ) ) {
			call_user_func( $this->feature_desc_cb, $this );
		}

		do_action( 'wp_redisearch_feature_box_full', $this->slug, $this );
	}

	/**
	 * Outputs Settings.
	 *
	 * @since 0.2.0
	 */
	public function output_settings_box() {
		$requirements_status = $this->requirements_status();
		?>

		<h3><?php esc_html_e( 'Settings', 'wp-redisearch' ); ?></h3>
		<div class="feature-fields" >
			<div class="field-name status"><?php esc_html_e( 'Status', 'wp-redisearch' ); ?></div>
			<div class="input-wrap <?php if ( 1 === $requirements_status->code ) : ?>disabled<?php endif; ?>">
				<label for="feature_<?php echo esc_attr( $this->slug ); ?>_enabled">
					<input type="radio" name="feature_<?php echo esc_attr( $this->slug ); ?>"
									id="feature_<?php echo esc_attr( $this->slug ); ?>_enabled"
									data-field-name="active"
									class="setting-field" <?php if ( 1 === $requirements_status->code ) : ?>disabled<?php endif; ?>
									<?php if ( $this->is_active() ) : ?>checked<?php endif; ?>
									value="1" /><?php esc_html_e( 'Enabled', 'wp-redisearch' ); ?>
				</label><br>
				<label for="feature_<?php echo esc_attr( $this->slug ); ?>_disabled">
					<input type="radio" name="feature_<?php echo esc_attr( $this->slug ); ?>"
									id="feature_<?php echo esc_attr( $this->slug ); ?>_disabled"
									data-field-name="active"
									class="setting-field" <?php if ( 1 === $requirements_status->code ) : ?>disabled<?php endif; ?>
									<?php if ( !$this->is_active() ) : ?>checked<?php endif; ?>
									value="0" /><?php esc_html_e( 'Disabled', 'wp-redisearch' ); ?>
				</label>
			</div>
		</div>

		<?php
		if ( ! empty( $this->feature_settings_cb ) ) {
			call_user_func( $this->feature_settings_cb, $this );
			return;
		}
		do_action( 'wp_redisearch_feature_box_settings_' . $this->slug, $this );
		?>
		<div class="action-wrap">
			<?php if ( $this->requires_reindex ) : ?>
				<span class="reindex-required">
					<?php esc_html_e('Setting adjustments to this feature require a re-index.', 'wp-redisearch' ); ?>
				</span>
			<?php endif; ?>

			<a class="button button-primary save-settings <?php if ( 1 === $requirements_status->code ): ?>disabled<?php endif; ?>"
					data-feature="<?php echo esc_attr( $this->slug ); ?>">
				<?php esc_html_e( 'Save', 'wp-redisearch' ); ?>
			</a>
		</div>
		<?php
	}
}
