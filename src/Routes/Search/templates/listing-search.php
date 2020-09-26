<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

global $c_debi_theme;
$query = C_DEBI\Search\Search::url_query();

// Output No results
if ( ! count($query->posts) ) {
	echo '<h4 class="grid-none">' . us_translate( 'No results found.' ) . '</h4>';
	return;
}

// Search Filter Dropdown Options:
function zc_create_dropdown_options($type_objects, $value_prop, $label_prop, $is_selected_callback){
    $html = '<option value="">All</option>';
    foreach($type_objects as $type_object){
        $value = $type_object->{$value_prop};
        $label = $type_object->{$label_prop};
        $selected = $is_selected_callback($value) ? 'selected' : '';
        $html .= '<option value="' . $value . '" ' . $selected . '">'.$label.'</option>';
    }
    return $html;
}

// --> Post Categories
$post_category_objects = get_categories(['hierarchical' => false]);
$post_category_is_selected_callback = function($value){
    return isset($query->query['category_name']) && $query->query['category_name'] === $value;
};
$post_category_options_html = zc_create_dropdown_options( $post_category_objects,
    'slug','name',
    $post_category_is_selected_callback
);

// --> Post Types
$post_type_objects = array_filter( get_post_types('', 'objects'), function($post_type) {
    return in_array($post_type->name, ['award', 'publication', 'page', 'newsletter', 'post', 'protocol', 'data_project', 'dataset']);
});
$post_type_is_selected_callback = function($value){
    return
        (isset($_GET['type']) && $_GET['type'] === $value)
        || ($value == 'post' && isset($query->query['category_name']));
};
$post_type_options_html = zc_create_dropdown_options($post_type_objects,
    'name', 'label',
    $post_type_is_selected_callback
);

// --> Publication Types
$publication_type_objects = get_terms(['taxonomy' => 'publication_type']);
$publication_type_is_selected_callback = function($value){
    return isset($_GET['publication_type']) && $_GET['publication_type'] === $value;
};
$publication_type_options_html = zc_create_dropdown_options($publication_type_objects,
    'slug', 'name',
    $publication_type_is_selected_callback
);

// --> Award Types
$award_type_objects = get_terms(['taxonomy' => 'award_type']);
$award_type_is_selected_callback = function($value){
    return isset($_GET['award_type']) && $_GET['award_type'] === $value;
};
$award_type_options_html = zc_create_dropdown_options($award_type_objects,
    'slug', 'name',
    $award_type_is_selected_callback
);

// Search Result Filter Options
$post_types = array();
$publication_types = array();
$award_types = array();
$post_cats = array();
foreach ($query->posts as $post) {
	array_push($post_types, $post->post_type);
	if (!$post->post_meta){
		$post->post_meta = get_post_meta($post->ID);
	}
	if (isset($post->post_meta['award_type'])){
	    $post->award_type = get_field('award_type', $post->ID);
		array_push($award_types, strtolower($post->award_type[0]->name));
	}
	if (isset($post->post_meta['publication_type'])){
        $post->publication_type = get_the_terms($post->ID,'publication_type');
		array_push($publication_types, strtolower($post->publication_type[0]->name));
	}
	$cats = get_the_category($post->ID);
	if (count($cats)){
		foreach($cats as $cat){
			array_push($post_cats, $cat->slug);
		}
	}
}
$post_cats = array_unique($post_cats);
sort($post_cats);
$expired = false;
if (in_array('expired', $post_cats)){
	$expired = true;
}
$post_types = array_unique($post_types);
sort($post_types);
$active_post_type_objects = array();
foreach ($post_types as $post_type){
	array_push($active_post_type_objects, $post_type_objects[$post_type]);
}
$publication_types = array_unique($publication_types);
sort($publication_types);
$award_types = array_unique($award_types);
sort($award_types);

//MCJ: Custom Filter Bar HTML
$filter_html = '<div class="filters-container"><div class="filters hidden">';
if ( count( $active_post_type_objects ) > 1 ) {
	$filter_html .= '<div class="filters-list post-types hidden"><span>Type</span><div class="filters-items">';
	$filter_html .= '<div class="filters-item active" data-filter="*">All</div>';
	foreach ($active_post_type_objects as $post_type) {
		$filter_html .= '<div class="filters-item" data-filter="' . $post_type->name . '">' . $post_type->label . '</div>';
	}
	$filter_html .= '</div></div>';
}
if ( count( $post_cats ) > 1 ) {
	$filter_html .= '<div class="filters-list post-cats hidden"><div class="label-area"><span>Post Category</span>';
	if ($expired){
		$filter_html .= '<span class="show-expired" /><input type="checkbox" name="show-expired" value="show-expired" />Show expired posts</span>';
	}
	$filter_html .= '</div><div class="filters-items"><div class="filters-item active" data-filter="*">All</div>';
	foreach ($post_cats as $post_cat) {
		if ($post_cat == 'expired'){
			$expired = true;
		}
		$post_cat_display = '';
		foreach ($post_category_objects as $category){
			if ($post_cat == $category->slug){
				$post_cat_display = $category->name;
				break;
			}
		}
		$filter_html .= '<div class="filters-item" data-filter="' . $post_cat . '">' . $post_cat_display . '</div>';
	}
	$filter_html .= '</div></div>';
}
if ( count($publication_types) > 1) {
	$filter_html .= '<div class="filters-list publication-types hidden"><span>Publication Type</span><div class="filters-items">';
	$filter_html .= '<div class="filters-item active" data-filter="*">All</div>';
	foreach ( $publication_types as $pub_type ) {
		$pub_type_slug = strtolower(str_replace(' ', '-', $pub_type));
		$pub_type_label = ucwords($pub_type);
		$filter_html .= '<div class="filters-item" data-filter="' . $pub_type_slug . '">' . $pub_type_label . '</div>';
	}
	$filter_html .= '</div></div>';
}
if ( count($award_types) > 1) {
	$filter_html .= '<div class="filters-list award-types hidden"><span>Award Type</span><div class="filters-items">';
	$filter_html .= '<div class="filters-item active" data-filter="*">All</div>';
	foreach ( $award_types as $award_type ) {
		$award_type_slug = strtolower(str_replace(' ', '-', str_replace(' &amp;', '', $award_type)));
		$award_type_label = str_replace('Rcn', 'RCN', ucwords($award_type));
		$filter_html .= '<div class="filters-item" data-filter="' . $award_type_slug . '">' . $award_type_label . '</div>';
	}
	$filter_html .= '</div></div>';
}
$filter_html .= '</div>';

// MCJ: Result Sort
$filter_html .= '<div class="sort"><span>Order</span><div class="sort-items">';
if (isset($_GET['s']) && $_GET['s']) {
	$filter_html .= '<div class="sort-item active" data-sort="search">Search order</div>';
	$filter_html .= '<div class="sort-item" data-sort="desc">Date Desc</div>';
} else {
	$filter_html .= '<div class="sort-item active" data-sort="desc">Date Desc</div>';
}
$filter_html .= '<div class="sort-item" data-sort="asc">Date Asc</div>';
$filter_html .= '<div class="sort-item" data-sort="title-asc">Title Asc</div>';
$filter_html .= '<div class="sort-item" data-sort="title-desc">Title Desc</div>';
$filter_html .= '</div></div></div>';

// MCJ: Search Form
?>
	<form role="search" id="search-header" method="get" class="search-form" action="<?php echo home_url( '/' ); ?>">
		<label>
			<span><?php echo _x( 'Query:', 'label' ) ?></span>
			<input type="search" class="search-field"
				   placeholder="<?php echo esc_attr_x( 'Search …', 'placeholder' ) ?>"
				   value="<?php echo get_search_query() ?>" name="s"
				   title="<?php echo esc_attr_x( 'Search for:', 'label' ) ?>" />
		</label>
		<label id="post-type-select">
			<span>Type: </span>
			<select name="type">
				<?php echo $post_type_options_html; ?>
			</select>
		</label>
		<label id="publication-type-select" class="hidden">
			<span>Publication Type: </span>
			<select  name="publication_type">
				<?php echo $publication_type_options_html; ?>
			</select>
		</label>
		<label id="award-type-select" class="hidden">
			<span>Award Type: </span>
			<select name="award_type">
				<?php echo $award_type_options_html; ?>
			</select>
		</label>
		<label id="post-category-select" class="hidden">
			<span>Post Category: </span>
			<select name="category">
				<?php echo $post_category_options_html; ?>
			</select>
		</label>
		<input type="submit" class="search-submit"
			   value="<?php echo esc_attr_x( 'Submit', 'submit button' ) ?>" />
	</form>
<?php

// Load listing Start
// Output the Grid semantics
echo '<div class="grid">';
echo $filter_html;
echo $c_debi_theme->blade_runner->run('Frontend.components.loader',[]);
echo '<div class="grid-list hidden">';

// RENDER RESULTS
foreach ($query->posts as $post){
    $entity = new C_DEBI_Theme\Entity($post);
    echo $c_debi_theme->blade_runner->run('Frontend.layout.entity_search_result', [ 'entity' => $entity, 'show_excerpt' => true, 'title_link_modal' => true ]);
}

// RENDER MODAL CONTENT
foreach ($query->posts as $post){
    $has_modal = !in_array($post->post_type, array('newsletter', 'protocol'));
	if ($has_modal){
		$html = '<div class="post hidden" data-id="'.$post->ID.'">';
		$html .= $c_debi_theme->blade_runner->run('Frontend.layout.entity_full', [ 'entity' => $entity ]);
		$html .= '</div>';
		echo $html;
	}
}
