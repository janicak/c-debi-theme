<?php

namespace C_DEBI_Theme\Shortcodes\CDEBISlider;

use C_DEBI_Theme\Shortcodes;

class Shortcode extends Shortcodes\Shortcode {

    public function render($atts, $content = null){
        $this->enqueue_assets();

        $slides = include(dirname( __FILE__ ) . '/slide-config.php');

        return $this->blade_runner->run('CDEBISlider.index', ['slides' => $slides]);
    }

}