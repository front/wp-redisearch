<?php

namespace WPRedisearch;

class Fields {
  
	/**
	 * @param object $option_group
	 */
  public static $options_group;

	/**
	 * @param object $option_name
	 */
  public static $options_name;

  /**
   * Initiate fields.
   * 
   * @since 0.1.0
   * @param string $options_group
   * @param string $options_name
   * @return 
   */
  public static function make( $options_group, $options_name ) {
    self::$options_group = $options_group;
    self::$options_name = $options_name;
  }

  /**
   * Initiate fields.
   *
   * @since 0.1.0
   * @param string $type
   * @param string $name
   * @param string $label
   * @param string $description
   * @param array $options
   * @return
   */
  public static function factory($type, $name, $label = null, $description = null, $options = null) {
    $class = self::type_to_class($type, __NAMESPACE__);
    $field = new $class( $name, $label, $description, $options );
  }

  /**
   * Add field to the page.
   *
   * @since 0.1.0
   * @param
   * @return
   */
  public static function add() {
    return call_user_func_array( array( get_class(), 'factory' ), func_get_args() );
  }

  /**
   * Returns all theme options.
   *
   * @since 0.1.0
   * @param
   * @return
   */
  public static function get_options_values() {
    return get_option( self::$options_name );
  }

  /**
   * Returns single option value.
   *
   * @since 0.1.0
   * @param string $name
   * @return string|array $option_value
   */
  public static function get_field_value( $name ) {
    $options = self::get_options_values();
    if ( isset( $options[$name] ) ) {
      return $options[$name];
    }
  }

	/**
	 * Convert a string representing an object type to a fully qualified class name
	 *
	 * @param  string $type
	 * @param  string $namespace
	 * @return string
	 */
	public static function type_to_class( $type, $namespace = '' ) {
		$type = ucwords( $type );
		$type = str_replace( ' ', '_', $type );
		$class = $type;
		if ( $namespace ) {
			$class = $namespace . '\\' . $class;
		}
		return $class;
	}
}