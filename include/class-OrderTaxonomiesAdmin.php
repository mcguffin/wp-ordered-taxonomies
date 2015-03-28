<?php


if ( ! class_exists( 'OrderTaxonomiesAdmin' ) ):
class OrderTaxonomiesAdmin {
	private static $_instance = null;
	
	/**
	 * Getting a singleton.
	 *
	 * @return object single instance of OrderTaxonomiesAdmin
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
		add_action( 'admin_init' , array( &$this , 'admin_init' ) );
		add_action( "admin_print_scripts" , array( &$this , 'enqueue_assets' ) );
	}
	/**
	 * Admin init
	 */
	function admin_init() {
	}

	/**
	 * Enqueue options Assets
	 */
	function enqueue_assets() {
		wp_enqueue_style( 'order_taxonomies-admin' , plugins_url( '/css/order_taxonomies-admin.css' , dirname(__FILE__) ) );

		wp_enqueue_script( 'order_taxonomies-admin' , plugins_url( 'js/order_taxonomies-admin.js' , dirname(__FILE__) ) );
		wp_localize_script('order_taxonomies-admin' , 'order_taxonomies_admin' , array(
		) );
	}

}

OrderTaxonomiesAdmin::instance();
endif;