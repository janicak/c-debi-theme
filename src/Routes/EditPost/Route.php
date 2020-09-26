<?php

namespace C_DEBI_Theme\Routes\EditPost;

class Route extends \C_DEBI_Theme\Routes\Route {

    public function register_hooks(){
        /* See http://wordpress.stackexchange.com/questions/128931/tinymce-adding-css-to-format-dropdown */
        add_filter( 'mce_buttons_2', [$this, 'mce_style_dropdown'] );
        add_filter( 'tiny_mce_before_init', [$this, 'mce_style_dropdown_styles'] );
        add_filter( 'mce_css', [$this, 'mce_css_register']);
    }

    public function mce_style_dropdown( $buttons ) {
        array_unshift( $buttons, 'styleselect' );
        return $buttons;
    }

    public function mce_style_dropdown_styles( $settings ) {
        $style_formats = array(
            array(
                'title' => 'Callout',
                'block' => 'div',
                'classes' => 'callout',
                'wrapper' => true
            ),
            array(
                'title' => 'Warning',
                'block' => 'div',
                'classes' => 'warning',
                'wrapper' => true
            ),
            array(
                'title' => 'Also link',
                'selector' => 'a',
                'classes' => 'also'
            ),
            array(
                'title' => 'File link',
                'selector' => 'a',
                'classes' => 'file'
            ),
            array(
                'title' => 'PDF link',
                'selector' => 'a',
                'classes' => 'pdf'
            ),
            array(
                'title' => 'Disabled link',
                'selector' => 'a',
                'attributes' => array(
                    'data-disabled' => "true"
                )
            ),
            array(
                'title' => 'Blockquote',
                'block' => 'blockquote',
                'wrapper' => true
            )

        );
        $settings['style_formats'] = json_encode( $style_formats );
        return $settings;
    }

    public function mce_css_register($url) {

        if ( ! empty( $url ) )
            $url .= ',';

        $assets = $this->asset_loader->getAssets('app', 'tinymce', [ 'js' => false, 'css' => true ]);
        $tinymce_css_url = $assets['css'][0]['url'];

        // Retrieves the plugin directory URL
        // Change the path here if using different directories
        $url .= $tinymce_css_url;

        return $url;
    }


}