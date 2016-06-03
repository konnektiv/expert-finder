<?php
/**
 * Version: 0.0.1
 * Author: Konnektiv
 * Author URI: http://konnektiv.de/
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

class Expert_Finder_AutoLoader {
    protected static $paths = array();

    public static function add_path($path) {
        if ($path) {
            self::$paths[] = plugin_dir_path(__FILE__) . $path;
        }
    }

    public static function load($cls) {
        if (strpos($cls, "Expert_Finder_") === 0) {
            $cls = strtolower($cls);
            $cls = str_replace("_", "-", $cls);
            $cls = str_replace("expert-finder", "expertfinder", $cls) . "-class.php";

            foreach (self::$paths as $path) {
                if (is_file($path . $cls)) {
                    require_once $path . $cls;
                    return;
                }
            }
        }
    }
}
spl_autoload_register(array('Expert_Finder_AutoLoader', 'load'));
