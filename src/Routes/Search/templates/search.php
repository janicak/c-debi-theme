<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * The template for displaying a Search Results Page
 */

$us_layout = US_Layout::instance();

us_register_context_layout( 'header' );
get_header();

us_register_context_layout( 'main' );


/** Derive Page Title from URL search parameters **/

$page_title_parts = [];

$get_param_config =[
    'type' => [
        'type' => 'post_type_slug_to_plural',
        'prepend_text' => 'Post Type: '
    ],
    'award_type' => [
        'type' => 'term_slug_to_plural',
        'taxonomy' => 'award_type',
        'acf_field' => 'award_type_plural_name',
        'prepend_text' => 'Award Type: '
    ],
    'publication_type' => [
        'type' => 'term_slug_to_plural',
        'taxonomy' => 'publication_type',
        'acf_field' => 'publication_type_plural_name',
        'prepend_text' => 'Publication Type: '
    ],
    'instrument' => [
        'type' => 'term_id_to_singular',
        'taxonomy' => 'instrument',
        'prepend_text' => 'Instrument Type: '
    ],
    'parameter' => [
        'type' => 'term_id_to_singular',
        'taxonomy' => 'parameter',
        'prepend_text' => 'Parameter Type: '
    ],
    'category' => [
        'type' => 'term_slug_to_singular',
        'taxonomy' => 'category',
        'prepend_text' => 'Post Category: '
    ],
    'person_id' => [
        'type' => 'post_id_to_title',
        'prepend_text' => 'Person: '
    ],
    's' => [
        'type' => 'text_search',
        'prepend_text' => 'Text Search: '
    ],
];

foreach( $get_param_config as $get_param => $param_config) {

    [ 'type' => $param_type, 'prepend_text' => $prepend_text ] = $param_config;

    if( isset( $_GET[ $get_param ] ) && $_GET[ $get_param ] ) {
        $param_value = $_GET[ $get_param ];

        switch( $param_type ) {

            case 'text_search':
                $page_title_parts[$get_param] = $prepend_text . '"' . str_replace('&quot;', '', get_search_query()) .'"';
                break;

            case 'term_slug_to_plural':
                [ 'taxonomy' => $taxonomy, 'acf_field' => $field_name ] = $param_config;
                $term = get_term_by( 'slug', $param_value, $taxonomy );
                $plural_name = get_field( $field_name, $term );
                $page_title_parts[$get_param] = $prepend_text . $plural_name;
                break;

            case 'term_id_to_singular':
                [ 'taxonomy' => $taxonomy, 'prepend_text' => $prepend_text ] = $param_config;
                $term = get_term( intval($param_value), $taxonomy );
                $page_title_parts[$get_param] = $prepend_text . $term->name;
                break;

            case 'term_slug_to_singular':
                [ 'taxonomy' => $taxonomy, 'prepend_text' => $prepend_text ] = $param_config;
                $term = get_term_by( 'slug', $param_value, $taxonomy );
                $page_title_parts[$get_param] = $prepend_text . $term->name;
                break;

            case 'post_type_slug_to_plural':
                $post_type = get_post_type_object( $param_value );
                $page_title_parts[$get_param] = $prepend_text . $post_type->label;
                break;

            case 'post_id_to_title':
                $post = get_post(intval($param_value));
                $post_title = $post ? $post->post_title : $param_value;
                $page_title_parts[$get_param] = $prepend_text . $post_title;
                break;

            default:
                break;
        }

    }
}

if (
    count($page_title_parts) > 1
    && isset( $page_title_parts['type'])
    && !(isset($page_title_parts['s']) && count($page_title_parts) === 2)
){
    unset($page_title_parts['type']);
}

if (
    count($page_title_parts) === 1
    && (isset($page_title_parts['type']))
){
    $page_title_parts['type'] = str_replace($get_param_config['type']['prepend_text'], '', $page_title_parts['type']);
}

$page_title = implode("; ", $page_title_parts);

//TODO: replace markup with whatever new crap Zephyr does, or just drop it
?>
<div class='l-titlebar size_medium color_alternate'>
	<div class='l-titlebar-h'><div class='l-titlebar-content'>
		<h1 itemprop='headline'><?=$page_title;?></h1>
	</div></div>
</div>
<div class="l-main">
	<div class="l-main-h i-cf">

		<main class="l-content"<?php echo ( us_get_option( 'schema_markup' ) ) ? ' itemprop="mainContentOfPage"' : ''; ?>>
			<section class="l-section<?php echo ( us_get_option( 'row_height' ) == 'small' ) ? ' height_small' : ''; ?>">
				<div class="l-section-h i-cf">
					<?php us_load_template( 'templates/listing-search' ); ?>
				</div>
			</section>
		</main>

	</div>
</div>

<?php

us_register_context_layout( 'footer' );
get_footer();

