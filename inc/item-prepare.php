<?php

function item_prepare($post_object, $excerpt){
    global $post;
    $post = $post_object;
    setup_postdata( $post );

    // Get post meta
    if (!isset($post->post_meta)){
        $post->post_meta = get_post_meta($post->ID);
    }

    // Format expiration date if exists
    if (isset($post->post_meta['_expiration-date'])) {
        $post->post_meta['_expiration-date'][0] = date('Y-m-d H:i:s', $post->post_meta['_expiration-date'][0]);
    }

    // Format publication date if exists
    if (isset($post->post_meta['publication_date_published'])) {
        $dateString = $post->post_meta['publication_date_published'][0];
        $pub_date = DateTime::createFromFormat('Ymd', $dateString);
        $post->post_meta['publication_date_published'][0] = $pub_date->format('Y-m-d H:i:s');
    }

    if ($post->post_type == 'post') {
        // Get item categories
        $post->categories = wp_get_post_categories($post->ID, array('fields' => 'all'));
        // Get item tags
        $post->tags = wp_get_post_tags($post->ID);
    } else if ($post->post_type == 'publication') {
        // Get item tags
        $post->tags = wp_get_post_tags($post->ID);
        $post->publication_type = get_the_terms($post->ID, 'publication_type');
        // Get post type name
        $post_type = get_post_type_object($post->post_type);
        $post->post_type_name = $post_type->label;
    } else if ($post->post_type == 'award') {
        if (!isset($post->award_type)) {
            $post->award_type = get_field('award_type', $post->ID);
        }
        $post->post_type_name = 'Awards';
    } else {
        // Get post type name
        $post_type = get_post_type_object($post->post_type);
        $post->post_type_name = $post_type->label;
    }

    // Get the post excerpt
    if ($excerpt){
        $html = '';
        if (isset($_GET['s']) && $_GET['s']){
            /*if( function_exists( 'searchwp_term_highlight_get_the_excerpt_global' ) ) {
                $html =  searchwp_term_highlight_get_the_excerpt_global($post->ID );
            } else {
                $html = '';
            }*/
            if (strpos($post->post_excerpt, '<mark class="searchwp-highlight">') !== false){
                $stripped_content = wp_strip_all_tags($post->post_excerpt);
                $words = explode(" ", $stripped_content);
                $count = count($words);
                if ($count > 5) {
                    $html = $post->post_excerpt;
                }
            }
        }
        $post->excerpt = $html;
    }

    // Add span around last word of title
    if (!strpos($post->post_title, 'searchwp-highlight')){
        $title_parts = explode(' ', $post->post_title);
        $last_index = count($title_parts) - 1;
        $title_parts[$last_index] = '<span class="last-word">'.$title_parts[$last_index].'</span>';
        $post->post_title = implode(' ', $title_parts);
    }

    // Add SearchWP result weight to post
    global $searchwp;
    if ($searchwp && isset($searchwp->results_weights)){
        $weight = 0;
        if (isset($searchwp->results_weights[$post->ID]) && isset($searchwp->results_weights[$post->ID]['weight'])){
            $weight = $searchwp->results_weights[$post->ID]['weight'];
        }
        $post->result_weight = $weight;
    }

    // IF DATASETS, LOAD DATASET PROJECTS TO GLOBAL OBJECT
    global $dataset_projects;
    global $data_project;
    if (!$dataset_projects && $post->post_type == 'dataset'){
        $dataset_projects = array();

        $data_project = array();

        $projects = get_posts(array(
            'post_type' => 'data_project',
            'numberposts' => -1,
        ));
        foreach ($projects as $project){
            $project->post_meta = get_post_meta($project->ID);
            if (isset($project->post_meta['data_project_datasets'])){
                $count = $project->post_meta['data_project_datasets'][0];
                if ($count){
                    for ($i = 0; $i < intval($count); $i++){
                        $dataset_id = $project->post_meta['data_project_datasets_'.$i.'_dataset'][0];
                        $dataset_projects[$dataset_id] = $project->ID;
                        $data_project[$project->ID] = $project;
                    }
                }
            }
        }
    }
    if ($post->post_type == 'dataset' && is_array($data_project)){
        if (isset($dataset_projects[$post->ID]) && isset($data_project[$dataset_projects[$post->ID]])) {
            $post->project = $data_project[$dataset_projects[$post->ID]];
        } else { $post->project = null; }
    }

}