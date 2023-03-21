<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://https://sudhanshu.wisdmlabs.net/
 * @since      1.0.0
 *
 * @package    Con_Calendar
 * @subpackage Con_Calendar/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Con_Calendar
 * @subpackage Con_Calendar/includes
 * @author     Sudhanshu Rai <sudhanshu.rai@wisdmlabs.com>
 */
class Con_Calendar_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'con-calendar',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
