<?php

namespace C_DEBI_Theme;

class Init{
    private $hook_loader;
    public $asset_loader;
    public $blade_runner;

    public function __construct() {
        $this->init_uploads_dir();

        $this->hook_loader = new HookLoader();
        $this->asset_loader = new AssetLoader();
        $this->blade_runner = new BladeRunner();

        $this->define_parent_theme_hooks();
        $this->define_route_hooks();
        $this->define_shortcode_hooks();
    }

    private function init_uploads_dir(){
        $theme_uploads_path = wp_upload_dir()[ 'basedir' ] . '/c-debi_theme';
        if( !is_dir( $theme_uploads_path ) ) {
            wp_mkdir_p( $theme_uploads_path );
        }
    }

    private function define_route_hooks(){
        $theme_routes = new Routes($this->asset_loader);
        $this->hook_loader->add_action( 'init', $theme_routes, 'register_hooks' );
    }

    private function define_shortcode_hooks(){
        $theme_shortcodes = new Shortcodes($this->asset_loader, $this->blade_runner);
        $this->hook_loader->add_action('get_header', $theme_shortcodes, 'register_hooks');
    }

    private function define_parent_theme_hooks(){
        $this->hook_loader->add_filter('us_files_search_paths', $this, 'add_template_search_paths');
    }

    public function add_template_search_paths($search_paths){
        $stylesheet_dir = get_stylesheet_directory();
        $parent_dirs = [ $stylesheet_dir . '/src/Routes'];
        foreach ($parent_dirs as $parent_dir){
            $template_dirs = array_reduce(scandir($parent_dir), function($acc, $filename) use ($parent_dir){
                if (!in_array($filename, ['.', '..']) && is_dir($parent_dir . '/' . $filename . '/templates')){
                    $acc[] = $parent_dir . '/' . $filename . '/';
                }
                return $acc;
            }, []);
            $search_paths = array_merge($template_dirs, $search_paths);
        }
        return $search_paths;
    }

    public function run_hook_loader() {
        $this->hook_loader->run();
    }
}


