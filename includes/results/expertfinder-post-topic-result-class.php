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

class Expert_Finder_Post_topic_Result extends Expert_Finder_Post_Result {

	protected function get_comments() {
		return bbp_get_topic_reply_count( $this->post->ID, true );
	}

	public function get_type() {
		return "Forum Topic";
	}
}