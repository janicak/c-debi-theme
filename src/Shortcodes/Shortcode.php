<?php

namespace C_DEBI_Theme\Shortcodes;

abstract class Shortcode {

    protected $shortcode_tag;
    protected $asset_loader;
    protected $blade_runner;
    protected $assets = null;

    public function __construct($shortcode_tag, $asset_loader, $blade_runner){
        $this->shortcode_tag = $shortcode_tag;
        $this->asset_loader = $asset_loader;
        $this->blade_runner = $blade_runner;
        $this->register_assets();
    }

    public function render($atts, $content = null){
        // $this->enqueue_assets()
        // return $this->blade_runner->run('blade.template', ['content' => $content]);
    }

    public function register_assets(){
        $app_assets = $this->asset_loader->getManifest( 'app' );

        $js_entry_point = 'shortcode_' . str_replace('-', '_', $this->shortcode_tag);

        if( isset( $app_assets[ 'wpackioEp' ][ $js_entry_point ] ) ) {
            $assets = $this->asset_loader->register( 'app', $js_entry_point, [] );
            $this->assets = $assets;
        }
    }

    protected function enqueue_assets(){
        if ($this->assets){
            wp_enqueue_script(
                array_pop($this->assets['js'])['handle']
            );
            wp_localize_script(
                array_pop( $this->assets[ 'js' ] )[ 'handle' ],
                'c_debi_theme_shortcode_' . str_replace('-', '_', $this->shortcode_tag),
                [
                    'ajax_url' => admin_url( 'admin-ajax.php' ),
                    'action' => 'ajax_req',
                    'nonce' => wp_create_nonce(),
                    'data' => $this->data_to_scripts()
                ]
            );
            wp_enqueue_style(
                array_pop($this->assets['css'])['handle']
            );
        }
    }

    protected function data_to_scripts(){
        return null;
    }
}