<?php
/**
 * Options functionality for the plugin.
 *
 * @package EDD_SL_Example
 */

namespace EDD_SL_Example\Includes\Admin;

/**
 * Class Options
 */
class Options {
	/**
	 * Store the options.
	 *
	 * @var array $options
	 */
	private static $options;

	/**
	 * Get options for the plugin.
	 *
	 * @param bool $force_reload Whether to skip caching and get options from the database.
	 *
	 * @return array Options.
	 */
	public static function get_options( $force_reload = false ) {
		// Try to get cached options.
		$options = self::$options;
		if ( empty( $options ) || true === $force_reload ) {
			$options = get_site_option( 'edd_sl_options', array() );
		}

		// Store options.
		if ( ! is_array( $options ) ) {
			$options = array();
		}

		$defaults = array(
			'license'        => false,
			'license_status' => '',
		);
		/**
		 * Filter for option defaults.
		 *
		 * @since 1.0.0
		 *
		 * @param array Option defaults.
		 */
		$defaults = apply_filters( 'edd_sl_options_defaults', $defaults );

		if ( empty( $options ) || count( $options ) < count( $defaults ) ) {
			$options = wp_parse_args(
				$options,
				$defaults
			);
		}

		self::$options = $options;

		/**
		 * Filter for overall options.
		 *
		 * @since 1.0.0
		 *
		 * @param array options.
		 */
		$options = apply_filters( 'edd_sl_options', $options );
		return $options;
	}

	/**
	 * Save options for the plugin.
	 *
	 * @param array $options array of options.
	 *
	 * @return array Options.
	 */
	public static function update_options( $options = array() ) {
		$saved_options = self::get_options( true );
		$options       = array_merge( $saved_options, $options );

		/**
		 * Filter for saving options.
		 *
		 * @since 1.0.0
		 *
		 * @param array options.
		 */
		$options = apply_filters( 'edd_sl_options_save_pre', $options );
		update_site_option( 'edd_sl_options', $options, false );

		self::$options = $options;

		return $options;
	}
}
