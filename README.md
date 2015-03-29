WP Ordered Taxonomies
=====================

WordPress plugin allowing you to manually sort taxonomies.
This is mainly focused on developers.


Usage:
------

### Frontend

On the frontend you have to explicitly set the `orderby` to `term_order`:

	$ordered_flaggz = get_terms( 'flaggz' , array( 'orderby' => 'term_order' ) );

This behaviour might change in the future.


### WP-Admin

The plugin has two working modes, which are selected through the `PRIVATE_ORDERED_TAXONOMIES` 
constant.  

#### User mode

Make sure the constant `PRIVATE_ORDERED_TAXONOMIES` is either not defined or `false`.
The user will be presented an settings section on the writing options screen, where she 
or he can select which taxonomies can be sorted.

#### Developer Mode

To enable developer mode add this to your wp-config.php:

	define( 'PRIVATE_ORDERED_TAXONOMIES' , true );

For existsing taxonomies like categories and post tags hook into `registered_taxonomy` 
and set the ordered flag to `true`:
	
	function my_registered_taxonomy( $taxonomy ) {
		if ( $taxonomy == 'category' ) {
			global $wp_taxonomies;
			$wp_taxonomies[ $taxonomy ]->ordered = true;
		}
	}
	add_action( 'registered_taxonomy' , array( &$this , 'my_registered_taxonomy' ) );
	

Enable term ordering for a custom taxonomy trough `register_taxonomy()` args: 

	$taxonomy = 'flaggz';
	$post_type = 'post';
	$args = array(
		'hierarchical'	=> false,
		'ordered'		=> true, // 
		'public'		=> true,
	);
	
	register_taxonomy( $taxonomy, $post_type , $args );




To Do:
------
 - [ ] JS/Ajax: consider pagination. Start counting at the lowest order instead of `0`
 - [ ] Test with hierarchical taxonomies
 - [ ] wp-config.php constant 
 - [ ] Frontend: Set default ordering of sorted taxonomies to `term_order`