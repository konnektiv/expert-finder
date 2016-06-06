<?php
/**
 * Version: 1.0.0
 * Author: Konnektiv
 * Author URI: http://konnektiv.de/
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

class Expert_Finder_Profile_Field_Finder extends Expert_Finder_Buddypress_Finder {

    public function getResults($search) {
		global $wpdb, $bp;

        $results = array();

        if ( !$this->isEnabled() )
            return $results;

        $results = $wpdb->get_results( $wpdb->prepare("
			SELECT *
			FROM {$bp->profile->table_name_data}
			WHERE value LIKE %s AND
				(SELECT COUNT(id) from {$bp->profile->table_name_fields}
				WHERE id=field_id) > 0", '%' . $wpdb->esc_like($search) . '%') );

        return array_map(function($result) use ($search) {
            return new Expert_Finder_Profile_Field_Result($result, $this->options['A'], $search);
        }, array_filter($results, function($result){
            $level = xprofile_get_field_visibility_level($result->field_id, $result->user_id);
            return  $level == "public" ||
                    $level == "adminsonly" && ( current_user_can("manage_options") || $result->user_id == get_current_user_id() ) ||
                    $level == "loggedin" && is_user_logged_in();
        }));
    }
}