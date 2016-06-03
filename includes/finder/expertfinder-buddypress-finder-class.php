<?php
/**
 * Version: 0.0.1
 * Author: Konnektiv
 * Author URI: http://konnektiv.de/
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

abstract class Expert_Finder_Buddypress_Finder extends Expert_Finder_Type_Finder {

    public function isAvailable() {
        return function_exists('bp_is_active');
    }
}