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

class Expert_Finder_Activity_Stream_Result extends Expert_Finder_Result {

	private $activity;

	/**
	 *  Constructor
	 *
	 * @since Expert_Finder_Activity_Stream_Result (1.0.0)
	 */
	public function __construct( $activity, $a, $search ) {
		$this->activity = $activity;
		parent::__construct( 0, $a, $search );
	}

	protected function is_in_title() {
		return 0;
	}

	protected function is_in_content() {
		return 1;
	}

	protected function get_occurences() {
		return substr_count( $this->activity->content, $this->search );
	}

	protected function get_likes() {
		$count = 0;
		if ( Expert_Finder_Settings::likes_available() ) {
			$count = BPLIKE_LIKES::get_likers( $this->activity->id, 'activity_update' );
		}

		return count( $count );
	}

	protected function get_comments() {
		$count = BP_Activity_Activity::get_activity_comments( $this->activity->id, $this->activity->mptt_left, $this->activity->mptt_right );

		return count( $count );
	}

	public function get_authors() {
		return array( $this->activity->user_id );
	}

	public function get_title() {
		return $this->activity->action;
	}

	public function get_type() {
		return "Activity Stream update";
	}
}