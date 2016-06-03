<?php
/**
 * Version: 0.0.1
 * Author: Konnektiv
 * Author URI: http://konnektiv.de/
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

abstract class Expert_Finder_Result {

    private $a_title, $a_content, $options;
    protected $search;

	/**
	 *  Constructor
	 *
	 * @since Expert_Finder_Result (0.0.1)
	 */
	protected function __construct($a_title, $a_content, $search) {
        $this->a_title   = $a_title;
        $this->a_content = $a_content;
        $this->search    = $search;
        $this->options   = Expert_Finder_Settings::instance()->options;
    }

    public function getBw() {
        return $this->getA() * $this->getL() * $this->getK() * $this->getH();
    }

    private function getA() {
        return $this->is_in_title() * $this->a_title + $this->is_in_content() * $this->a_content;
    }

    private function getH() {
        return pow($this->options['G_H'], $this->get_occurences());
    }

    private function getL() {
        return pow($this->options['G_L'], $this->get_likes());
    }

    private function getK() {
        return pow($this->options['G_K'], $this->get_comments());
    }

    public function get_link() {
        return null;
    }

    abstract protected function is_in_title();

    abstract protected function is_in_content();

    abstract protected function get_occurences();

    abstract protected function get_likes();

    abstract protected function get_comments();

    abstract protected function get_authors();

    abstract protected function get_title();

    abstract protected function get_type();

}