<?php

namespace C_DEBI_Theme\Shortcodes\NewsGrid;

use C_DEBI_Theme\Shortcodes;
use C_DEBI_Theme\Entity;

class Shortcode extends Shortcodes\Shortcode {

    public function render($atts, $content = null){
        $this->enqueue_assets();

        $category_entity_count_limits = [
            'announcement' => 3,
            'press' => 5,
            'publication' => 5,
            'spotlight' => 5
        ];

        $categories = $this->get_sorted_unexpired_entities_by_category($category_entity_count_limits);

        return $this->blade_runner->run('NewsGrid.index', ['categories' => $categories]);
    }

    public function get_sorted_unexpired_entities_by_category($category_entity_count_limits){
        $post_entities = array_map(
            function($post){ return new Entity($post); },
            (new \WP_Query([
                'post_type' => 'post',
                'numberposts' => -1,
                'tax_query' => [
                    [
                        'taxonomy' => 'category',
                        'field' => 'slug',
                        'terms' => 'expired',
                        'operator' => 'NOT IN'
                    ]
                ],
            ]))->posts
        );

        $publication_entities = array_map(
            function($post){ return new Entity($post); },
            (new \WP_Query([
                'post_type' => 'publication',
                'numberposts' => 15,
            ]))->posts
        );

        $entities = array_merge($publication_entities, $post_entities);

        // Differentiate entities by category, treating post_type === 'publication' as a category
        $entities_by_category = array_reduce($entities, function($acc, $entity){
            if (isset($entity->taxonomies['category'])){
                foreach ($entity->taxonomies['category'] as $term){
                    $slug = $term->slug;
                    $label = $term->name;
                    $is_expiring = $entity->date_expired ? 'expiring' : 'not_expiring';
                    if (!isset($acc[$slug])){
                        $acc[$slug] = ['label' => $label, 'expiring' => [], 'not_expiring' => []];
                    }
                    $acc[$slug][$is_expiring ][] = $entity;
                }
            }
            if ($entity->post_type === 'publication'){
                $acc['publication']['not_expiring'][] = $entity;
            }
            return $acc;
        }, ['publication' => ['label' => 'Publications', 'expiring' => [], 'not_expiring' => []]]);

        // Sort entities by expiration date, then non-expiring entities sorted by publication/post date;
        // Cap entities at category slug count limits
        foreach($entities_by_category as $slug => &$data){
            usort($data['expiring'], function($a, $b){
                return $a->date_expired < $b->date_expired ? -1 : 1;
            });

            usort($data['not_expiring'], function($a, $b){
                $a_date = isset($a->acf_fields['publication_date_published']) ?? $a->date_created;
                $b_date = isset($b->acf_fields['publication_date_published']) ?? $b->date_created;
                return ($a_date > $b_date) ? -1 : 1;
            });

            $posts = array_merge($data['expiring'], $data['not_expiring']);
            unset($data['expiring']);
            unset($data['not_expiring']);

            $count_limit = isset($category_entity_count_limits[$slug]) ? $category_entity_count_limits[$slug] : count($posts);
            $data['posts'] = array_slice($posts, 0, $count_limit);

        }

        // Sort categories by slug
        ksort($entities_by_category);

        return $entities_by_category;
    }

}