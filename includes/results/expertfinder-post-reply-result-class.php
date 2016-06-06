<?php
/**
 * Version: 1.0.0
 * Author: Konnektiv
 * Author URI: http://konnektiv.de/
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

class Expert_Finder_Post_reply_Result extends Expert_Finder_Post_Result {

    private $topic;

    /**
	 *  Constructor
	 *
	 * @since Expert_Finder_Post_reply_Result (1.0.0)
	 */
	public function __construct($post, $a_title, $a_content, $search) {
        $this->topic = get_post(bbp_get_reply_topic_id($post->ID));
        parent::__construct($post, $a_title, $a_content, $search);
    }

    public function get_title() {
        return $this->topic->post_title;
    }

    public function get_type() {
        return "Forum Reply";
    }
}