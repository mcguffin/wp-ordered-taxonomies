<?php


if ( ! class_exists( 'OrderTaxonomiesSettings' ) ):
class OrderTaxonomiesSettings {
	private static $_instance = null;
	
	/**
	 * Setup which to WP options page the Rainbow options will be added.
	 * 
	 * Possible values: general | writing | reading | discussion | media | permalink
	 */
	private $optionset = 'writing'; // writing | reading | discussion | media | permalink

	/**
	 * Getting a singleton.
	 *
	 * @return object single instance of XxxxxSettings
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	}

	/**
	 * Private constructor
	 */
	private function __construct() {
		add_action( 'admin_init' , array( &$this , 'register_settings' ) );
		
		add_option( 'ordered_taxonomies' , array() , '' , true );
	}

	/**
	 * Enqueue options Assets
	 */
	function enqueue_assets() {

	}
	


	/**
	 * Setup options page.
	 */
	function register_settings() {
		$settings_section = 'ordered_taxonomies_settings';
		// more settings go here ...
		register_setting( $this->optionset , 'ordered_taxonomies' , array( &$this , 'sanitize_ordered_taxonomies' ) );

		add_settings_section( $settings_section, __( 'Ordered Terms',  'wp-ordered-taxonomies' ), array( &$this, 'ordered_taxonomies_description' ), $this->optionset );
		// ... and here
		add_settings_field(
			'ordered_taxonomies',
			__( 'Taxonomies to order',  'wp-ordered-taxonomies' ),
			array( $this, 'ordered_taxonomies_ui' ),
			$this->optionset,
			$settings_section
		);
	}

	/**
	 * Print some documentation for the optionset
	 */
	public function ordered_taxonomies_description() {
		?>
		<div class="inside">
			<p><?php _e( 'Select which Taxonomies will be manually sorted.' , 'wp-ordered-taxonomies' ); ?></p>
		</div>
		<?php
	}
	
	/**
	 * Output Theme selectbox
	 */
	public function ordered_taxonomies_ui() {
		$setting_name = 'ordered_taxonomies';
		$setting = (array) get_option($setting_name);
		$taxonomies = get_taxonomies( array( 'show_ui' => true ), 'objects' );

		foreach ( $taxonomies as $taxonomy ) {
			?><p><label for="check-<?php esc_attr_e( $taxonomy->name ) ?>"><?php
				?><input <?php checked( in_array($taxonomy->name, $setting ) ) ?> id="check-<?php esc_attr_e( $taxonomy->name ) ?>" type="checkbox" name="<?php esc_attr_e($setting_name) ?>[]" value="<?php esc_attr_e( $taxonomy->name ) ?>" /><?php
				echo $taxonomy->labels->name
			?></label></p><?php
		}
	}
	

	/**
	 * Sanitize value of setting_1
	 *
	 * @return string sanitized value
	 */
	function sanitize_ordered_taxonomies( $value ) {	
		// do sanitation here!
		$return = array();
		$value = (array) $value;
		foreach ( $value as $taxo )
			if ( taxonomy_exists( $taxo ) ) 
				$return[] = $taxo;
		return $return;
	}
}

OrderTaxonomiesSettings::instance();
endif;