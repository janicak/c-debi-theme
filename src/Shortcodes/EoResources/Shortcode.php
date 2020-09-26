<?php

namespace C_DEBI_Theme\Shortcodes\EoResources;

use C_DEBI_Theme\Shortcodes;
use C_DEBI_Theme\Entity;

class Shortcode extends Shortcodes\Shortcode {

    public function render($atts, $content = null){
        $this->enqueue_assets();

        $entities = array_map(
            function($post){ return new Entity($post); },
            (new \WP_Query([
                'post_type' => 'eo_resource',
                'numberposts' => -1,
                'order' => 'asc',
                'orderby' => 'title'
            ]))->posts
        );

        $filters = array_reduce($entities, function($acc, $entity){
            $entity_taxonomies = [
                'audience' => $entity->taxonomies['audience'] ?? false,
                'resource_type' => $entity->taxonomies['resource_type'] ?? false
            ];
            foreach ($entity_taxonomies as $taxonomy => $terms){
                if ($terms){
                    foreach($terms as $term){
                        $acc[$taxonomy]['options'][$term->slug] = $acc[$taxonomy]['options'][$term->slug] ?? $term->name;
                    }
                }
            }
            return $acc;
        }, [
            'audience' => [ 'label' => 'audiences', 'options' => [] ],
            'resource_type' => [ 'label' => 'Resource Types', 'options' => [] ]
        ]);

        return $this->blade_runner->run('EoResources.index', ['entities' => $entities, 'filters' => $filters]);
    }

}