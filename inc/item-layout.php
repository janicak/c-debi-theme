<?php

function news_item_layout($post){
    $meta = item_components($post);
    $html = '';
    $html .= $meta['organization']['no-link'];
    $html .= item_title($post)['no-link'];
    $html .= $meta['date']['display'];
    if ($post->show_content){
        $html .= item_content($post);
    }
    return $html;
}

function news_item_layout_large($post){
    $meta = item_components($post);
    $html = '';
    //$html .= $meta['organization']['no-link'];
    $html .= '<div class="title-container announcement"><span class="icon"></span>';
    $html .= item_title($post)['no-link'];
    $html .= '</div>';
    $html .= $meta['date']['display'];
    $html .= item_content($post);
    return $html;
}

function full_item_layout($post){

    $html = '';
    $components = item_components($post);

    $options = array(
        'people' => false,
        'title' => 'ext-link',
        'ext-link' => true,
        'content' => true,
        'date' => true

    );
    foreach($components['types']['slugs'] as $type){
        if (in_array($type, array('publication', 'award', 'data_project', 'dataset', 'protocol'))){
            $options['people'] = true;
        }
        if (in_array($type, array('publication', 'award', 'data_project', 'dataset', 'announcement', 'newsletter', 'protocol'))){
            if (in_array($type, array('publication')) && $post->post_type == 'post') {
                $options['title'] = 'ext-link';
            } else if (in_array($type, array('newsletter', 'protocol'))){
                $options['title'] = 'ext-link';
            } else {
                $options['title'] = 'link';
            }
        }
        if (in_array($type, array('dataset', 'data_project'))){
            $options['ext-link'] = false;
            $options['content'] = false;
            $options['date'] = false;
        }
    }

    // GET ITEM CSS CLASSES
    $classes = array($post->post_type);
    if (isset($post->categories)){
        foreach($post->categories as $cat){
            array_push($classes, $cat->slug);
        }
    }

    // START HEADER
    $html .= '<div class="item-header background-color '.implode(' ', $classes).'"><div class="container">';

    // --> POST CATEGORIES or TYPE
    $html .= $components['types']['display'];

    // --> ORGANIZATION
    $html .= $components['organization']['link'];

    // --> TITLE
    $html .= item_title($post)[$options['title']];

    // START INFO
    $html .= '<div class="info">';

    // --> PROJECT
    if ($post->post_type == 'dataset') {
        $project = dataset_project_link($post);
        if ($project) {
            $html .= $project;
        }
    }

    // --> PEOPLE
    if ($components['people']){
        $html .= $components['people']['short'];
    }

    // --> AMOUNT
    if ($post->post_type == 'award') {
        $amount = award_amount($post);
        if ($amount){
            $html .= $amount;
        }
    }

    // -> POST DATE
    if ($options['date']){
        $html .= $components['date']['display'];
    }

    // --> CONTRIBUTION NUMBER
    if ($post->post_type == 'publication') {
        $contrib = publication_contribution_number($post);
        if ($contrib){
            $html .= $contrib;
        }
    }

    // END INFO
    $html .= '</div>';

    // END HEADER
    $html .= '</div></div>';

    // START BODY
    $html .= '<div class="item-body"><div class="container">';

    // -> CONTENT
    if ($options['content']){
        $html .= item_content($post);
    }

    // --> DATASET STUFF
    if ($post->post_type == 'dataset'){
        $simple_fields = dataset_simple_fields($post);
        if ($simple_fields){
            $html .= $simple_fields;
        }
        $descriptions = dataset_descriptions($post);
        if ($descriptions){
            $html .= $descriptions;
        }
        $instruments = dataset_instruments($post);
        if ($instruments['full']){
            $html .= $instruments['full'];
        }
        $parameters = dataset_parameters($post);
        if ($parameters['full']){
            $html .= $parameters['full'];
        }
        if (isset($components['people']['full'])){
            $html .= $components['people']['full'];
        }
        $project_info = dataset_project_info($post);
        if ($project_info['full']){
            $html .= $project_info['full'];
        }
    }

    // PROJECT STUFF
    if ($post->post_type == 'data_project'){
        $simple_fields = project_simple_fields($post);
        if ($simple_fields){
            $html .= $simple_fields;
        }
        $description = project_description($post);
        if ($description){
            $html .= $description;
        }
        if (isset($components['people']['full'])){
            $html .= $components['people']['full'];
        }
    }


    // -> EXTERNAL URL or PERMALINK
    if ($options['ext-link']){
        $html .= item_link($post);
    }

    // -> RELATED ENTITIES
    $components['related'] = item_related_entities($post);
    if (count($components['related'])){
        $html .= '<div class="related"><h4>Related Items</h4>';
        foreach ($components['related'] as $key => $val){
            $count = count($val['items']);
            if ($count){
                $html .= '<div class="related-category '.$key.'"><div class="container">';
                $html .= '<div class="title"><span class="icon"></span><span>'.$val['label'].'</span></div>';
                $html .= '<div class="items">';
                for ($i = 0; $i < $count; $i++){
                    $html .= '<div class="item">';
                    $html .= $val['items'][$i];
                    $html .= '</div>';
                    // Add separator if needed
                    if ($i + 1 < $count){
                        $html .= '<div class="separator"></div>';
                    }
                }
                $html .= '</div></div></div>';
            }
        }
       $html .= '</div>';
    }

    // END BODY
    $html .= '</div></div>';


    return $html;
}

function search_item_layout($post){
    $html = '';
    $components = item_components($post);

    // Set type-related options
    $options = array(
        "organization" => true,
        "title" => 'modal-link',
    );
    foreach ($components['types']['slugs'] as $type){
        if (in_array($type, array('spotlight', 'announcement'))){
            $options['organization'] = false;
        }
        if (in_array($type, array('spotlight', 'page', 'press'))){
            $options['title'] = 'link';
        }
        if (in_array($type, array('newsletter', 'protocol'))){
            $options['title'] = 'ext-link';
        }
    }

    // START ITEM ARTICLE
    $weight = isset($post->result_weight) ? ' data-weight="'.$post->result_weight.'"' : '';
    $html .= '<article class="search-result '. implode(' ', $components['types']['slugs']).'" data-sort="'.$components['date']['sort'].'"'.$weight.'><div class="container">';

    // --> START HEAD
    $html .= '<div class="head">';

    // -->--> POST CATEGORIES or TYPE
    $html .= $components['types']['display'];

    // -->--> POST DATE
    $html .= $components['date']['display'];

    // --> END HEAD
    $html .= '</div>';

    // --> ORGANIZATION
    if ($options['organization'] && $components['organization']){
        $html .= $components['organization']['link'];
    }

    // --> INSTRUMENTS
    if ($post->post_type == 'dataset'){
        $instruments = dataset_instruments($post);
        if ($instruments['short']){
            $html .= $instruments['short'];
        }
    }

    // --> TITLE
    $link_option = $post->no_modals ? 'link' : $options['title'];
    $html .= $components['title'][$link_option];

    // --> PROJECT
    if ($post->post_type == 'dataset') {
        $project = dataset_project_link($post);
        if ($project) {
            $html .= $project;
        }
    }

    // --> PEOPLE
    if (isset($components['people'])){
        $html .= $components['people']['short'];
    }

    // --> CONTRIBUTION NUMBER
    if ($post->post_type == 'publication') {
        $contrib = publication_contribution_number($post);
        if ($contrib){
            $html .= $contrib;
        }
    }

    // -> EXCERPT
    if ($post->excerpt){
        $html .= '<div class="excerpt">' . $post->excerpt . '</div>';
    }

    // END ARTICLE
    $html .= '</div></article>';

    return $html;
}
