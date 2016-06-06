<?php
/**
 * Version: 1.0.0
 * Author: Konnektiv
 * Author URI: http://konnektiv.de/
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

class Expert_Finder_Post_Finder extends Expert_Finder_Type_Finder {

    public function getResults($search) {
        $post_types = $this->options['post_types'];
        $results = array();

		foreach($post_types as $post_type => $options){

            if (!isset($options['enabled']) || !$options['enabled'] )
                continue;

            $posts = get_posts(array(
                'post_type'      => $post_type,
                'posts_per_page' => -1,
                's'              => $search
            ));

            foreach($posts as $post){
                $post_type_class = "Expert_Finder_Post_{$post_type}_Result";
                $class = "Expert_Finder_Post_Result";
                if (class_exists($post_type_class)) {
                    $class = $post_type_class;
                }
                $reflection = new \ReflectionClass($class);
                $results[] = $reflection->newInstanceArgs(array($post, $options['A_title'], $options['A_content'], $search));
            }
        }

        return $results;
    }
}