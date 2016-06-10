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

class Expert_Finder_Profile_Field_Result extends Expert_Finder_Result {

	private $field, $group;

	/**
	 *  Constructor
	 *
	 * @since Expert_Finder_Profile_Field_Result (1.0.0)
	 */
	public function __construct( $field, $a, $search ) {
		$this->field = new BP_XProfile_Field( $field->field_id, $field->user_id );
		$this->group = new BP_XProfile_Group( $this->field->group_id );

		parent::__construct( 0, $a, $search );
	}

	protected function is_in_title() {
		return 0;
	}

	protected function is_in_content() {
		return 1;
	}

	protected function get_occurences() {
		return substr_count( $this->field->data->value, $this->search );
	}

	protected function get_likes() {
		return 0;
	}

	protected function get_comments() {
		return 0;
	}

	public function get_authors() {
		return array( $this->field->data->user_id );
	}

	public function get_title() {
		return $this->field->data->value;
	}

	public function get_type() {
		return "Profile Field ({$this->group->name}/{$this->field->name})";
	}
}