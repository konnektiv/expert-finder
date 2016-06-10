<?php
/**
 * Version: 1.0.0
 * Author: Konnektiv
 * Author URI: http://konnektiv.de/
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Expert_Finder_Result_Type_Factory {

	/**
	 * A dummy constructor to prevent loading more than one instance
	 *
	 * @since Expert_Finder_Result_Type_Factory (1.0.0)
	 */
	private function __construct() { /* Do nothing here */
	}

	public static function getFinder( $result_type, $options = array() ) {
		$finder = "Expert_Finder_{$result_type}_Finder";

		if ( class_exists( $finder ) ) {
			$reflection = new \ReflectionClass( $finder );
			$finder     = $reflection->newInstanceArgs( array( $options ) );
		} else {
			error_log( "Unknown result type: $result_type" );
		}

		return $finder;
	}

}