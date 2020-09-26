<?php

namespace C_DEBI_Theme;

use eftec\bladeone\BladeOne;

trait BladeOneExtras {

    // based on https://github.com/Hedronium/SpacelessBlade/blob/master/src/SpacelessBladeProvider.php
    public function compileSpaceless(){
        return '<?php ob_start() ?>';
    }
    public function compileEndSpaceless(){
        return "<?php echo preg_replace('/>\\s+</', '><', ob_get_clean()); ?>";
    }
}

class BladeRunner extends BladeOne {
    use BladeOneExtras;
    public function __construct( $templatePath = null, $compiledPath = null, $mode = 0 ) {
        $stylesheet_dir = get_stylesheet_directory();
        $templatePath = [
            $stylesheet_dir . '/src/views',
            $stylesheet_dir . '/src/Routes',
            $stylesheet_dir . '/src/Shortcodes',
        ];
        $compiledPath = wp_upload_dir()[ 'basedir' ] . '/c-debi_theme';
        $mode = 5;
        parent::__construct( $templatePath, $compiledPath, $mode );
    }
}