<?php
/**
 * Version: 0.0.1
 * Author: Konnektiv
 * Author URI: http://konnektiv.de/
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

class Expert_Finder_Post_Result extends Expert_Finder_Result {

    protected $post;

	/**
	 *  Constructor
	 *
	 * @since Expert_Finder_Post_Result (0.0.1)
	 */
	public function __construct($post, $a_title, $a_content, $search) {
        $this->post = $post;
        parent::__construct($a_title, $a_content, $search);
    }

    protected function is_in_title() {
        return (stripos($this->post->post_title, $this->search) !== FALSE?1:0);
    }

    protected function is_in_content() {
        return (stripos($this->post->post_content, $this->search) !== FALSE?1:0);
    }

    protected function get_occurences() {
        return substr_count($this->post->post_content, $this->search) + substr_count($this->post->post_title, $this->search);
    }

    protected function get_likes() {
        return intval(get_post_meta($this->post->ID, 'bp_liked_count_total', true));
    }

    protected function get_comments() {
        $count = wp_count_comments( $this->post->ID );
        return $count->approved;
    }

    public function get_authors() {
        return apply_filters('expertfinder_post_result_authors', array( $this->post->post_author ), $this->post );
    }

    public function get_title() {
        return $this->post->post_title;
    }

    public function get_type() {
        $object = get_post_type_object($this->post->post_type);
        return $object->labels->singular_name;
    }

    public function get_link() {
        return get_post_permalink($this->post->ID);
    }
}