<?php
/**
 * Plugin Name: Expert Finder
 * Plugin URI: https://wordpress.org/plugins/expert-finder
 * Description: This WordPress plugin provides a search engine to find experts who created or commented various WordPress content types
 * Version: 1.0.0
 * Author: Konnektiv
 * Author URI: http://konnektiv.de/
 * License: GNU AGPL (license.txt)
 * Text Domain: expert-finder
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

class Expert_Finder {

	/**
	 * @var Expert_Finder
	 */
	private static $instance;

	/**
	 * Main Expert_Finder Instance
	 *
	 * Insures that only one instance of Expert_Finder exists in memory at
	 * any one time. Also prevents needing to define globals all over the place.
	 *
	 * @since Expert_Finder (1.0.0)
	 *
	 * @staticvar array $instance
	 *
	 * @return Expert_Finder
	 */
	public static function instance( ) {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new Expert_Finder;
			self::$instance->includes();
			self::$instance->setup_globals();
		}

		return self::$instance;
	}

	/**
	 * A dummy constructor to prevent loading more than one instance
	 *
	 * @since Expert_Finder (1.0.0)
	 */
	private function __construct() { /* Do nothing here */
	}



	/**
	 * Component global variables
	 *
	 * @since Expert_Finder (1.0.0)
	 * @access private
	 *
	 */
	private function setup_globals() {
		Expert_Finder_Shortcode::instance();
		Expert_Finder_Settings::instance();
	}

	/**
	 * Includes
	 *
	 * @since Expert_Finder (1.0.0)
	 * @access private
	 */
	private function includes() {
    	require_once('includes/expertfinder-autoloader-class.php');
		Expert_Finder_AutoLoader::add_path('./');
		Expert_Finder_AutoLoader::add_path('factories/');
		Expert_Finder_AutoLoader::add_path('finder/');
		Expert_Finder_AutoLoader::add_path('results/');
	}

}

Expert_Finder::instance();