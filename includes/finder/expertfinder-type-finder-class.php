<?php
/**
 * Version: 0.0.1
 * Author: Konnektiv
 * Author URI: http://konnektiv.de/
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

abstract class Expert_Finder_Type_Finder {

    protected $options;

	/**
	 *  Constructor
	 *
	 * @since Expert_Finder_Post_Result (0.0.1)
	 */
	public function __construct($options) {
        $this->options = $options;
    }

    abstract protected function getResults($search);

}