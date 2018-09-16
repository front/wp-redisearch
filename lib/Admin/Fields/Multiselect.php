<?php

namespace WPRedisearch;

class Multiselect extends Fields {
  
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
	 * @param object $description
	 */
  public static $options;

  /**
   * Initiate field.
   *
   * @since 0.1.0
   * @param string $name
   * @param string $label
   * @param string $description
   * @param array $options
   * @return function field_html()
   */
  public function __construct($name, $label, $description, $options) {
    self::$name = $name;
    self::$label = $label;
    self::$description = $description;
    self::$options = $options;
    return $this->field_html();
  }

  /**
   * Get field value, so we can use it to show current value.
   *
   * @since 0.1.0
   * @param
   * @return array $field_value
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
    return $options_name . '[multiselect__' . self::$name . ']';
  }

  /**
   * And the actual output markup of the field.
   *
   * @since 0.1.0
   * @param
   * @return
   */
  public function field_html() {
    ?>
    <div class="wprds-field multiselect">
      <div class="label"><?php echo self::$label ?></div>
      <div class="options">
        <?php
          foreach ( self::$options as $key => $value) {
            ?>
              <label class="option">
                <input type="checkbox"
                      name="<?php echo self::input_name() ?>[<?php echo $value ?>]"
                      <?php echo (isset(self::get_value()[ $value]) && self::get_value()[ $value] == 'on') ? 'checked="checked"' : '' ?> />
                <?php echo $value ?>
              </label>
            <?php
          }
        ?>
      </div>
      <span class="desc"><?php echo self::$description ?></span>
    </div>
    <?php
  }
}