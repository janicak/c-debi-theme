<?php

namespace C_DEBI_Theme\Routes;

abstract class Route {
    protected $route_slug;
    protected $asset_loader;

    public function __construct( $route_slug, $asset_loader ) {
        $this->route_slug = $route_slug;
        $this->asset_loader = $asset_loader;
    }

    public function register_hooks(){
        if (is_admin()) {
            add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
            add_action('wp_ajax_ajax_req', [$this, 'handle_ajax_request']);
        } else {
            add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
            add_action('wp_ajax_nopriv_ajax_req', [$this, 'handle_ajax_request']);
        }
    }

    protected function data_to_scripts() {
        return [];
    }

    public function enqueue_assets() {
        // get manifest of webpack-generated assets from the primary, 'app', compiler
        $app_assets = $this->asset_loader->getManifest( 'app' );

        // the code-splitting entry-point for the route corresponds to the route slug
        $js_entry_point = $route_id = $this->route_slug;

        if( isset( $app_assets[ 'wpackioEp' ][ $js_entry_point ] ) ) {

            $route_assets = $this->asset_loader->enqueue( 'app', $js_entry_point, [] );

            // pass ajax connection info and any seed data for the route's javascript app
            wp_localize_script(
                array_pop( $route_assets[ 'js' ] )[ 'handle' ],
                $route_id,
                [
                    'ajax_url' => admin_url( 'admin-ajax.php' ),
                    'action' => 'ajax_req',
                    'nonce' => wp_create_nonce(),
                    // we pass in a route id for the client to use in requests, so the server
                    // can re-instantiate the correct route to handle the response
                    'route' => $route_id,
                    'data' => $this->data_to_scripts()
                ]
            );
        }
    }

    public function handle_ajax_request() {

        if( wp_verify_nonce( $_REQUEST[ 'nonce' ] ) ) {

            $res = null;

            foreach( $_REQUEST[ 'reqs' ] as $req ) {

                $method = $req[ 'method' ];
                $args = isset( $req[ 'args' ] ) ? $req[ 'args' ] : null;
                $data = null;

                // Method is expected to be a static method of the Route
                try {
                    $ReflectionClass = new \ReflectionClass(get_class($this));
                    $ReflectionMethod = $ReflectionClass->getMethod($method);
                    if (
                        $ReflectionMethod && $ReflectionMethod->isStatic()
                    ) {
                        $data = $this::$method( $args );
                    } else {
                        $data = "Method '" . $method . "' not found.";
                        wp_send_json_error($data);
                    }
                } catch (\ReflectionException $e) {
                    $data = "Caught exception: " . $e->getMessage();
                    wp_send_json_error($data);
                }

                // Group the data response under a 'key' if requested
                $req_key = isset( $req[ 'key' ] ) ? $req[ 'key' ] : null;
                if( $req_key ) {
                    $res[ $req_key ] = $data;
                } else {
                    $res = $data;
                }
            }

            wp_send_json_success($res);

        } else {
            wp_send_json_error("Invalid nonce");
        }
    }
}

