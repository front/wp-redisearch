<?php
namespace WpRediSearch\Features;

use FKRediSearch\Index;
use WpRediSearch\RediSearch\Client;
use WpRediSearch\Settings;
use WpRediSearch\Features;

class WooCommerce {

  /**
   * The Index object
   * @since 1.0.0
   * @var string
   */
  public $index;

  /**
  * Initiate synonym terms to be added to the index
  * @since 0.2.1
  * @param
  * @return
  */
  public function __construct() {
    $client = (new Client())->return();
    $this->index = new Index($client);
    $this->index->setIndexName( Settings::indexName() );

    Features::init()->register_feature( 'woocommerce', array(
      'title' => 'WooCommerce',
      'setup_cb' => array( $this, 'setup' ),
      'requirements_cb' => array( $this, 'requirements' ),
      'activation_cb' => array( $this, 'activated' ),
      'deactivation_cb' => array( $this, 'deactivated' ),
      'feature_desc_cb' => array( $this, 'feature_desc' ),
      'feature_options_cb' => array( $this, 'feature_options' ),
      'requires_reindex' => true,
      'deactivation_requires_reindex' => false,
    ) );
  }

	/**
	 * This method will run on each page load.
   * You can hook functions which must run always.
	 *
	 * @since 0.2.1
	 */
  public function setup () {
    add_filter( 'wp_redisearch_indexable_post_types', array( $this, 'indexable_post_type' ) );
    add_filter( 'wp_redisearch_indexable_meta_keys', array( $this, 'indexable_meta_keys' ) );
    add_filter( 'wp_redisearch_indexable_temrs', array( $this, 'indexable_terms' ), 10, 2 );
  }
    
	/**
	 * Add product to indexable post types list.
	 *
   * @param   array $post_types     Array of indexable post types
   * @since   0.2.1
   * @return  array $post_types
	 */
  public function indexable_post_type ( $post_types ) {
    return array_merge( $post_types, array( 'product' ) );
  }

	/**
	 * Add product meta keys to indexable meta keys
   * 
	 * @param array $meta         Existing meta keys
   * @since 0.2.1
   * @return  array
	 */
  public function indexable_meta_keys( $meta ) {
    $indexable_keys = array_unique(
      array(
        '_thumbnail_id',
        '_product_attributes',
        '_wpb_vc_js_status',
        '_swatch_type',
        'total_sales',
        '_downloadable',
        '_virtual',
        '_regular_price',
        '_sale_price',
        '_tax_status',
        '_tax_class',
        '_purchase_note',
        '_featured',
        '_weight',
        '_length',
        '_width',
        '_height',
        '_visibility',
        '_sku',
        '_sale_price_dates_from',
        '_sale_price_dates_to',
        '_price',
        '_sold_individually',
        '_manage_stock',
        '_backorders',
        '_stock',
        '_upsell_ids',
        '_crosssell_ids',
        '_stock_status',
        '_product_version',
        '_product_tabs',
        '_override_tab_layout',
        '_suggested_price',
        '_min_price',
        '_customer_user',
        '_variable_billing',
        '_wc_average_rating',
        '_product_image_gallery',
        '_bj_lazy_load_skip_post',
        '_min_variation_price',
        '_max_variation_price',
        '_min_price_variation_id',
        '_max_price_variation_id',
        '_min_variation_regular_price',
        '_max_variation_regular_price',
        '_min_regular_price_variation_id',
        '_max_regular_price_variation_id',
        '_min_variation_sale_price',
        '_max_variation_sale_price',
        '_min_sale_price_variation_id',
        '_max_sale_price_variation_id',
        '_default_attributes',
        '_swatch_type_options',
        '_order_key',
        '_billing_company',
        '_billing_address_1',
        '_billing_address_2',
        '_billing_city',
        '_billing_postcode',
        '_billing_country',
        '_billing_state',
        '_billing_email',
        '_billing_phone',
        '_shipping_address_1',
        '_shipping_address_2',
        '_shipping_city',
        '_shipping_postcode',
        '_shipping_country',
        '_shipping_state',
        '_billing_last_name',
        '_billing_first_name',
        '_shipping_first_name',
        '_shipping_last_name',
      )
    );

    return array_merge( $meta, $indexable_keys );
  }
  
	/**
	 * Index WooCommerce taxonomy names.
	 *
   * @param   array $terms      Array of indexable taxonomy names
   * @param   array $post       Post(product) properties array.
   * @since   0.2.1
   * @return  array
	 */
  public function indexable_terms ( $terms, $post ) {
    $wc_taxonomies = array( 'product_type', 'product_visibility', );

    if ( $attribute_taxonomies = wc_get_attribute_taxonomies() ) {
      foreach ( $attribute_taxonomies as $tax ) {
        if ( $name = wc_attribute_taxonomy_name( $tax->attribute_name ) ) {
          if ( empty( $tax->attribute_) ) {
            $wc_taxonomies[] = $name;
          }
        }
      }
    }

    return array_merge( $terms, $wc_taxonomies );
  }
  
	/**
	 * Check if WooCommerce plugin installed.
	 *
	 * @since 0.2.1
	 */
  public function requirements () {
    $status = new \stdClass();
    if ( ! class_exists( 'WooCommerce' ) ) {
      $status->code = 1;
      $status->message = esc_html__( 'WooCommerce plugin not installed.', 'wp-redisearch' );
    } else {
      $status->code = 0;
      $status->message = '';
    }

    return $status;
  }
  
	/**
	 * Fires after feature activation.
	 *
	 * @since 0.2.1
	 */
  public function activated () {
  }
  
	/**
	 * Fires after feature deactivation.
	 *
	 * @since 0.2.1
	 */
  public function deactivated () {
  }

	/**
	 * Feature description.
   * This will be added to feature setting box.
	 *
	 * @since 0.2.1
	 */
  public function feature_desc () {
    ?>
      <p><?php esc_html_e( 'Finding products more faster and more precise regardless of buyers misspelling, means more success for your business.', 'wp-redisearch' ) ?></p>
    <?php
  }

	/**
	 * Feature option/settings.
   * Here we're adding fields to plugin options page.
	 *
	 * @since 0.2.1
	 */
  public function feature_options () {
  }

}