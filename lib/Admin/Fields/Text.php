<?php

namespace WPRedisearch;

class Text extends Fields {
  
	/**
	 * @param object $name
	 */
  public static $name;

	/**
	 * @param object $label
	 */
  public static $label;

	/**
	 * @param object $description
	 */
  public static $description;

  /**
   * Initiate field.
   *
   * @since 0.1.0
   */
  public function __construct($name, $label, $description) {
    self::$name = $name;
    self::$label = $label;
    self::$description = $description;
    return $this->field_html();
  }

  /**
   * Get field value, so we can use it to show current value.
   *
   * @since 0.1.0
   */
  public static function get_value() {
    $field_value = parent::get_field_value( self::$name );
    return $field_value;
  }

  /**
   * Set field name attr.
   *
   * @since 0.1.0
   */
  public static function input_name() {
    $options_name = parent::$options_name;
    return $options_name . '[text__' . self::$name . ']';
  }

  /**
   * And the actual output markup of the field.
   *
   * @since 0.1.0
   */
  public function field_html() {
    ?>
    <div class="wprds-field text-field">
      <div class="label"><?php echo self::$label ?></div>
      <input type="text" name="<?php echo self::input_name() ?>"
            value="<?php echo esc_attr( self::get_value() ); ?>" />
      <span class="desc"><?php echo self::$description ?></span>
    </div>
    <?php
  }
}