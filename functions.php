<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementorChild
 */

/**
 * Load child theme css and optional scripts
 *
 * @return void
 */
function hello_elementor_child_enqueue_scripts() {
	wp_enqueue_style(
		'hello-elementor-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		[
			'hello-elementor-theme-style',
		],
		'1.0.0'
	);
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_enqueue_scripts' );


/** Add shortcodes for copyright automatic year update using [year] **/
function year_shortcode() {
  $year = date('Y');
  return $year;
}
add_shortcode('year', 'year_shortcode');

//Change WP logo for login or reset password
function my_login_logo_one() { 
?> 
<style type="text/css"> 
body.login div#login h1 a {
 background-image: url(https://www.webiva.ch/wp-content/uploads/WEBIVA-Favicon.svg);
padding-bottom: 10px; 
} 
</style>
 <?php 
} add_action( 'login_enqueue_scripts', 'my_login_logo_one' );

/**
 * Custom admin login header link
 */
function custom_login_url() {
    return home_url( '/' );
}
add_filter( 'login_headerurl', 'custom_login_url' );


//Hides elementor template library from menu for user with editor permission
function remove_menus(){
	// get current login user's role
	$roles = wp_get_current_user()->roles;

	// test role
	if( !in_array('editor',$roles)){
	return;
	}

	//remove menu from site backend.
	remove_menu_page( 'edit.php?post_type=elementor_library' ); // Elementor Templates
	remove_menu_page( 'elementor' ); // Elementor
}
add_action( 'admin_menu', 'remove_menus' , 100 );
