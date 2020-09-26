<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Template to show single page or any post type
 */

$us_layout = US_Layout::instance();

us_register_context_layout( 'header' );
get_header();

us_register_context_layout( 'main' );

global $post;

if (in_array($post->post_type, ['page', 'newsletter']) || in_category('spotlight', $post)) {

    us_load_template('templates/single-page');

} else {

    us_load_template('templates/single-entity');

}

us_register_context_layout( 'footer' );
get_footer()

?>
