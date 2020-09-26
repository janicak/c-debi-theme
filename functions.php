<?php

require __DIR__ . '/vendor/autoload.php';

@ini_set( 'upload_max_size' , '64M' );
@ini_set( 'post_max_size', '64M');
@ini_set( 'max_execution_time', '300' );

add_image_size ( 'w-200', 200 );
function custom_image_sizes( $sizes ) {
    return array_merge( $sizes, [
        'w-200' => __( 'w-200' ),
    ] );
}
add_filter( 'image_size_names_choose', 'custom_image_sizes' );

function custom_favicon() {
    echo '<link rel="icon" href="'.get_stylesheet_directory_uri().'/src/common_assets/img/favicon.ico" type="image/x-icon">';
}
add_action('wp_head', 'custom_favicon');

global $c_debi_theme;
$c_debi_theme = new C_DEBI_Theme\Init();
$c_debi_theme->run_hook_loader();