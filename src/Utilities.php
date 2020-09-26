<?php

namespace C_DEBI_Theme;

class Utilities {

    static function remove_empty_p($content) {
        $content = force_balance_tags($content);
        $stripped_content = preg_replace('/<p>(?:\s|&nbsp;|\xc2|\xa0)*?<\/p>/i', '', $content);
        return $stripped_content;
    }
}