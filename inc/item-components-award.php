<?php

function award_components($components, $post){
    $components['amount'] = award_amount($post);
    $components['people'] = award_participants($post);
    return $components;
}

function award_participants($post){

    $people = array(
        'short' => ''
    );
    $count = $post->post_meta['award_participants'][0];
    if ($count){
        $base_url = get_site_url();

        $roles = array();
        $people_meta = array();
        for($i = 0; $i < $count; $i++){
            $role = $post->post_meta['award_participants_'.$i.'_role'][0];
            $person = $post->post_meta['award_participants_'.$i.'_person'][0];
            $org = $post->post_meta['award_participants_'.$i.'_organization'][0];
            $current_placement = null;
            $degree = null;
            //$current_placement = isset($post->post_meta['award_participants_'.$i.'_current_placement']) ? $post->post_meta['award_participants_'.$i.'_current_placement'][0] : null;
            //$degree = isset($post->post_meta['award_participants_'.$i.'_degree']) ? $post->post_meta['award_participants_'.$i.'_degree'][0] : null;
            if (
                in_array($post->award_type[0]->slug, ["postdoctoral-fellowship", "graduate-fellowship"])
                && in_array(strtolower($role), ['awardee', 'pi'])
            ) {
                $current_placement = get_field('person_current_placement', $person);
                $degree = get_field('person_degree', $person);
            }
            if (!isset($roles[$role])){
                $roles[$role] = array();
            }
            array_push($roles[$role], array(
              'ID' => $person,
              'org' => $org,
              'current_placement' => $current_placement,
              'degree' => $degree
            ));
        }
        foreach($roles as $role => $role_people) {
            $role_label = count($role_people) > 1 ? ucwords($role . 's') : ucwords($role);
            $html = '<div class="people '.$role.'"><span class="label">'.$role_label.': </span><span class="value">';
            for($i=0; $i < count($role_people); $i++){
                $person_id = $role_people[$i]['ID'];
                $person_org = $role_people[$i]['org'];
                $person_current_placement = $role_people[$i]['current_placement'];
                $person_degree = $role_people[$i]['degree'];
                if (($person_current_placement || $person_degree) && $i > 0){
                    if (!$role_people[$i-1]['current_placement'] && ! $role_people[$i-1]['degree']){
                        $html .= '<br />';
                    }
                }
                $person_org = $person_org ? ' (' . $person_org . ')' : '';
                $html .= '<a href="'.$base_url.'/?s=&person_id='.$person_id.'">'.get_the_title($person_id).'</a>'.$person_org;
                if ($i + 1 < count($role_people)) {
                    $html .= ', ';
                }
                if ($person_current_placement){
                    $html .= '<div class="current-placement"><span class="label">Current Placement: </span>';
                    $html .= $person_current_placement . '</div>';
                }
                if ($person_degree){
                    $html .= '<div class="degree"><span class="label">Degree: </span>';
                    $html .= $person_degree . '</div>';
                }
            }
            $html .= '</span></div>';
            $people_meta[$role] = $html;
        }
        //$people_order = array('PI','Awardee', 'degree', 'current_placement', 'Advisor', 'Host');
        $people_order = array('PI','Awardee', 'Advisor', 'Host');
        $ordered_people = array();
        foreach($people_order as $ordered_role){
            if (isset($people_meta[$ordered_role])){
                $ordered_people[$ordered_role] = $people_meta[$ordered_role];
                unset($people_meta[$ordered_role]);
            }
        }
        if (count($people_meta)){
            foreach($people_meta as $key => $val){
                $ordered_people[$key] = $val;
            }
        }
        $short_html = '';
        foreach($ordered_people as $html){
            $short_html .= $html;
        }
        $people['short'] = $short_html;

    }
    return $people;
}

function award_amount($post){
    $html = '';
    if ($post->post_type == 'award') {
        if (isset($post->post_meta['award_amount'])) {
            $amount = $post->post_meta['award_amount'][0];
            $fmt = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
            $amount = $fmt->formatCurrency($amount, 'USD');
            $html .= '<div class="amount"><span class="label">Amount: </span><span class="amount">' . $amount . '</span></div>';
        }
    }
    return $html;
}