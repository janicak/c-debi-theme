<?php

namespace C_DEBI_Theme;

class Entity {

    public $post_id;
    public $permalink;
    public $post_type;
    public $post_type_labels;
    public $post_title;
    public $post_content;
    public $post_excerpt;

    public $date_created;
    public $date_modified;
    public $date_expired;

    public $acf_fields;
    public $acf_field_meta;
    public $taxonomies;
    public $related_entities;
    public $people;

    public $search_result_weight;

    public function __construct($post) {
        $this->post_id = $post->ID;
        $this->permalink = get_permalink($post->ID);
        $this->post_type = $post->post_type;
        $this->post_type_labels = $this->get_post_type_labels();
        $this->post_title = $post->post_title;
        $this->post_content = $post->post_content;
        $this->post_excerpt = $post->post_excerpt;

        $this->date_created = $post->post_date;
        $this->date_modified = $post->post_modified;
        $this->date_expired = $this->get_date_expired();

        $this->acf_fields = $this->get_acf_fields();
        $this->acf_field_meta = $this->get_acf_field_meta();
        $this->taxonomies = $this->get_taxonomies();
        $this->related_entities = [];
        $this->people = [];

        $this->search_result_weight = $this->get_search_result_weight();

        $this->convert_date_strings_to_date_objects();
    }

    public function get_post_type_and_term_slugs(){
        $css_classes = [];

        $css_classes[] = $this->post_type;

        $taxonomies = [ 'category', 'award_type', 'publication_type' ];

        foreach($taxonomies as $taxonomy){

            if (isset($this->taxonomies[$taxonomy])){

                foreach($this->taxonomies[$taxonomy] as $term){
                    $css_classes[] = $term->slug;
                }

            }
        }

        return $css_classes;
    }
    public function get_query_type_info(){
        $query_type_info = [
            'param' => '',
            'values' => []
        ];

        $post_types_to_query_type_map = [
            'post' => [
                'param' => 'category',
                'taxonomy' => 'category',
                'sub_type' => false
            ],
            'publication' => [
                'param' => 'type',
                'taxonomy' => false,
                'sub_type' => [
                    'param' => 'publication_type',
                    'taxonomy' => 'publication_type'
                ]
            ],
            'award' => [
                'param' => 'type',
                'taxonomy' => false,
                'sub_type' => [
                    'param' => 'award_type',
                    'taxonomy' => 'award_type'
                ]
            ],
            'default' => [
                'param' => 'type',
                'taxonomy' => false,
                'sub_type' => false
            ]
        ];

        ['param' => $param, 'taxonomy' => $taxonomy, 'sub_type' => $sub_type] =
            $post_types_to_query_type_map[$this->post_type] ??
            $post_types_to_query_type_map['default'];

        $query_type_info['param'] = $param;

        if ($taxonomy) {
            $query_type_info['values'] = isset($this->taxonomies[$taxonomy])
                ? array_map(
                    function($term){
                        return [
                            'slug' => $term->slug,
                            'label' => $term->name,
                        ];
                    },
                    $this->taxonomies[$taxonomy]
                )
                : false;
        } else {
            $query_type_info['values'] = [[
                'slug' => $this->post_type,
                'label' => $this->post_type_labels["plural"],
            ]];
            if ($sub_type){
                ['param' => $sub_type_param, 'taxonomy' => $sub_type_taxonomy] = $sub_type;
                $query_type_info['values'][0]['sub_type'] = isset($this->taxonomies[$sub_type_taxonomy])
                    ? array_map(
                    function($term) use ($sub_type_param){
                        return [
                            'param' => $sub_type_param,
                            'slug' => $term->slug,
                            'label' => $term->name,
                        ];
                    },
                    $this->taxonomies[$sub_type_taxonomy]
                )
                : false;
            }
        }

        return $query_type_info;
    }
    public function get_organization_info(){
        $organization_info = [
            'slug' => false,
            'name' => false
        ];

        $post_type_to_organization_info = [
            'post' => [
                'slug' => isset($this->acf_fields['post_organization']) && $this->acf_fields['post_organization']
                    ? $this->acf_fields['post_organization'][0]->slug : false,
                'name' => isset($this->acf_fields['post_organization']) && $this->acf_fields['post_organization']
                    ? $this->acf_fields['post_organization'][0]->name : false
            ],
            'publication' => [
                'slug' => false,
                'name' => $this->acf_fields['publication_publisher_title'] ?? false
            ],
        ];

        $organization_info = $post_type_to_organization_info[$this->post_type] ?? $organization_info;

        return $organization_info;
    }
    public function get_external_url(){
        $post_type_to_ext_url = [
            'post' => function(){ return $this->acf_fields['post_url'] ?? false; },
            'publication' => function(){ return $this->acf_fields['publication_url'] ?? false; },
            'protocol' => function(){ return $this->acf_fields['protocol_url'] ?? false; },
            'newsletter' => function(){ return $this->acf_fields['newsletter_mailchimp_url'] ?? false; },
        ];

        $external_url = isset($post_type_to_ext_url[$this->post_type]) ? $post_type_to_ext_url[$this->post_type]() : false;

        return $external_url;
    }
    public function is_new(){
        if (isset($this->taxonomies['post_tag'])){
            foreach($this->taxonomies['post_tag'] as $term){
                if ($term->slug === 'new') {
                    return true;
                }
            }
        }
        return false;
    }
    public function get_related_entities($related_entity_type_slugs = null){
        $related_entities = [];

        $entity_type_to_related_entity_callbacks = [
            'award' => [
                'data_project' => function(){
                    return isset($this->acf_fields['award_data_projects']) ? $this->posts_to_entities($this->acf_fields['award_data_projects']) : [];
                },
                'publication' => function(){
                    return isset($this->acf_fields['award_publications']) ? $this->posts_to_entities($this->acf_fields['award_publications']) : [];
                }
            ],
            'data_project' => [
                'award' => function(){
                    return isset($this->acf_fields['data_project_awards']) ? $this->posts_to_entities($this->acf_fields['data_project_awards']) : [];
                },
                'dataset' => function(){
                    return isset($this->acf_fields['data_project_datasets']) && $this->acf_fields['data_project_datasets']
                        ? $this->posts_to_entities(array_map(
                                function($row){ return get_post($row['dataset']);},
                                $this->acf_fields['data_project_datasets']
                            ))
                        : [];
                }
            ],
            'publication' => [
                'award' => function(){
                    return isset($this->acf_fields['publication_awards']) ? $this->posts_to_entities($this->acf_fields['publication_awards']) : [];
                },
            ],
            'dataset' => [
                'award' => function(){
                    $related_data_projects = $this->get_related_entities('data_project');
                    $awards = [];
                    foreach ($related_data_projects as $data_project){
                        $awards = array_merge($awards, $data_project->get_related_entities('award'));
                    }
                    return $awards;
                },
                'data_project' => function(){
                    $data_project_query = new \WP_Query([
                        'post_type' => 'data_project',
                        'posts_per_page' => -1,
                        'meta_query' => [
                            'relation' => 'OR',
                            [
                                'key' => 'data_project_datasets_$_dataset',
                                'compare' => '=',
                                'value' => $this->post_id
                            ]
                        ]
                    ]);
                    return count($data_project_query->posts) ? $this->posts_to_entities($data_project_query->posts) : [];
                },
                'dataset' => function(){
                    $related_data_projects = $this->get_related_entities('data_project');
                    $datasets= [];
                    foreach ($related_data_projects as $data_project){
                        $datasets = array_merge(
                            $datasets,
                            array_values(array_filter(
                                $data_project->get_related_entities('dataset'),
                                function($dataset){ return $dataset->post_id !== $this->post_id; }
                            ))
                        );
                    }
                    return $datasets;
                }
            ]
        ];

        $this_related_entity_callbacks = $entity_type_to_related_entity_callbacks[$this->post_type] ?? false;

        if ($this_related_entity_callbacks){
            $slugs = $related_entity_type_slugs ?? array_keys($this_related_entity_callbacks);
            $slugs = is_array($slugs) ? $slugs : [ $slugs ];

            foreach($slugs as $slug){
                if (isset($this_related_entity_callbacks[$slug]) && !isset($this->related_entities[$slug])){
                    $this->related_entities[$slug] = $this_related_entity_callbacks[$slug]();
                }
                $related_entities[$slug] = $this->related_entities[$slug];
            }

        }

        if (is_array($related_entity_type_slugs) && count($related_entity_type_slugs) > 1){
            return array_filter($related_entities, function($k) use($related_entity_type_slugs){
                return in_array($k, $related_entity_type_slugs);
            }, ARRAY_FILTER_USE_KEY);
        } else if ($related_entity_type_slugs) {
            $slug = is_array($related_entity_type_slugs) ? $related_entity_type_slugs[0] : $related_entity_type_slugs;
            return $related_entities[$slug];
        } else {
            return $related_entities;
        }

    }
    public function get_date_info(){
        $date_info = [
            'label' => '',
            'dates' => [],
            'sort_date' => null
        ];

        switch($this->post_type){
            case 'post':
                if ($this->date_expired){
                    $date_info['label'] = new \DateTime() > $this->date_expired ? 'Expired' : 'Expires on';
                    $date_info['dates'][] = $date['sort_date'] = $this->date_expired;
                }
                break;
            case 'publication':
                if ($this->acf_fields['publication_date_published']){
                    $date_info['label'] = 'Published';
                    $date_info['dates'][] = $date['sort_date'] = $this->acf_fields['publication_date_published'];
                }
                break;
            case 'award':
                if ($this->acf_fields['award_start_date'] && $this->acf_fields['award_end_date']){
                    $date_info['label'] = 'Award Dates';
                    $date_info['dates'][] = $date['sort_date'] = $this->acf_fields['award_start_date'];
                    $date_info['dates'][] = $this->acf_fields['award_end_date'];
                }
                break;
            case 'page':
                $date_info['label'] = 'Modified';
                $date_info['dates'][] = $date['sort_date'] = $this->date_modified;
                break;
            case 'dataset';
                if ($this->acf_fields['dataset_date_modified']){
                    $date_info['label'] = 'Last Modified';
                    $date_info['dates'][] = $date['sort_date'] = $this->acf_fields['dataset_date_modified'];
                }
                break;
            case 'data_project';
                if ($this->acf_fields['data_project_date_modified']){
                    $date_info['label'] = 'Last Modified';
                    $date_info['dates'][] = $date['sort_date'] = $this->acf_fields['data_project_date_modified'];
                }
                break;
            default:
                $date_info['label'] = 'Posted';
                $date_info['dates'][] = $date['sort_date'] = $this->date_created;
                break;
        }

        if (!count($date_info['dates'])){
            $date_info['label'] = 'Posted';
            $date_info['dates'][] = $date['sort_date'] = $this->date_created;
        }

        $date['sort_date'] = $date['sort_date']->format('Y-m-d H:i:s');

        return $date_info;
    }
    public function get_people(){
        if (count($this->people)){
            return $this->people;
        }

        $people = [];

        $post_types_to_people_callbacks = [
            'publication' => function() {
                $people = [];
                if( isset( $this->acf_fields[ 'publication_authors' ] ) ) {
                    $people[ 'publication_authors' ] = [ 'label' => 'Authors', 'people' => $this->acf_fields[ 'publication_authors' ] ];
                }
                if( isset( $this->acf_fields[ 'publication_editors' ] ) ) {
                    $people[ 'publication_editors' ] = [ 'label' => 'Editors', 'people' => $this->acf_fields[ 'publication_editors' ] ];
                }
                return $people;
            },
            'dataset' => function() {
                $people = [];
                if( isset( $this->acf_fields[ 'dataset_people' ] ) ) {
                    $people[ 'dataset_people' ] = [
                        'label' => 'Dataset Maintainers',
                        'people' => $this->deduplicate_bcodmo_people($this->acf_fields['dataset_people'])
                    ];
                }
                $data_projects = $this->get_related_entities('data_project');
                if (count($data_projects)){
                    $data_projects_people = array_reduce($data_projects, function($acc, $project){
                        $data_project_people = $project->get_people();
                        if (isset($data_project_people['data_project_people'])){
                            $acc = array_merge($acc, $this->deduplicate_bcodmo_people($data_project_people['data_project_people']['people']));
                        }
                        return $acc;
                    }, []);
                    if(count($data_projects_people)){
                        $people['data_project_people'] = ['label' => 'Data Project Maintainers', 'people' => $data_projects_people ];
                    }
                }
                return $people;
            },
            'data_project' => function(){
                $people = [];
                if (isset($this->acf_fields['data_project_people'])){
                    $people['data_project_people'] = [
                        'label' => 'Data Project Maintainers',
                        'people' => $this->deduplicate_bcodmo_people($this->acf_fields['data_project_people'])
                    ];
                }
                return $people;
            },
            'protocol' => function(){
                $people = [];
                if (isset($this->acf_fields['protocol_authors'])){
                    $people['protocol_authors'] = ['label' => 'Authors', 'people' => $this->acf_fields['protocol_authors']];
                }
                return $people;
            },
            'award' => function(){
                $people = [];

                $role_order = ['pi', 'awardee', 'advisor', 'host'];

                if (isset($this->acf_fields['award_participants'])){
                    $award_participants = array_map(function($row){
                        if (
                            isset($this->acf_fields['award_type']) && count($this->acf_fields['award_type'])
                            && in_array($this->acf_fields['award_type'][0]->slug, ['postdoctoral-fellowship', 'graduate-fellowship'])
                            && in_array(strtolower($row['role']), ['awardee', 'pi'])
                        ) {
                            $row['current_placement'] = get_field('person_current_placement', $row['person']->ID);
                            $row['degree'] = get_field('person_degree', $row['person']->ID);
                        }
                        return $row;
                    }, $this->acf_fields['award_participants']);

                    $participants_by_role = array_reduce($award_participants, function($acc, $row){
                        $role_slug = strtolower($row['role']);
                        if (!isset($acc[$role_slug])){
                            $acc[$role_slug] = [ 'label' => $row['role'], 'people' => []];
                        }
                        $acc[$role_slug]['people'][] = $row;
                        return $acc;
                    }, []);

                    $ordered_participants_by_role = [];
                    $unordered_participants_by_role = [];

                    foreach($role_order as $role_slug) {
                        if (isset($participants_by_role[$role_slug])){
                            $ordered_participants_by_role[] = $participants_by_role[$role_slug];
                            unset($participants_by_role[$role_slug]);
                        }
                    }
                    if (count($participants_by_role)){
                        $unordered_participants_by_role = array_values($participants_by_role);
                    }

                    $people = array_values(array_merge($ordered_participants_by_role, $unordered_participants_by_role));
                }

                return $people;
            }
        ];

        $post_type_to_people_callback = $post_types_to_people_callbacks[$this->post_type] ?? false;

        if ($post_type_to_people_callback && is_callable($post_type_to_people_callback)){
            $people = $post_type_to_people_callback();
        }

        $this->people = $people;

        return $people;
    }
    public function get_search_excerpt(){
        if (isset($_GET['s'])
            && $_GET['s']
            && strpos($this->post_excerpt, '<mark class="searchwp-highlight">') !== false)
        {
            $stripped_content = wp_strip_all_tags($this->post_excerpt);
            $words = explode(" ", $stripped_content);
            $count = count($words);
            if ($count > 5) {
                return $this->post_excerpt;
            }
        }
        return false;
    }

    private function posts_to_entities($posts){
        return $posts ? array_map(function($post){ return new Entity($post); }, $posts) : [];
    }
    private function deduplicate_bcodmo_people($rows){
        $id_to_rows_and_contact = [];
        foreach ($rows as $i => $row){
            $id = $row['person']->ID;
            $contact = false;
            if (isset($row['contact'])){
                $contact = $row['contact'] ? true: false;
            } else if (strtolower($row['role']) === 'contact') {
                $contact = true;
            }
            $id_to_rows_and_contact[$id] = $id_to_rows_and_contact[$id] ?? [];
            $id_to_rows_and_contact[$id][] = ["row" => $i, "contact" => $contact];
        }
        // Preserve the order of people, BCO-DMO lists contacts last
        foreach ($id_to_rows_and_contact as $id => $rows_and_contacts){
            if (count($rows_and_contacts) > 1){
                $is_contact = false;
                foreach($rows_and_contacts as $row_and_contact){
                    $is_contact = !$is_contact ? $row_and_contact["contact"] : $is_contact;
                }
                if ($is_contact){
                    foreach($rows_and_contacts as $i => $row_and_contact){
                        if ($i === 0) {
                            $row_and_contact["contact"] = true;
                        } else {
                            unset($rows[$row_and_contact["row"]]);
                        }
                    }
                }
            }
        }
        return array_values($rows);
    }
    private function get_post_type_labels(){
        $post_type_obj = get_post_type_object($this->post_type);
        return [
            "singular" => $post_type_obj->labels->singular_name,
            "plural" => $post_type_obj->label
        ];
    }
    private function get_date_expired(){
        $expiration_date = get_post_meta($this->post_id, '_expiration-date');
        return $expiration_date[0] ?? false;
    }
    private function get_acf_fields(){
        return get_fields($this->post_id);
    }
    private function get_acf_field_meta(){
        $field_groups = acf_get_field_groups(['post_id' => $this->post_id ]);

        return array_reduce(
            $field_groups,
            function($acc, $group){
                $group_field_meta = acf_get_fields($group['key']);

                $group_field_meta_by_name = array_reduce(
                    $group_field_meta,
                    function($acc, $field){
                        $acc[$field['name']] = $field;
                        return $acc;
                    },
                []);

                return array_merge( $acc, $group_field_meta_by_name );
            },
        []);
    }
    private function get_taxonomies(){
        $all_taxonomies = get_taxonomies('', 'names');
        $taxonomy_slug_to_plural_field_name = [
            'award_type' => 'award_type_plural_name',
            'publication_type' => 'publication_type_pural_name'
        ];

        $terms = wp_get_post_terms($this->post_id, $all_taxonomies);

        $terms_by_taxonomy_slug = array_reduce(
            $terms,
            function($acc, $term) use ($taxonomy_slug_to_plural_field_name) {
                $taxonomy_slug = $term->taxonomy;
                $acc[$taxonomy_slug] = $acc[$taxonomy_slug] ?? [];

                if (isset($taxonomy_slug_to_plural_field_name[$taxonomy_slug])){
                    $plural_field_name = $taxonomy_slug_to_plural_field_name[$taxonomy_slug];
                    $term->plural = get_field($plural_field_name, $term) ?? false;
                }

                $acc[$taxonomy_slug][] = $term;
                return $acc;
            }, []);

        return $terms_by_taxonomy_slug;
    }
    private function get_search_result_weight(){
        global $searchwp;
        return $searchwp->results_weights[$this->post_id]['weight'] ?? 0;
    }
    private function convert_date_strings_to_date_objects(){
        
        $this->date_created = \DateTime::createFromFormat('Y-m-d H:i:s', $this->date_created);
        $this->date_modified = \DateTime::createFromFormat('Y-m-d H:i:s', $this->date_modified);
        $this->date_expired = $this->date_expired ? new \DateTime("@$this->date_expired") : false;

        foreach ($this->acf_field_meta as $field_name => $field_meta){
            if ($field_meta['type'] === 'date_picker' && $this->acf_fields[$field_name]){
                $date_string = $this->acf_fields[$field_name];
                $return_format = $field_meta['return_format'];
                $this->acf_fields[$field_name] = \DateTime::createFromFormat($return_format, $date_string);
            }
        }
        
    }

}