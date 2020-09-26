<?php

function item_prepare($post){
    $post->fields = get_fields($post->ID);

    $post->field_meta = array_reduce(
        acf_get_field_groups($post->ID),
        function($acc, $group_key){
            return array_merge(
                $acc,
                acf_get_fields($group_key)
            );
        },[]);

    switch($post->post_type){
        case 'post':
            $post->categories = wp_get_post_categories($post->ID, array('fields' => 'all'));

            $post->tags = wp_get_post_tags($post->ID);

            $expiration_date = get_post_meta($post->ID, '_expiration-date');
            $post->expiration_date = $expiration_date
                ? (date('Ymd', $post->post_meta['_expiration-date'][0]))
                    : false;
            break;

        case 'publication':
            $post->tags = wp_get_post_tags($post->ID);
            break;

        case 'dataset':
            $post->data_projects = (new WP_Query([
                'post_type' => 'data_project',
                'posts_per_page' => -1,
                'meta_query' => [
                    'relation' => 'OR',
                    [
                        'key' => 'data_project_datasets_$_dataset',
                        'compare' => '=',
                        'value' => $post->ID
                    ]
                ]
            ]))->posts;
            break;

        default:
            break;
    }

    global $searchwp;
    $post->search_result_weight = $searchwp->results_weights[$post->ID]['weight'] ?? 0;

}