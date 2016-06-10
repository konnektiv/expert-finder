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

class Expert_Finder_Search_Engine {
	/**
	 * @var Expert_Finder_Search_Engine
	 */
	private static $instance;

	/**
	 * Main Expert_Finder_Search_Engine Instance
	 *
	 * Insures that only one instance of Expert_Finder_Search_Engine exists in memory at
	 * any one time. Also prevents needing to define globals all over the place.
	 *
	 * @since Expert_Finder_Search_Engine (1.0.0)
	 *
	 * @staticvar array $instance
	 *
	 * @return Expert_Finder_Search_Engine
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new Expert_Finder_Search_Engine;
			self::$instance->setup_globals();
			self::$instance->setup_actions();
		}

		return self::$instance;
	}

	/**
	 * A dummy constructor to prevent loading more than one instance
	 *
	 * @since Expert_Finder_Search_Engine (1.0.0)
	 */
	private function __construct() { /* Do nothing here */
	}


	/**
	 * Component global variables
	 *
	 * @since Expert_Finder_Search_Engine (1.0.0)
	 * @access private
	 *
	 */
	private function setup_globals() {

		$this->options = Expert_Finder_Settings::instance()->options;
	}

	/**
	 * Setup the actions
	 *
	 * @since Expert_Finder_Search_Engine (1.0.0)
	 * @access private
	 *
	 * @uses remove_action() To remove various actions
	 * @uses add_action() To add various actions
	 */
	private function setup_actions() {
	}

	/**
	 * Perform an expert search and return all results according
	 * to the settings configured in the backend
	 *
	 * @param string $search The search phrase
	 */
	public function get_experts( $search ) {
		$result_types = $this->options['result_types'];
		$results      = array();

		foreach ( $result_types as $result_type => $options ) {
			$finder = Expert_Finder_Result_Type_Factory::getFinder( $result_type, $options );
			if ( $finder->isAvailable() ) {
				$results = array_merge( $results, $finder->getResults( $search ) );
			}
		}

		$this->sort_results( $results );
		$authors = $this->get_authors( $results );
		$this->sort_authors( $authors );

		return $authors;
	}

	private function sort_results( array &$results ) {
		usort( $results, function ( $a, $b ) {
			return ( $a->getBw() > $b->getBw() ) ? - 1 : 1;
		} );
	}

	private function sort_authors( array &$authors ) {
		uasort( $authors, function ( $a, $b ) {
			return ( $a['ranking'] > $b['ranking'] ) ? - 1 : 1;
		} );
	}

	private function get_authors( array $results ) {
		$authors = array();

		foreach ( $results as $result ) {
			$b_w = $result->getBw();

			foreach ( $result->get_authors() as $author ) {
				if ( ! isset( $authors[ $author ] ) ) {
					$authors[ $author ] = array(
						'ranking' => 0,
						'results' => array()
					);
				}

				$authors[ $author ]['ranking'] += $b_w;
				$authors[ $author ]['results'][] = $result;
			}
		}

		return $authors;
	}
}

Expert_Finder_Search_Engine::instance();