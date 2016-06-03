<?php
/**
 * Version: 0.0.1
 * Author: Konnektiv
 * Author URI: http://konnektiv.de/
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

class Expert_Finder_Settings {
	/**
	 * @var Expert_Finder_Settings
	 */
	private static $instance;

	/**
	 * Main Expert_Finder_Settings Instance
	 *
	 * Insures that only one instance of Expert_Finder_Settings exists in memory at
	 * any one time. Also prevents needing to define globals all over the place.
	 *
	 * @since Expert_Finder_Settings (0.0.1)
	 *
	 * @staticvar array $instance
	 *
	 * @return Expert_Finder_Settings
	 */
	public static function instance( ) {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new Expert_Finder_Settings;
			self::$instance->setup_globals();
			self::$instance->setup_actions();
		}

		return self::$instance;
	}

	/**
	 * A dummy constructor to prevent loading more than one instance
	 *
	 * @since Expert_Finder_Settings (0.0.1)
	 */
	private function __construct() { /* Do nothing here */
	}


	/**
	 * Recursive argument parsing
	 *
	 * This acts like a multi-dimensional version of wp_parse_args() (minus
	 * the querystring parsing - you must pass arrays).
	 *
	 * Values from $a override those from $b; keys in $b that don't exist
	 * in $a are passed through.
	 *
	 * This is different from array_merge_recursive(), both because of the
	 * order of preference ($a overrides $b) and because of the fact that
	 * array_merge_recursive() combines arrays deep in the tree, rather
	 * than overwriting the b array with the a array.
	 *
	 * The implementation of this function is specific to the needs of
	 * BP_Group_Extension, where we know that arrays will always be
	 * associative, and that an argument under a given key in one array
	 * will be matched by a value of identical depth in the other one. The
	 * function is NOT designed for general use, and will probably result
	 * in unexpected results when used with data in the wild. See, eg,
	 * http://core.trac.wordpress.org/ticket/19888
	 *
	 * @since BuddyPress (1.8)
	 * @arg array $a
	 * @arg array $b
	 * @return array
	 */
	 private function parse_args_r( &$a, $b ) {
		$a = (array) $a;
		$b = (array) $b;
		$r = $b;
		foreach ( $a as $k => &$v ) {
			if ( is_array( $v ) && isset( $r[ $k ] ) ) {
				$r[ $k ] = self::parse_args_r( $v, $r[ $k ] );
			} else {
				$r[ $k ] = $v;
			}
		}
		return $r;
	}

	/**
	 * Component global variables
	 *
	 * @since Expert_Finder_Settings (0.0.1)
	 * @access private
	 *
	 */
	private function setup_globals() {
		$this->default_options = array(
			'G_L'			=> 1.1,
			'G_K'			=> 1.2,
			'G_H'			=> 1.1,
			'num_results'   => 3,
			'num_experts'   => 15,
			'result_types'	=> array(
                'post' => array(
                    'post_types' => array(
						'post' => array(
                            'A_title'       => 20,
                            'A_content'     => 10,
                        ),
                        'page' => array(
                            'A_title'       => 20,
                            'A_content'     => 10,
                        ),
                        'open_house' => array(
                            'A_title'       => 20,
                            'A_content'     => 10,
                        ),
                        'beta_house' => array(
                            'A_title'       => 10,
                            'A_content'     =>  5,
                        ),
                        'topic' => array(
                            'A_title'       => 6,
                            'A_content'     => 3,
                        ),
                        'reply' => array(
                            'A_title'       => 0,
                            'A_content'     => 3,
                        ),
                    )
                ),
                'activity_stream' => array(
                    'A' => 1
                ),
                'profile_field' => array(
                    'A' => 10
                )
            ),
		);
		$options = get_option( 'expertfinder_options' );
		$this->options = $this->parse_args_r( $options, $this->default_options);
	}

	/**
	 * Setup the actions
	 *
	 * @since Expert_Finder_Settings (0.0.1)
	 * @access private
	 *
	 * @uses remove_action() To remove various actions
	 * @uses add_action() To add various actions
	 */
	private function setup_actions() {
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	/**
	 * Add options page
	 */
	function add_plugin_page() {
		 // This page will be under "Settings"
		add_options_page(
			'Settings Admin',
			'Expert Finder',
			'manage_options',
			'expertfinder-setting-admin',
			array( $this, 'create_admin_page' )
		);
	}

	/**
	 * Options page callback
	 */
	public function create_admin_page()
	{
		?>
		<div class="wrap">
			<h2>Expert Finder Settings</h2>
			<form method="post" action="options.php">
			<?php
				// This prints out all hidden setting fields
				settings_fields( 'expertfinder_option_group' );
				do_settings_sections( 'expertfinder-setting-admin' );
				submit_button();
			?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register and add settings
	 *
	 * @since Expert_Finder_Settings (0.0.1)
	 */
	function page_init() {
		register_setting(
			'expertfinder_option_group', // Option group
			'expertfinder_options', // Option name
			array( $this, 'sanitize' ) // Sanitize
		);

		add_settings_section(
			'setting_section_search_engine', // ID
			'Expert Finder Search Engine configuration', // Title
			array( $this, 'print_search_engine_info' ), // Callback
			'expertfinder-setting-admin' // Page
		);

		add_settings_field(
			'expertfinder_gl', // ID
			'Likes weight', // Title
			array( $this, 'expertfinder_gl_callback' ), // Callback
			'expertfinder-setting-admin', // Page
			'setting_section_search_engine' // Section
		);

		add_settings_field(
			'expertfinder_gk', // ID
			'Comments weight', // Title
			array( $this, 'expertfinder_gk_callback' ), // Callback
			'expertfinder-setting-admin', // Page
			'setting_section_search_engine' // Section
		);

		add_settings_field(
			'expertfinder_gh', // ID
			'Occurences weight', // Title
			array( $this, 'expertfinder_gh_callback' ), // Callback
			'expertfinder-setting-admin', // Page
			'setting_section_search_engine' // Section
		);

		add_settings_field(
			'expertfinder_num_results', // ID
			'Number of results per expert', // Title
			array( $this, 'expertfinder_num_results_callback' ), // Callback
			'expertfinder-setting-admin', // Page
			'setting_section_search_engine' // Section
		);

		add_settings_field(
			'expertfinder_num_experts', // ID
			'Number experts to show', // Title
			array( $this, 'expertfinder_num_experts_callback' ), // Callback
			'expertfinder-setting-admin', // Page
			'setting_section_search_engine' // Section
		);

		add_settings_field(
			'expertfinder_result_types', // ID
			'Result types', // Title
			array( $this, 'expertfinder_result_types_callback' ), // Callback
			'expertfinder-setting-admin', // Page
			'setting_section_search_engine' // Section
		);
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 * @return array
	 */
	public function sanitize( $input )
	{
		return $input;
	}

	 /**
	 * Print the Section text
	 */
	public function print_search_engine_info()
	{
		print 'Configure the different variables of the Expert Search engine here.';
	}

	private function parse_option($option) {
		$tokens = explode('.', $option);
		$id = implode('-', $tokens);
		$name = implode('][', $tokens);
		$value = $this->options;
		foreach($tokens as $key){
			if (isset($value[$key])) {
				$value = $value[$key];
			} else {
				$value = false;
				break;
			}
		}

		return array(
			'id'    => $id,
			'name'  => $name,
			'value' => $value,
			);
	}

	public function checkbox_callback($option, $text)
	{
		$option = $this->parse_option($option);

		?>
		<input id="expertfinder-options-<?php echo $option['id'] ?>" name="expertfinder_options[<?php echo $option['name'] ?>]"
			   type="checkbox" value="1" <?php checked($option['value']) ?>>
		<label for="expertfinder-options-<?php echo $option['id'] ?>"><?php echo $text ?></label>
		<?php
	}

	public function text_callback($option, $text, $type="text", $width = "70%", $attrs = '')
	{

		$option = $this->parse_option($option);

		?>
		<label for="expertfinder-options-<?php echo $option['id'] ?>"><?php echo $text ?></label><br>
		<input style="width:<?php echo $width ?>;" id="expertfinder-options-<?php echo $option['id'] ?>"
			   name="expertfinder_options[<?php echo $option['name'] ?>]"
			   type="<?php echo $type ?>" value="<?php echo $option['value'] ?>" <?php echo $attrs ?>>
		<?php
	}

	public function expertfinder_gl_callback()
	{
		$this->text_callback('G_L', 'Enter the global weight G_L for the number of likes.', 'number', '30%', 'step="0.01"');
	}

	public function expertfinder_gk_callback()
	{
		$this->text_callback('G_K', 'Enter the global weight G_K for the number of comments.', 'number', '30%', 'step="0.01"');
	}

	public function expertfinder_gh_callback()
	{
		$this->text_callback('G_H', 'Enter the global weight G_H for number of occurences of the search term.', 'number', '30%', 'step="0.01"');
	}

	public function expertfinder_num_results_callback()
	{
		$this->text_callback('num_results', 'Enter the number of results to show for each expert.', 'number', '30%');
	}

	public function expertfinder_num_experts_callback()
	{
		$this->text_callback('num_experts', 'Enter the maximum number of experts to show.', 'number', '30%');
	}

	public function expertfinder_result_types_callback()
	{
		$finder = Expert_Finder_Result_Type_Factory::getFinder('activity_stream');
		if ($finder->isAvailable()) {
			echo "<h3>Activity Stream updates</h3>";
			$this->checkbox_callback("result_types.activity_stream.enabled", 'Check to enable searching for activity stream updates.' );
			echo "<p>";
			$this->text_callback('result_types.activity_stream.A', 'Enter base points A when the search term occures in an activty stream update.', 'number', '30%');
			echo "</p>";
		}

		$finder = Expert_Finder_Result_Type_Factory::getFinder('profile_field');
		if ($finder->isAvailable()) {
			echo "<h3>Profile fields</h3>";
			$this->checkbox_callback("result_types.profile_field.enabled", 'Check to enable searching for profile fields.' );
			echo "<p>";
			$this->text_callback('result_types.profile_field.A', 'Enter base points A when the search term occures in a profile field.', 'number', '30%');
			echo "</p>";
		}

		foreach($this->default_options['result_types']['post']['post_types'] as $post_type => $options){

			if (!post_type_exists($post_type))
				continue;
			$object = get_post_type_object($post_type);

			echo "<h3>{$object->labels->name}</h3>";
			$this->checkbox_callback("result_types.post.post_types.{$post_type}.enabled", sprintf('Check to enable searching for %s', $object->labels->name) );
			echo "<p>";
			$this->text_callback("result_types.post.post_types.{$post_type}.A_title",
								 sprintf('Enter base points A when the search term occures in the title of a %s.', $object->labels->singular_name), 'number', '30%');
			echo "<br>";
			$this->text_callback("result_types.post.post_types.{$post_type}.A_content",
								 sprintf('Enter base points A when the search term occures in the content of a %s.', $object->labels->singular_name), 'number', '30%');
			echo "</p>";
		}
	}
}