<?php
/**
 * Version: 0.0.1
 * Author: Konnektiv
 * Author URI: http://konnektiv.de/
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

class Expert_Finder_Shortcode {
	/**
	 * @var Expert_Finder_Shortcode
	 */
	private static $instance;

	/**
	 * Main Expert_Finder_Shortcode Instance
	 *
	 * Insures that only one instance of Expert_Finder_Shortcode exists in memory at
	 * any one time. Also prevents needing to define globals all over the place.
	 *
	 * @since Expert_Finder_Shortcode (0.0.1)
	 *
	 * @staticvar array $instance
	 *
	 * @return Expert_Finder_Shortcode
	 */
	public static function instance( ) {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new Expert_Finder_Shortcode;
			self::$instance->setup_shortcodes();
		}

		return self::$instance;
	}

	/**
	 * A dummy constructor to prevent loading more than one instance
	 *
	 * @since Expert_Finder_Shortcode (0.0.1)
	 */
	private function __construct() { /* Do nothing here */
	}

	/**
	 * Setup the short codes to be used in templates
	 *
	 * @since Expert_Finder_Shortcode (0.0.1)
	 * @access private
	 *
	 * @uses add_shortcode() to add various shortcodes
	 */
	private function setup_shortcodes() {
		add_shortcode( 'expert_finder', array( $this, 'expert_finder_shortcode' ) );
	}

    private function is_admin_user() {
        return current_user_can( 'manage_options' );
    }

	function expert_finder_shortcode() {
		$engine = Expert_Finder_Search_Engine::instance();
		$search = $_SERVER['REQUEST_METHOD'] === 'POST'?$_POST['expert_finder_search']:false;
        $experts = array();
        $num_results = Expert_Finder_Settings::instance()->options['num_results'];
        $num_experts = Expert_Finder_Settings::instance()->options['num_experts'];

        if ($search)
		  $experts = $engine->get_experts($search);

        ob_start();
        ?>

        <form class="expert-finder search-form" method="post">
			<div class="rtp-search-form-wrapper">
				<label class="screen-reader-text hide"><?php _e('Search for Experts:', 'expert-finder') ?></label>
				<input type="search" required="required" minlength="3" placeholder="<?php _e('Search for Experts ...', 'expert-finder') ?>" value="<?php echo $search ?>"
					   name="expert_finder_search" class="search-text search-field rtp-search-input"
					   title="<?php _e('Search for Experts ...', 'expert-finder') ?>">
				<button type="submit" class="searchsubmit search-submit rtp-search-button button tiny" value="Search" title="Search">
			</div>
        </form>

        <?php if (!empty($experts)) { ?>
            <p>These experts have been found:</p>
        <?php } elseif (!empty($search)) { ?>
            <p>No experts found for: <?php echo $search ?></p>
        <?php } ?>

        <?php $expert_count = 0; ?>
        <?php foreach($experts as $user_id => $result){ ?>
            <?php if ($expert_count >= $num_experts) break; ?>
            <?php $expert_count++; ?>
            <h3>
                <a href="<?php echo get_author_posts_url( $user_id ); ?>"><?php echo get_the_author_meta( 'display_name', $user_id ); ?></a>
                <?php if ($this->is_admin_user()) { ?>
                    <small>Pw: <?php echo $result['ranking'] ?></small>
                <?php } ?>
            </h3>
            <ul>
            <?php foreach($result['results'] as $index => $document){ ?>
                <?php if ($index >= $num_results) break; ?>
				<?php $link = $document->get_link() ?>
                <li><?php echo $document->get_type() ?>:
                    <?php if ($link && !is_wp_error($link)) { ?>
                        <a href="<?php echo $link ?>"><?php echo $document->get_title() ?></a>
                    <?php } else { ?>
                        <?php echo $document->get_title() ?>
                    <?php } ?>
                    <?php if ($this->is_admin_user()) { ?>
                         -- <small>Bw: <?php echo $document->getBw() ?></small>
                    <?php } ?>
                </li>
            <?php } ?>
            </ul>
        <?php }

		return ob_get_clean();
	}
}