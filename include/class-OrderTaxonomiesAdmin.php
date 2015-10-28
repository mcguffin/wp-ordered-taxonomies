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
		add_action( 'load-edit-tags.php' , array( &$this , 'enqueue_assets' ) );
		add_action( 'load-edit-tags.php' , array( &$this , 'set_default_orderby' ) );
		add_action( 'wp_ajax_order-terms' , array( &$this , 'ajax_order_terms' ) );

		add_action( 'registered_taxonomy' , array( &$this , 'registered_taxonomy' ) , 30 ,3 );
	}
	
	/**
	 * Setup sort column
	 *
	 * @action 'registered_taxonomy'
	 */
	function registered_taxonomy( $taxonomy , $object_type , $args ) {
		global $wp_taxonomies;
		if ( $wp_taxonomies[ $taxonomy ]->ordered ) {
			add_filter( "manage_{$taxonomy}_custom_column" , array( &$this , 'sortkey_column_content' ) , 10 , 3 );
			add_filter( "manage_edit-{$taxonomy}_columns" , array( &$this , 'add_sortkey_column' ) );
			add_filter( "manage_edit-{$taxonomy}_sortable_columns" , array( &$this , 'add_sorted_sortkey_column' ) );
		}
	}
	/**
	 * Sort column content
	 *
	 * @filter 'manage_{$taxonomy}_custom_column'
	 */
	function sortkey_column_content( $column_content, $column_name, $term_id ) {
		if ( $column_name == 'term_order' )	{
			global $wpdb;
			$term = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->terms WHERE term_id = %d" , $term_id ) );
			return '<span class="order-icon large handle">'.$term->term_order . '</span>';
		}
		return $column_content;
	}
	/**
	 * Add sort column
	 *
	 * @filter 'manage_edit-{$taxonomy}_columns'
	 */
	function add_sortkey_column( $columns ) {
		$new_column = array( 
			'term_order' =>  '<span class="dashicons dashicons-sort"></span>',//__('#','wp-ordered-taxonomies'),
		);
		return $new_column + $columns;
	}
	/**
	 * Make sort column a sortable column
	 *
	 * @filter 'manage_edit-{$taxonomy}_sortable_columns'
	 */
	function add_sorted_sortkey_column( $sortable_columns ) {
		$sortable_columns['term_order'] = 'term_order';
		return $sortable_columns;
	}
	
	/**
	 * Make term_order the Default order for sorted taxonomy edit screens
	 *
	 * @action 'load-edit-tags.php'
	 */
	function set_default_orderby() {
		global $wp_taxonomies;
		if ( 	! isset( $_REQUEST['orderby'] ) &&
				( $taxonomy = get_current_screen()->taxonomy ) && 
				( $wp_taxonomies[ $taxonomy ]->ordered )
			)
			$_REQUEST['orderby'] = 'term_order';
	}

	/**
	 * Ajax reqest for sorting columns
	 *
	 * @action 'wp_ajax_order-terms'
	 */
	function ajax_order_terms() {
		// check request
		$response = array('success' => false , 'error' => '' , 'terms_order' => array() );
		if ( isset( $_REQUEST['_wpnonce'] , $_REQUEST['order_terms'] ) ) {
			// check nonce & caps
			if ( wp_verify_nonce( $_REQUEST['_wpnonce'] , 'order-terms' ) && current_user_can( 'manage_categories' ) ) {
				global $wpdb;
				$result = false;
				foreach ( $_REQUEST['order_terms'] as $term_id => $sortkey ) {
					if ( intval($term_id) )
						$result = $wpdb->update( $wpdb->terms , 
							array( 'term_order' => $sortkey ) , 
							array('term_id' => $term_id ) , 
							array( '%d' ) , 
							array( '%d' ) );
					if ( $result !== false ) {
						$response['terms_order'][$term_id] = intval($sortkey);
					} else {
						break;
					}
				}
				$response['success'] = !!$result;
				// should return array( $term_id => $term_order )
			} else {
				$response['error'] = __('Insufficient Permission');
			}
		} else {
			$response['error'] = __('Bad request');
		}
		header( 'Content-Type: application/json' );
		echo json_encode($response);
		exit('');
	}
	
	/**
	 * Admin init
	 */
	function admin_init() {
	}

	/**
	 * Enqueue options Assets
	 *
	 * @action 'load-edit-tags.php'
	 */
	function enqueue_assets() {
		wp_enqueue_style( 'order_taxonomies-admin' , plugins_url( '/css/order_taxonomies-admin.css' , dirname(__FILE__) ) );

		wp_enqueue_script( 'order_taxonomies-admin' , plugins_url( 'js/order_taxonomies-admin.js' , dirname(__FILE__) ) , array( 'jquery' , 'jquery-ui-core' , 'jquery-ui-sortable') );
		wp_localize_script('order_taxonomies-admin' , 'order_taxonomies_admin' , array(
			'wpnonce' => wp_create_nonce( 'order-terms' ),
		) );
	}

}

OrderTaxonomiesAdmin::instance();
endif;