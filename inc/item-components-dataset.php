<?php

function dataset_components($components, $post){
    $components['instruments'] = dataset_instruments($post);
    $components['project_info'] = dataset_project_info($post);
    $components['parameters'] = dataset_parameters($post);

    $components['simple_fields'] = dataset_simple_fields($post);
    $components['descriptions'] = dataset_descriptions($post);
    return $components;
}

function dataset_project_link($post){
    $project_link = '';
    if ($post->project){
        $link = get_permalink($post->project->ID);
        $title = $post->project->post_title;
        $project_link = '<div class="project"><span class="label">Data Project: </span>';
        $project_link .= '<a href="'.$link.'">'.$title.'</a></div>';
    }
    return $project_link;
}

function dataset_project_info($post){

    require_once('item-components-data_project.php');

    $project_info = array(
        'full' => '',
        'link' => ''
    );

    if ($post->project){
        // PROJECT LINK
        $prj_link = get_permalink($post->project->ID);
        $prj_title = $post->project->post_title;
        $project_info['link'] = '<div class="project"><span class="label">Data Project: </span>';
        $project_info['link'] .= '<a href="'.$prj_link.'">'.$prj_title.'</a></div>';

        // FULL VIEW
        $full = '';
        $full .= '<h4>BCO-DMO Project Info</h4>';

        // --> Simple project fields
        $full .= project_simple_fields($post->project);

        // --> Project description
        $full .= str_replace('h4>', 'h5>', str_replace('<h4', '<h5', (project_description($post->project))));

        // --> Project people
        $full .= str_replace('h4>', 'h5>', str_replace('<h4', '<h5', (project_people($post->project)['full'])));

        $project_info['full'] = $full;
    }

    return $project_info;
}

function dataset_descriptions($post){
    $descriptions = '';
    $fields = array(
        'dataset_acquisition_description' => 'Acquisition Description',
        'dataset_processing_description' => 'Processing Description'
    );
    foreach ($fields as $field => $label){
        if (isset($post->post_meta[$field])){
            $value = $post->post_meta[$field][0];
            if ($value){
                $descriptions .= '<h4>'.$label.'</h4>';
                $descriptions .= '<div class="'.$field.'">'.$value.'</div>';
            }
        }
    }
    return $descriptions;
}

function dataset_simple_fields($post){
    $simple_fields = '';

    $fields = array(
        'dataset_url' => 'URL',
        'dataset_download_url' => 'Download URL',
        'dataset_media_type' => 'Media Type',
        'dataset_date_created' => 'Created',
        'dataset_date_modified' => 'Modified',
        'dataset_bco_dmo_state' => 'State',
        'dataset_brief_description' => 'Brief Description'
    );
    $field_info = array();
    foreach ($fields as $field => $label){
        if (isset($post->post_meta[$field])){
            $value = $post->post_meta[$field][0];
            if ($value){
                if ($field == 'dataset_url' || $field == 'dataset_download_url'){
                    $value = '<a href="'.$value.'" target="_blank">'.$value.'</a>';
                }
                if ($field == 'dataset_date_modified' || $field == 'dataset_date_created'){
                    $date = new DateTime($value);
                    $value = $date->format('F j, Y');
                }
                $field_info[] = array('label' => $label, 'value' => $value);
            }
        }
    }
    $count = count($field_info);
    if ($count){
        $simple_fields .= '<table class="simple-fields"><tbody>';
        for ($i = 0; $i < $count; $i++){
            $field = $field_info[$i];
            $simple_fields .= '<tr><th>'.$field['label'].'</th><td>'.$field['value'].'</td></tr>';
        }
        $simple_fields .= '</tbody></table>';
    }
    return $simple_fields;
}

function dataset_people($post){
    $base_url = get_site_url();
    $people = array(
        'short' => '',
        'full' => ''
    );

    $short_html = '';
    $full_html = '';

    if (isset($post->project)) {
        $count = $post->project->post_meta['data_project_people'][0];
        if ($count){
            $short_html .= '<div class="people data_project_people">';
            $short_html .= '<span class="label">Project Maintainers: </span>';
            $people = get_field('data_project_people', $post->project->ID);
            $base_url = get_site_url();
            $i = 0;
            foreach($people as $person) {
                $short_html .= '<a href="' . $base_url . '/?s=&person_id=' . $person['person']->ID . '">' . $person['person']->post_title . '</a>';
                $short_html .= $i + 1 < count($people) ? ', ' : '';
                $i++;
            }
            $short_html .= '</div>';
        }
    }

    $count = $post->post_meta['dataset_people'][0];

    if ($count){
        $people_info = array();
        $people = get_field('dataset_people', $post->ID);
        foreach ($people as $person){
            $person_id = $person['person']->ID;
            $person_name = $person['person']->post_title;
            array_push($people_info, array(
                'link' => '<a href="'.$base_url.'/?s=&person_id='.$person_id.'">'.$person_name.'</a>',
                'name' => $person_name,
                'affiliation' => $person['affiliation'],
                'contact' => $person['contact']
            ));
        }

        $short_html .= '<div class="people dataset_people">';
        $short_html .= '<span class="label">Dataset Maintainers: </span>';
        for ($i = 0; $i < count($people_info); $i++){
            $short_html .= $people_info[$i]['link'];
            $short_html .= $i + 1 < count($people_info) ? ', ' : '';
        }
        $short_html .= '</div>';


        // FULL VIEW
        $full_html = '<div class="people dataset_people">';
        $full_html .= '<h4>Dataset Maintainers</h4>';
        $full_html .= '<table><thead><tr><th>Name</th><th>Affiliation</th><th>Contact</th></tr></thead><tbody>';

        foreach ($people_info as $person){
            $contact = $person['contact'] ? '✓' : '';
            $full_html .= '<tr><td>'.$person['link'].'</td><td>'.$person['affiliation'].'</td><td>'.$contact.'</td></tr>';
        }
        $full_html .= '</tbody></table></div>';
    }

    $people['short'] = $short_html;
    $people['full'] = $full_html;

    return $people;
}

function dataset_instruments($post){
    $instruments = array(
        'short' => '',
        'full' => ''
    );

    if (isset($post->post_meta['dataset_instruments']) && $post->post_meta['dataset_instruments'][0]){

        $count = $post->post_meta['dataset_instruments'][0];
        $types = array();
        $instances = array();
        for ($i = 0; $i < $count; $i++){
            $type_id = $post->post_meta['dataset_instruments_'.$i.'_type_term'][0];
            if (!isset($types[$type_id])){
                $types[$type_id] = [
                    'name' => $post->post_meta['dataset_instruments_'.$i.'_type'][0],
                    'desc' => isset($post->post_meta['dataset_instruments_'.$i.'_type_desc']) ? $post->post_meta['dataset_instruments_'.$i.'_type_desc'][0] : null
                ];
            }
            $instance_name = isset($post->post_meta['dataset_instruments_'.$i.'_name']) ? $post->post_meta['dataset_instruments_'.$i.'_name'][0] : '';
            $instance_desc = isset($post->post_meta['dataset_instruments_'.$i.'_desc']) ? $post->post_meta['dataset_instruments_'.$i.'_desc'][0] : '';
            $instances[] = array(
                'name' => $instance_name,
                'desc' => $instance_desc,
                'type_id' => $type_id,
                'type_name' => $post->post_meta['dataset_instruments_'.$i.'_type'][0],
                'type_desc' => isset($post->post_meta['dataset_instruments_'.$i.'_type_desc']) ? $post->post_meta['dataset_instruments_'.$i.'_type_desc'][0] : null
            );
        }

        $html = '<div class="instruments">';

        $short_html = $html;
        $short_html .= '<span class="label">Instruments: </span>';
        $short_types = array();
        foreach ($types as $type_id => $type) {
            $short_types[] = '<a href="' . get_site_url() . '/?s=&instrument=' . $type_id . '">' . $type['name'] . '</a>';
        }
        $short_html .= implode(', ', $short_types);
        $short_html .= '</div>';
        $instruments['short'] = $short_html;

        $full_html = $html;
        $full_html .= '<h4>Instruments</h4>';
        $full_html .= '<div class="c-debi-accordion">';
        foreach ($instances as $instance) {
            $full_html .= '<div class="section">';
            $type_link = '<a href="' . get_site_url() . '/?s=&instrument=' . $instance['type_id'] . '">' . $instance['type_name'] . '</a>';
            $instrument_title_html = $instance['name'] ? $instance['name'] .' ['. $type_link . ']' : $type_link;
            $instrument_title = '<div class="instrument-title">'. $instrument_title_html . '</div>';
            $full_html .= '<div class="section-title">'.$instrument_title.'<div class="details"><span class="label">Details</span><span class="toggle"></span></div></div>';
            $full_html .= '<div class="section-content">';
            if ($instance['desc']){
                $inst_desc_label = $instance['name'] ? ' ('.$instance['name'].')' : '';
                $full_html .= '<div class="instance-description"><div class="name">Instance Description'.$inst_desc_label.'</div><div class="description">'.$instance['desc'].'</div></div>';
            }
            if ($instance['type_desc']){
                $full_html .= '<div class="type-description"><div class="name">'.$type_link.'</div><div class="description">'.$instance['type_desc'].'</div></div>';
            }
            $full_html .= '</div></div>';
        }
        $full_html .= '</div></div>';

        $instruments['full'] = $full_html;
    }
    return $instruments;
}

function dataset_parameters($post){

    $parameters = array(
        'short' => '',
        'full' => ''
    );

    if (isset($post->post_meta['dataset_parameters']) && $post->post_meta['dataset_parameters'][0]){
        $count = $post->post_meta['dataset_parameters'][0];
        $params = array();
        for ($i = 0; $i < $count; $i++){
            $params[] = array(
                'generic_name' => $post->post_meta['dataset_parameters_'.$i.'_generic_name'][0],
                'generic_id' => isset($post->post_meta['dataset_parameters_'.$i.'_generic']) ? $post->post_meta['dataset_parameters_'.$i.'_generic'][0] : null,
                'generic_desc' => $post->post_meta['dataset_parameters_'.$i.'_generic_desc'][0],
                'name' => $post->post_meta['dataset_parameters_'.$i.'_name'][0],
                'desc' => $post->post_meta['dataset_parameters_'.$i.'_desc'][0]
            );
        }

        $html = '<div class="parameters">';

        $short_html = $html;
        $short_params = array();
        foreach ($params as $param){
            $terms_html[] = '<a href="'.get_site_url().'/?s=&parameter='.$param['generic_id'].'">'.$param['generic_name'].'</a>';
        }
        $short_html .= '<span class="label">Parameter Types: </span>';
        $short_html .= implode(', ', $short_params);
        $short_html .= '</div>';
        $parameters['short'] = $short_html;

        $full_html = $html;
        $full_html .= '<h4>Parameters</h4>';
        $full_html .= '<div class="c-debi-accordion">';
        foreach ($params as $param){
            $full_html .= '<div class="section">';
            $generic_link = '<a href="'.get_site_url().'/?s=&parameter='.$param['generic_id'].'">'.$param['generic_name'].'</a>';
            $parameter_title = '<div class="parameter-title">'. $param['name'] .' ['. $generic_link . ']</div>';
            $full_html .= '<div class="section-title">'.$parameter_title.'<div class="details"><span class="label">Details</span><span class="toggle"></span></div></div>';
            $full_html .= '<div class="section-content">';
            $full_html .= '<div class="instance-description"><div class="name">'.$param['name'].'</div><div class="description">'.$param['desc'].'</div></div>';
            $full_html .= '<div class="generic-description"><div class="name">'.$generic_link.'</div><div class="description">'.$param['generic_desc'].'</div></div>';
            $full_html .= '</div></div>';
        }
        $full_html .= '</div></div>';

        $parameters['full'] = $full_html;
    }
    return $parameters;
}