<?php

namespace C_DEBI_Theme\Shortcodes\MC4WPGridForm;

use C_DEBI_Theme\Shortcodes;

class Shortcode extends Shortcodes\Shortcode {

    public function render($atts, $content = null){
        $this->enqueue_assets();

        $id = $atts['id'] ?? '';
        $shortcode_tag = 'mc4wp_form' . $id;

        if (shortcode_exists($shortcode_tag)){
            return do_shortcode('['.$shortcode_tag.']') .
                '<script>
                    var forms = document.getElementsByClassName("mc4wp-form");
                    Array.prototype.forEach.call(forms, function(form){
                        form.className += " grid-form";
                    });
                </script>';
        }

        return false;
    }

}