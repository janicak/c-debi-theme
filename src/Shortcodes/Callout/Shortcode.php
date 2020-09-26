<?php

namespace C_DEBI_Theme\Shortcodes\Callout;

use C_DEBI_Theme\Shortcodes;

class Shortcode extends Shortcodes\Shortcode {

    public function render($atts, $content = null){
        $this->enqueue_assets();
        return $this->blade_runner->run('Callout.index', ['classname' => $this->shortcode_tag, 'content' => $content]);
    }

}