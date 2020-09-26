<?php

namespace C_DEBI_Theme;

class Shortcodes {

    private $current_route;

    public function __construct( $asset_loader, $blade_runner) {
        $this->asset_loader = $asset_loader;
        $this->blade_runner = $blade_runner;
    }

    public function register_hooks(){
        $shortcode_tag_to_classname = [
            'callout' => 'C_DEBI_Theme\\Shortcodes\\Callout\\Shortcode',
            'warning' => 'C_DEBI_Theme\\Shortcodes\\Callout\\Shortcode',
            'eo-resources' => 'C_DEBI_Theme\\Shortcodes\\EoResources\\Shortcode',
            'cdebi-slider' => 'C_DEBI_Theme\\Shortcodes\\CDEBISlider\\Shortcode',
            'mc4wp_gridform' => 'C_DEBI_Theme\\Shortcodes\\MC4WPGridform\\Shortcode',
            'news-grid' => 'C_DEBI_Theme\\Shortcodes\\NewsGrid\\Shortcode',
        ];

        foreach ($shortcode_tag_to_classname as $tag => $classname){
            $shortcode = new $classname($tag, $this->asset_loader, $this->blade_runner);
            add_shortcode($tag, [$shortcode, 'render']);
        }
    }
}