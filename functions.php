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
	if( !in_array('contributor',$roles)){
	return;
	}
	

	$ptype = 'elementor_library';
	
	//remove menu from site backend.
	remove_menu_page( 'edit-comments.php' ); // Comments 
	remove_menu_page( 'tools.php' ); // Tools
// 	remove_menu_page( 'edit-tags.php?taxonomy=elementor_library_category' ); // elementor category
// 	remove_menu_page( 'edit.php?post_type=elementor_library&tabs_group=library' ); // elementor category
	remove_menu_page( 'edit.php?post_type=elementor_library' ); // Elementor Templates
	remove_menu_page( 'elementor' ); // Elementor
// 	remove_submenu_page( 'edit.php?post_type={$ptype}', 'edit-tags.php?taxonomy=elementor_library_category&amp;post_type={$ptype}' );
	remove_menu_page( '/#menu-posts-elementor_library');
	
}
add_action( 'admin_menu', 'remove_menus' , 999 );


// Maps posts capability to elementor_library post type capabilities  
function editing_elementor_library_capability($args, $post_type)
{
  if ('elementor_library' === $post_type) {
    $args['map_meta_cap'] = true;
    $args['capability_type'] = 'elementor_library'; // You could set your capability type name here
  }
  return $args;
}
add_filter('register_post_type_args', 'editing_elementor_library_capability', 10, 2);

// Add content to empty cart page
add_action( 'woocommerce_cart_is_empty', 'show_content_empty_cart' );

function show_content_empty_cart() {
   echo "<style>.empty-cart-template {display:block !important}</style>";
}



// Auto uncheck "Ship to a different address"
add_filter( 'woocommerce_ship_to_different_address_checked', '__return_false' );

// Add "+" and "-" button to shop
function appel_styles_et_scripts() {
  /*wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/quantity-input-V1.css',false,'1.1','all');*/
  wp_enqueue_script( 'mon-fichier-javascript', get_stylesheet_directory_uri() . '/woocommerce/quantity-input-V1.js', array(), '1.0.0', true );
}

add_action( 'wp_enqueue_scripts', 'appel_styles_et_scripts' );

wp_enqueue_style('styleCSS', get_stylesheet_directory_uri(). '/woocommerce/quantity-input-V1.css');


/* cart-auto-update-V1 script with jQuery as a dependency, enqueued in the footer */
add_action('wp_enqueue_scripts', 'tutsplus_enqueue_custom_js');
function tutsplus_enqueue_custom_js() {
    wp_enqueue_script('custom', get_stylesheet_directory_uri().'/woocommerce/cart-auto-update-V1.js', 
    array('jquery'), false, true);
}



/**
 * Rename "home" in woocommerce breadcrumb
 */
add_filter( 'woocommerce_breadcrumb_defaults', 'wcc_change_breadcrumb_home_text' );
function wcc_change_breadcrumb_home_text( $defaults ) {
    // Change the breadcrumb home text from 'Home' to 'Shop'
	$defaults['home'] = 'Shop';
	return $defaults;
}

/**
 * Replace the home link URL
 */
add_filter( 'woocommerce_breadcrumb_home_url', 'woo_custom_breadrumb_home_url' );
function woo_custom_breadrumb_home_url() {
    return 'https://boulangerie.webivadev.ch/boutique/';
}


// /**
//  * Chanages the "# in stock" text to "#" for WooCommerce
//  */
// add_filter( 'woocommerce_get_availability_text', 'webiva_custom_get_availability_text', 99, 2 );
// function webiva_custom_get_availability_text( $availability, $product ) {
//    $stock = $product->get_stock_quantity();
//    if ( $product->is_in_stock() && $product->managing_stock() ) $availability = '' . $stock;
//    return $availability;
// }


add_filter( 'woocommerce_get_price_html', 'change_variable_products_price_display', 10, 2 );
function change_variable_products_price_display( $price, $product ) {

    // Only for variable products type
    if( ! $product->is_type('variable') ) return $price;

    $prices = $product->get_variation_prices( true );

    if ( empty( $prices['price'] ) )
        return apply_filters( 'woocommerce_variable_empty_price_html', '', $product );

    $min_price = current( $prices['price'] );
    $max_price = end( $prices['price'] );
    $prefix_html = '<span class="price-prefix">' . __('A partir de: ') . '</span>';

    $prefix = $min_price !== $max_price ? $prefix_html : ''; // HERE the prefix

    return apply_filters( 'woocommerce_variable_price_html', $prefix . wc_price( $min_price ) . $product->get_price_suffix(), $product );
}

//Removes links
add_filter( 'woocommerce_product_is_visible','product_invisible');
function product_invisible(){
    return false;
}

// remove single product page link
remove_action( 'woocommerce_before_shop_loop_item','woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_after_shop_loop_item','woocommerce_template_loop_product_link_close', 5 );

// hide single product page completely
add_filter( 'woocommerce_register_post_type_product','hide_product_page',12,1);
function hide_product_page($args){
  $args["publicly_queryable"]=false;
  $args["public"]=false;
  return $args;
}

// Hides default woocommerce notice when product added to cart
add_filter( 'wc_add_to_cart_message_html', '__return_false' );
// Hides default woocommerce notice when product removed from cart
add_filter( 'woocommerce_cart_item_removed_notice_type', '__return_false' );
