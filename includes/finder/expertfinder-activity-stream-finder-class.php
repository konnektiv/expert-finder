<?php
/**
 * Version: 1.0.0
 * Author: Konnektiv
 * Author URI: http://konnektiv.de/
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

class Expert_Finder_Activity_Stream_Finder extends Expert_Finder_Buddypress_Finder {

    public function getResults($search) {

        $results = array();

        if ( !$this->isEnabled() )
            return $results;

        $results = bp_activity_get(array(
            'filter' => array( 'action' => 'activity_update' ),
            'search_terms'   => $search
        ));

        return array_map(function($activity) use ($search) {
            return new Expert_Finder_Activity_Stream_Result($activity, $this->options['A'], $search);
        }, $results['activities']);
    }
}