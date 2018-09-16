<?php

namespace WPRedisearch;

class Select extends Fields {
  
	/**
	 * @param object $name
	 */
  public static $name;

	/**
	 * @param object $label
	 */
  public $label;

	/**
	 * @param object $description
	 */
  public $description;

  /**
   * Initiate field.
   *
   * @since 0.1.0
   * @param string $name
   * @param string $label
   * @param string $description
   * @return function field_html()
   */
  public function __construct($name, $label, $description) {
    self::$name = $name;
    $this->label = $label;
    $this->description = $description;
    return $this->field_html();
  }

  /**
   * Get field value, so we can use it to show current value.
   *
   * @since 0.1.0
   * @param
   * @return string $field_value
   */
  public static function get_value() {
    $field_value = parent::get_field_value( self::$name );
    return $field_value;
  }

  /**
   * Set field name attr.
   *
   * @since 0.1.0
   * @param
   * @return string $field_name
   */
  public static function input_name() {
    $options_name = parent::$options_name;
    return $options_name . '[text__' . self::$name . ']';
  }

  public function field_html() {
    ?>
    <tr valign="top" class="wpex-custom-admin-screen-background-section">
            <th scope="row"><?php esc_html_e( 'Select Example', 'wp-redisearch' ); ?></th>
            <td>
              <?php $value = self::get_theme_option( 'select_example' ); ?>
              <select name="<?php echo self::$options_name ?>[select_example]">
                <?php
                $options = array(
                  '1' => esc_html__( 'Option 1', 'wp-redisearch' ),
                  '2' => esc_html__( 'Option 2', 'wp-redisearch' ),
                  '3' => esc_html__( 'Option 3', 'wp-redisearch' ),
                );
                foreach ( $options as $id => $label ) { ?>
                  <option value="<?php echo esc_attr( $id ); ?>" <?php selected( $value, $id, true ); ?>>
                    <?php echo strip_tags( $label ); ?>
                  </option>
                <?php } ?>
              </select>
            </td>
          </tr>

    <tr valign="top" class="wprds-text-input">
      <th scope="row"><?php echo $this->label ?></th>
      <td>
        <?php $value = self::get_value(); ?>
        <?php //$value = 'input value' ?>
        <input type="text" name="<?php echo self::input_name() ?>"
              value="<?php echo esc_attr( $value ); ?>"
        />
        <span><?php echo $this->description ?></span>
      </td>
    </tr>
    <?php
  }
}
