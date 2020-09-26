<?php

function data_project_components($components, $post){
    $components['simple_fields'] = project_simple_fields($post);
    $components['description'] = project_description($post);
    $components['people'] = project_people($post);
    return $components;
}

function project_description($post){

    $description = '';
    // --> Project description
    if (isset($post->post_meta['data_project_description'])){
        $project_desc = $post->post_meta['data_project_description'][0];
        if ($project_desc){
            $description .= '<h4>Project Description</h4><div class="project-description">'.$project_desc.'</div>';
        }
    }
    return $description;
}



function project_simple_fields($post){

    $simple_fields = '';

    $fields = array(
        'title' => 'Project Title',
        'data_project_acronym' => 'Acronym',
        'data_project_url' => 'URL',
        'data_project_date_created' => 'Created',
        'data_project_date_modified' => 'Modified'
    );
    $field_info = array();
    foreach ($fields as $field => $label){
        $value = '';
        if ($field == 'title') { $value = $post->post_title; } else {
            if (isset($post->post_meta[$field])) {
                $value = $post->post_meta[$field][0];
            }
        }
        if ($value){
            if ($field == 'data_project_date_created' || $field == 'data_project_date_modified'){
                $date = '';
                if (preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $value)) {
                    $date = DateTime::createFromFormat('Y-m-d', $value);
                } else if (preg_match('/^[0-9]{8}$/', $value)) {
                    $date = DateTime::createFromFormat('Ymd', $value);
                }
                if (is_object($date)){
                    $value = $date->format('F j, Y');
                }
            }
            if ($field == 'data_project_url'){
                $value = '<a href="'.$value.'" target="_blank">'.$value.'</a>';
            }
            $field_info[] = array('label' => $label, 'value' => $value);
        }
    }
    $count = count($field_info);
    if ($count){
        $simple_fields .= '<table class="project-info"><tbody>';
        for ($i = 0; $i < $count; $i++){
            $field = $field_info[$i];
            $simple_fields .= '<tr><th>'.$field['label'].'</th><td>'.$field['value'].'</td></tr>';
        }
        $simple_fields .= '</tbody></table>';
    }
    return $simple_fields;
}

function project_people($post){

    $project_people = array(
        'short' => '',
        'full' => ''
    );

    if (isset($post->post_meta['data_project_people'])){
        $people_count = $post->post_meta['data_project_people'][0];
        if ($people_count){
            $base_url = get_site_url();
            $people_field = get_field('data_project_people', $post->ID);
            $people = [];
            foreach ($people_field as $row){
                $person_id = $row['person']->ID;
                $name = $row['person']->post_title;
                $link = '<a href="'.$base_url.'/?s=&person_id='.$person_id.'">'.$name.'</a>';
                $affiliation = $row['affiliation'];
                $role = $row['role'];
                if (!isset($people[$person_id])){
                    $people[$person_id] = array(
                        'name' => $name,
                        'link' => $link,
                        'affiliation' => $affiliation,
                        'role' => $role,
                        'contact' => false
                    );
                } else {
                    if ($role == 'contact'){
                        $people[$person_id]['contact'] = true;
                    } else {
                        $people[$person_id]['contact'] .= ', ' . $role;
                    }
                }
            }

            // SHORT VIEW
            $short_html = '<div class="people project_people">';
            $short_html .= '<span class="label">Project Maintainers: </span>';
            $i = 0;
            foreach($people as $person_id => $person) {
                $short_html .= $person['link'];
                $short_html .= $i + 1 < count($people) ? ', ' : '';
                $i++;
            }
            $short_html .= '</div>';
            $project_people['short'] = $short_html;

            // FULL VIEW
            $full = '<h4>Project Maintainers</h4>';
            $full .= '<table class="project-people"><thead><tr><th>Name</th><th>Affiliation</th><th>Role</th><th>Contact</th></tr></thead><tbody>';
            foreach ($people as $person){
                $contact = $person['contact'] ? '✓' : '';
                $full .= '<tr><td>'.$person['link'].'</td><td>'.$person['affiliation'].'</td><td>'.$person['role'].'</td><td>'.$contact.'</td></tr>';
            }
            $full .= '</tbody></table>';
            $project_people['full'] = $full;
        }
    }


    return $project_people;
}