<?php

namespace C_DEBI_Theme\Routes\AltSearch;

use C_DEBI_Theme\Entity;

class Route extends \C_DEBI_Theme\Routes\Route {

    public function register_hooks(){
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        wp_enqueue_style( 'dashicons' );
        add_action('wp_ajax_nopriv_ajax_req', [$this, 'handle_ajax_request']);
        $this->document_title_hook();
    }

    public function document_title_hook(){
        $search_type = get_query_var('search_type');

        $search_type_to_doc_title = [
            'publications' => 'Publications'
        ];

        $title = $search_type_to_doc_title[$search_type] ?? 'Search';

        add_filter('document_title_parts', function($title_parts) use ($title){
            $title_parts['title'] = $title;
            return $title_parts;
        });
    }

    public function data_to_scripts() {
        $data = [
            'title' => 'Search',
            'items' => []
        ];

        $search_type = get_query_var('search_type');

        $search_type_to_data = [
            'publications' => [
                'title' => 'Publications',
                'items_callback' => 'fast_publication_callback'
            ]
        ];

        if (isset($search_type_to_data[$search_type])){
            ['title' => $title, 'items_callback' => $items_callback] = $search_type_to_data[$search_type];
            $start_time = microtime(true);
            $data = [
                'title' => $title,
                'items' => $this->{$items_callback}()
            ];
            $end_time = microtime(true);
            $data['exec_time'] = $end_time - $start_time;
        }

        return $data;
    }

    private function unset_entity_props($entity, $props){
        foreach ($props as $entity_prop){
            unset($entity->{$entity_prop});
        }
    }

    private function unset_acf_fields($entity, $field_names){
        foreach($field_names as $acf_field_name){
            unset($entity->acf_fields[$acf_field_name]);
        }
    }

    private function reduce_date($entity, $field_name, $acf_field = true){
        if ($acf_field && isset($entity->acf_fields[$field_name])){
            $entity->acf_fields[$field_name] = $entity->acf_fields[$field_name]->format('Ymd');
        } else if (isset($entity->{$field_name})) {
            $entity->{$field_name} = $entity->{$field_name}->format('Ymd');
        }
    }

    private function reduce_post($post){
        return [
            'post_title' => $post->post_title,
            'permalink' => get_permalink($post->ID)
        ];
    }

    private function reduce_term($term){
        return [
            'name' => $term->name,
            'slug' => $term->slug
        ];
    }

    private function fast_publication_callback(){
        $posts = (new \WP_Query(['post_type' => 'publication', 'posts_per_page' => -1]))->posts;
        return array_map(function($post){
            $reduced_post = [
                'ID' => $post->ID,
                'post_title' => $post->post_title,
                'post_content' => $post->post_content,
                'publication_contribution_number' => get_field('publication_contribution_number', $post->ID),
                'publication_publisher_title' => get_field('publication_publisher_title', $post->ID),
                'publication_file' => get_field('publication_file', $post->ID),
                'publication_date_published' => get_field('publication_date_published', $post->ID),
                'publication_url' => get_field('publication_url', $post->ID)
            ];
            $field_names_to_callbacks = [
                'publication_type' => function($post){
                    $field_val = get_field('publication_type', $post->ID);
                    return $field_val ? $this->reduce_term($field_val) : false;
                },
                'publication_authors' => function($post){
                    $field_val = get_field('publication_authors', $post->ID);
                    foreach($field_val as &$row){
                        if ($row['person']){
                            $row = $this->reduce_post($row['person']);
                        } else {
                            $row = [
                                'post_title' => $row['given'] . ' ' .$row['family'],
                                'permalink' => null
                            ];
                        }
                    }
                    return $field_val;
                },
                'publication_editors' => function($post){
                    $field_val = get_field('publication_editors', $post->ID);
                    foreach($field_val as &$row){
                        if ($row['person']){
                            $row = $this->reduce_post($row['person']);
                        } else {
                            $row = [
                                'post_title' => $row['given'] . ' ' .$row['family'],
                                'permalink' => null
                            ];
                        }
                    }
                    return $field_val;
                },
                'publication_awards' => function($post){
                    $field_val = get_field('publication_awards', $post->ID);
                    foreach($field_val as &$rel_post){
                        $rel_post = $this->reduce_post($rel_post);
                    }
                    return $field_val;
                }
            ];
            foreach($field_names_to_callbacks as $field_name => $callback){
                $reduced_post[$field_name] = is_callable($callback) ? $callback($post) : false;
            }
            return $reduced_post;
        }, $posts);
    }

    private function slow_publication_callback(){
        $posts = (new \WP_Query(['post_type' => 'publication', 'posts_per_page' => -1]))->posts;
        $entities = array_map(function($post){
            $entity = new Entity($post);

            $this->unset_entity_props($entity, ['post_type', 'post_type_labels', 'acf_field_meta',
                'post_excerpt', 'date_created', 'date_modified', 'taxonomies', 'related_entities',
                'date_expired', 'people', 'search_result_weight']);

            $this->unset_acf_fields($entity, ['publication_doi']);

            $this->reduce_date($entity, 'publication_date_published');

            foreach (['publication_authors', 'publication_editors'] as $field_name){
                if ($entity->acf_fields[$field_name]){
                    foreach ($entity->acf_fields[$field_name] as &$row){
                        if ($row['person']){
                            $row = $this->reduce_post($row['person']);
                        } else {
                            $row = [
                                'post_title' => $row['given'] . ' ' .$row['family'],
                                'permalink' => null
                            ];
                        }
                    }
                }
            }

            foreach ($entity->acf_fields['publication_awards'] as &$award){
                $award = $this->reduce_post($award);
            }

            foreach (['publication_type'] as $field_name){
                if ($entity->acf_fields[$field_name]){
                    $entity->acf_fields[$field_name] = $this->reduce_term($entity->acf_fields[$field_name]);
                }
            }

            return $entity;
        }, $posts);
        return $entities;
    }

}