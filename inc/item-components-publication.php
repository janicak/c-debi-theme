<?php

function publication_components($components, $post){
    $components['contribution_number'] = publication_contribution_number($post);
    $components['people'] = publication_people($post);
    return $components;
}

function publication_people($post){
    $people = array(
        'short' => ''
    );
    $base_url = get_site_url();
    $people_types = array('publication_authors', 'publication_editors');
    $html = '';
    foreach ($people_types as $people_type){
        $count = 0;
        if (isset($post->post_meta[$people_type])){
            $count = intval($post->post_meta[$people_type][0]);
        }
        if ($count){
            $label = ucwords(str_replace('publication_', '', $people_type));
            $html .= '<div class="people '.$people_type.'">';
            $html .= '<span class="label">'.$label.': </span>';
            $people_links = array();
            for ($i = 0; $i < $count; $i++){
                $person_id = $post->post_meta[$people_type.'_'.$i.'_person'][0] ? $post->post_meta[$people_type.'_'.$i.'_person'][0] : '';
                if ($person_id){
                    $person = '<a href="'.$base_url.'/?s=&person_id='.$person_id.'">'.get_the_title($person_id).'</a>';
                } else {
                    $person = $post->post_meta[$people_type.'_'.$i.'_given'][0] . ' ' . $post->post_meta[$people_type.'_'.$i.'_family'][0] ;
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

function publication_contribution_number($post){
    $html = '';
    if (isset($post->post_meta['publication_contribution_number'])){
        $html .= '<div class="contribution-number">';
        $html .= '<span class="label">C-DEBI Contribution Number: </span>'.$post->post_meta['publication_contribution_number'][0].'</div>';
    }
    return $html;
}