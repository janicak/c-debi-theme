<?php

namespace C_DEBI_Theme;

use \Routes as CustomRoutes;

class Routes {

    private $current_routes = [];
    private $path = '';

    public function __construct( $asset_loader) {
        $this->asset_loader = $asset_loader;
        $this->path = $this->getPathInfo();
        $this->create_virtual_pages();
    }

    private function get_current_routes() {
        $current_routes = [];

        if (!is_admin()){
            $slug = 'front_end';
            $frontend_route = $this->get_current_route($slug);
            if ($frontend_route){
                $current_routes[] = $frontend_route;
            }
        }

        $slug = false;

        if( isset( $_REQUEST[ 'route' ] ) ) {
            $slug = $_REQUEST[ 'route' ];

        } else if ( isset($_GET['action']) && $_GET['action'] === 'edit') {
            $slug = 'edit_post';

        }else if ( is_front_page() ){
            $slug = 'home';

        } else if ( is_singular('page' )){
            $slug = 'page';

        } else if ( is_search() ){
            $slug = 'search';

        } else if (preg_match("/^\/search(\/[a-z]+\/?)?/", $this->path)){
            $slug = 'alt_search';
            
        }

        $current_route = $slug ? $this->get_current_route($slug) : false;

        if ($current_route) { $current_routes[] = $current_route; }

        return $current_routes;
    }

    private function get_current_route($slug){
        $route_slugs_to_classnames = [
            'front_end' => 'C_DEBI_Theme\\Routes\\FrontEnd\\Route',
            'edit_post' => 'C_DEBI_Theme\\Routes\\EditPost\\Route',
            'page' => 'C_DEBI_Theme\\Routes\\Page\\Route',
            'search' => 'C_DEBI_Theme\\Routes\\Search\\Route',
            'home' => 'C_DEBI_Theme\\Routes\\Home\\Route',
            'alt_search' => 'C_DEBI_Theme\\Routes\\AltSearch\\Route',
        ];

        $classname = $route_slugs_to_classnames[$slug] ?? false;

        if (!$classname) {
            return false;
        }

        $class = new $classname($slug, $this->asset_loader);

        if ($class){
            return ['slug' => $slug, 'class' => $class];
        }

        return false;
    }

    public function register_hooks(){
        add_action('template_redirect', [$this, 'register_route_hooks']);
    }

    // See https://carlalexander.ca/wordpress-adventurous-rewrite-api/
    public function create_virtual_pages(){
        add_action('after_switch_theme', function(){
            add_option('c-debi-theme_flush_rewrite_rules', true);
        });
        add_action('init', function(){
            if (get_option('c-debi-theme_flush_rewrite_rules')) {
                flush_rewrite_rules();
                delete_option('c-debi-theme_flush_rewrite_rules');
            }
        }, 99);
        add_action( 'init', function (){
            add_rewrite_rule('^search/?$', 'index.php?is_search=1', 'top');
            add_rewrite_rule('^search/([a-z]+)/?$', 'index.php?is_search=1&search_type=$matches[1]', 'top');
        }, 10, 0);
        add_filter( 'query_vars', function( $query_vars ){
            $query_vars[] = 'is_search';
            $query_vars[] = 'search_type';
            return $query_vars;
        } );
        add_filter( 'template_include', function($template){
            if ( intval( get_query_var( 'is_search' ) ) ) {
                return get_stylesheet_directory() . '/src/Routes/AltSearch/templates/alt-search.php';
            }
            return $template;
        } );
    }

    public function register_route_hooks(){
        $this->current_routes = $this->get_current_routes();
        if (count($this->current_routes)){
            foreach($this->current_routes as $route){
                $route['class']->register_hooks();
            }
        }
    }

    private function getPathInfo() {
        $home_path = parse_url( home_url(), PHP_URL_PATH );
        return preg_replace( "#^/?{$home_path}/#", '/', add_query_arg( array() ) );
    }
}