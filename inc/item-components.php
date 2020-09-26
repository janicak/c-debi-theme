<?php

function item_components($post){
    $components = array(
        'title' => item_title($post),
        'date' => item_date($post),
        'organization' => item_organization($post),
        'types' => item_types($post),
    );
    switch($post->post_type){
        case 'dataset':
            require_once('item-components-dataset.php');
            $components['people'] = dataset_people($post);
            break;
        case 'publication':
            require_once('item-components-publication.php');
            $components['people'] = publication_people($post);
            break;
        case 'award':
            require_once('item-components-award.php');
            $components['people'] = award_participants($post);
            break;
        case 'data_project':
            require_once('item-components-data_project.php');
            $components['people'] = project_people($post);
            break;
        default:
            $components['people'] = item_people($post);
            break;
    }
    return $components;
}

function item_date($post){
    $html = '';
    $update_types = array('page');

    if (isset($post->post_meta['_expiration-date']) &!$post->search_layout){
        $label = 'Expires: ';
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $post->post_meta['_expiration-date'][0]);
        $type = 'expires';
    } else if (isset($post->post_meta['publication_date_published'])) {
        $label = 'Published: ';
        $date = DateTime::createFromFormat('Ymd', $post->post_meta['publication_date_published'][0]);
        $type = 'published';
    } else if (isset($post->post_meta['award_start_date'])){
        $label = 'Award Dates: ';
        $date = array();
        $date[] = DateTime::createFromFormat('Ymd', $post->post_meta['award_start_date'][0]);
        $date[] = DateTime::createFromFormat('Ymd', $post->post_meta['award_end_date'][0]);
        $type = 'award-dates';
    } else if (in_array($post->post_type, $update_types)){
        $label = 'Updated: ';
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $post->post_modified);
        $type = 'updated';
    } else if ( isset($post->post_meta['dataset_modified_date']) || isset($post->post_meta['data_project_modified_date']) ){
        $label = 'Last Modified: ';
        $value = isset($post->post_meta['dataset_modified_date']) ? $post->post_meta['dataset_modified_date'][0] : $post->post_meta['data_project_modified_date'][0];
        $date = '';
        if (preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $value)) {
            $date = DateTime::createFromFormat('Y-m-d', $value);
        } else if (preg_match('/^[0-9]{8}$/', $value)) {
            $date = DateTime::createFromFormat('Ymd', $value);
        }
        $type = 'modified';
    } else {
        $label = 'Posted: ';
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $post->post_date);
        $type = 'posted';
    }

    if ($label && $date){
        $html = '<div class="date-info">';
        if (is_array($date)){
            $display_date = $date[0]->format('F j, Y');
            if ($date[1]){
                $display_date .= ' — '.$date[1]->format('F j, Y');
            }
        } else {
            $display_date = $date->format('F j, Y');
        }
        $html .= $label . '<span class="date">' . $display_date . '</span>';
        $html .= '</div>';
    }

    if ($date){
        if (is_array($date) && $date[1]){
            $date = $date[1]->format('Y-m-d H:i:s');
        } else if (is_object($date)) {
            $date = $date->format('Y-m-d H:i:s');
        }
    }

    return array('type' => $type, 'sort' => $date, 'display' => $html );
}

function item_organization($post){

    $org = array(
        'link' => '',
        'no-link' => ''
    );
    $base_url = get_site_url();
    $organization = get_field('post_organization', $post->ID);

    if ($organization && count($organization)){
        $html = '<div class="organization">';
        $html .= '<a href="'.$base_url.'/?s=&organization='.$organization[0]->slug.'">'.$organization[0]->name.'</a>';
        $html .= '</div>';
        $org['link'] = $html;
        $html = '<div class="organization">';
        $html .= $organization[0]->name;
        $html .= '</div>';
        $org['no-link'] = $html;
    } else if (isset($post->post_meta['publication_publisher_title'])){
        $org['no-link'] = '<div class="organization">'.$post->post_meta['publication_publisher_title'][0].'</div>';
        $org['link'] = $org['no-link'];
    }

    return $org;
}

function item_types($post){

    $types = array(
        'slugs' => array(),
        'display' => ''
    );
    $types['slugs'][] = $post->post_type;
    if (isset($post->categories)){
        foreach($post->categories as $cat){
            $types['slugs'][] = $cat->slug;
        }
    }
    switch($post->post_type){
        case 'page':
            $types['slugs'][] = 'page-type';
            break;
        case 'publication':
            if (isset($post->publication_type)){
                $types['slugs'][] = $post->publication_type[0]->slug;
            }
            break;
        case 'award':
            if (!property_exists($post, 'award_type')){
                $post->award_type = get_field('award_type', $post->ID);
            }
            $types['slugs'][] = $post->award_type[0]->slug;
            break;
        default:
            break;
    }

    $html = '';
    $base_url = get_site_url();
    $html .= '<div class="type">';
    if (isset($post->categories)){
        $post_cat_links = array();
        foreach ($post->categories as $cat){
            array_push($post_cat_links, '<span class="'.$cat->slug.'"><span class="icon"></span><a href="'.$base_url.'/?s=&category='.$cat->slug.'">'.$cat->name.'</a></span>');
        }
        $html .= implode('<span class="separator">, </span>', $post_cat_links);
    } else {
        $html .= '<span class="'.$post->post_type.'"><span class="icon"></span><a href="'.$base_url.'/?s=&type='.$post->post_type.'">'.$post->post_type_name.'</a>';
        if (isset($post->publication_type)){
            $pub_type = $post->publication_type[0]->name;
            $pub_type_slug = $post->publication_type[0]->slug;
            $html .= '<span class="separator"> > </span><a href="'.$base_url.'/?s=&publication_type='.$pub_type_slug.'">'.$pub_type.'</a>';
        }
        if (property_exists($post, 'award_type')){
            $award_type_slug = $post->award_type[0]->slug;
            switch ($award_type_slug){
                case 'rcn-research-exchange-grant':
                    $award_type_label = 'RCN Research Exchange Grants';
                    break;
                case 'education-outreach-grant':
                case 'education-and-outreach-grant':
                    $award_type_slug = 'education-outreach-grant';
                    $award_type_label = 'Education & Outreach Grants';
                    break;
                default:
                    $award_type_label = ucwords(str_replace('-',' ',$award_type_slug)) . 's';
            }
            $html .= '<span class="separator"> > </span><a href="'.$base_url.'/?s=&award_type='.$award_type_slug.'">'.$award_type_label.'</a>';
        }
        $html .= '</span>';
    }
    $html .= '</div>';
    $types['display'] = $html;

    return $types;
}

function item_people($post){
    $base_url = get_site_url();
    $people = array(
        'short' => ''
    );
    $people_types = array('publication_authors', 'publication_editors', 'data_project_people', 'dataset_people', 'protocol_authors', 'award_participants');
    foreach ($people_types as $people_type){
        $count = 0;
        if (isset($post->post_meta[$people_type])){
            $count = $post->post_meta[$people_type][0];
        }
        if ($count){
            $label = ucwords(str_replace('_', ' ', $people_type));
            $html = '<div class="people '.$people_type.'">';
            $html .= '<span class="label">'.$label.': </span>';
            $people_links = array();
            for ($i = 0; $i < $count; $i++){
                $person_id = $post->post_meta[$people_type.'_'.$i.'_person'][0];
                if (!$person_id){
                    $person = $post->post_meta[$people_type.'_'.$i.'_name'][0];
                } else {
                    $person = '<a href="'.$base_url.'/?s=&person_id='.$person_id.'">'.get_the_title($person_id).'</a>';
                }
                array_push($people_links, $person);
            }
            $html .= implode(', ', $people_links);
            $html .= '</div>';
            $people['short'] = $html;
        }
    }
    return $people;
}


function item_title($post){
    $title = array(
        'link' => '',
        'modal-link' => '',
        'ext-link' => '',
        'no-link' => ''
    );

    $html = '<div class="title">';
    $html .= $post->post_title;
    if ($post->tags && $post->tags[0]->name == 'new'){
        $html .= '<span class="new">NEW!</span>';
    }
    $html .= '</div>';
    $title['no-link'] = $html;

    $html = '<div class="title">';
    $link = get_permalink($post->ID);
    $html .= '<a href="'.$link.'">'.$post->post_title.'</a>';
    $html .= '</div>';
    $title['link'] = $html;

    $html = '<div class="title">';
    $html .= '<a href="'.$link .'" data-id="'.$post->ID.'" rel="modal">'.$post->post_title.'</a>';
    $html .= '</div>';
    $title['modal-link'] = $html;

    $url = '';

    if (array_key_exists('publication_url', $post->post_meta)){
        $url = $post->post_meta['publication_url'][0];
    }

    if (array_key_exists('post_url', $post->post_meta)){
        $url = $post->post_meta['post_url'][0];
    }

    if (array_key_exists('protocol_url', $post->post_meta)){
        $url = $post->post_meta['protocol_url'][0];
    }

    if (array_key_exists('newsletter_mailchimp_url', $post->post_meta)){
        $url = $post->post_meta['newsletter_mailchimp_url'][0];
    }

    if ($url){
        $html = '<div class="title">';
        $html .= '<a href="'.$url.'" target="_blank">'.$post->post_title.'</a>';
        $html .= '</div>';
        $title['ext-link'] = $html;
    } else {
        $title['ext-link'] = $title['link'];
    }

    return $title;
}

function item_related_entities($post){
    $related = array();
    if ($post->post_type == 'award') {
        $pubs = get_field('award_publications', $post->ID);
        if ($pubs) {
            $related['publication'] = array(
                'label' => 'Publications',
                'items' => array()
            );
            foreach( $pubs as $pub ) {
                $pub = get_post($pub);
                item_prepare($pub, null);
                $pub->no_modals = true;
                $related['publication']['items'][] = search_item_layout($pub);
            }
        }
        $data_project = get_field('award_data_projects', $post->ID);
        if ($data_project) {
            $related['data_project'] = array(
                'label' => 'Data Projects',
                'items' => array()
            );
            foreach( $data_project as $project ) {
                $project = get_post($project);
                item_prepare($project, null);
                $project->no_modals = true;
                $related['data_project']['items'][] = search_item_layout($project);
            }
        }
    }
    if ($post->post_type == 'publication') {
        $args = array(
            'post_type' => 'award',
            'meta_query' => array(
                array('key' => 'award_publications',
                    'value' => '"'.$post->ID.'"',
                    'compare' => 'LIKE')
            )
        );
        $pub_award_query = new WP_Query( $args );
        if ($pub_award_query->posts){
            $related['award'] = array(
                'label' => 'Awards',
                'items' => array()
            );
            foreach( $pub_award_query->posts as $award ) {
                item_prepare($award, null);
                $award->no_modals = true;
                $related['award']['items'][] = search_item_layout($award);
            }
        }
    }
    if ($post->post_type == 'data_project') {
        $args = array(
            'post_type' => 'award',
            'meta_query' => array(
                array('key' => 'award_data_projects',
                    'value' => '"'.$post->ID.'"',
                    'compare' => 'LIKE')
            )
        );
        $data_project_award_query = new WP_Query( $args );
        if ($data_project_award_query->posts){
            $related['award'] = array(
                'label' => 'Awards',
                'items' => array()
            );
            foreach( $data_project_award_query->posts as $award ) {
                item_prepare($award, null);
                $award->no_modals = true;
                $related['award']['items'][] = search_item_layout($award);
            }
        }
        if (isset($post->post_meta['data_project_datasets'])){
            $project_datasets = $post->post_meta['data_project_datasets'][0];
            if ($project_datasets){
                $related['dataset'] = array(
                    'label' => 'Datasets',
                    'items' => array()
                );
                for ($i = 0; $i < $project_datasets; $i++){
                    $dataset_id = $post->post_meta['data_project_datasets_'.$i.'_dataset'][0];
                    $dataset = get_post($dataset_id);
                    $dataset->no_modals = true;
                    $dataset->project = $post;
                    $dataset->post_meta = get_post_meta($dataset_id);
                    //$dataset->no_related = true;
                    //item_prepare($dataset, null);
                    $related['dataset']['items'][] = search_item_layout($dataset);
                }
            }
        }
    }
    if ($post->post_type == 'dataset') {
        if (isset($post->project)){
            $args = array(
                'post_type' => 'award',
                'meta_query' => array(
                    array('key' => 'award_data_projects',
                        'value' => '"'.$post->project->ID.'"',
                        'compare' => 'LIKE')
                )
            );
            $data_project_award_query = new WP_Query( $args );
            if ($data_project_award_query->posts){
                $related['award'] = array(
                    'label' => 'Awards',
                    'items' => array()
                );
                foreach( $data_project_award_query->posts as $award ) {
                    item_prepare($award, null);
                    $award->no_modals = true;
                    $related['award']['items'][] = search_item_layout($award);
                }
            }
        }
        if ($post->project){
            $project_datasets = $post->project->post_meta['data_project_datasets'][0];
            if ($project_datasets){
                $related['dataset'] = array(
                    'label' => 'Datasets',
                    'items' => array()
                );
                for ($i = 0; $i < $project_datasets; $i++){
                    $dataset_id = $post->project->post_meta['data_project_datasets_'.$i.'_dataset'][0];
                    if ($dataset_id != $post->ID){
                        $dataset = get_post($dataset_id);
                        $dataset->no_modals = true;
                        $dataset->post_meta = get_post_meta($dataset_id);
                        $dataset->project = $post->project;
                        //item_prepare($dataset, null);
                        $related['dataset']['items'][] = search_item_layout($dataset);
                    }
                }
            }
        }
    }
    return $related;
}

function item_content($post){
    $html = '<div class="content">';
    if (in_array($post->post_type, ['publication', 'award'])){
        $html .= '<h4>Abstract</h4>';
    }
    $html .= apply_filters('the_content', $post->post_content);
    $html .= '</div>';
    return $html;
}

function item_link($post){
    $html = '';
    $url = '';
    if (array_key_exists('post_url', $post->post_meta)){
        $url = $post->post_meta['post_url'][0];
    } else if (array_key_exists('publication_url', $post->post_meta)){
        $url = $post->post_meta['publication_url'][0];
    } else if (array_key_exists('protocol_url', $post->post_meta)){
        $url = $post->post_meta['protocol_url'][0];
    }
    if ($url){
        $html .= '<div class="link">';
        $html .= '<span class="label">Source: </span><a href="'.$url.'" target="_blank">'.$url.'</a>';
        $html .= '</div>';
    }
    if (array_key_exists('publication_file', $post->post_meta) && $post->post_meta['publication_file'][0]) {
        $url = get_field("publication_file", $post->ID);
        $html .= '<div class="link">';
        $html .= '<span class="label">File: </span><a href="'.$url.'" target="_blank">'.$url.'</a>';
        $html .= '</div>';
    }
    return $html;
}