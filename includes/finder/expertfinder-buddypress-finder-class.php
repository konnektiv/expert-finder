<?php
/**
 * Version: 1.0.0
 * Author: Konnektiv
 * Author URI: http://konnektiv.de/
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

abstract class Expert_Finder_Buddypress_Finder extends Expert_Finder_Type_Finder {

    protected function isEnabled() {
        return isset($this->options['enabled']) && $this->options['enabled'];
    }

    public function isAvailable() {
        return function_exists('bp_is_active');
    }
}