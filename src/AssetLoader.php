<?php

namespace C_DEBI_Theme;

use WPackio\Enqueue;

/**
 * Class C_DEBI_Theme\AssetLoader
 *
 * Constructs WPackio\Enqueue class with plugin settings. WPackio\Enqueue provides an API to enqueue scripts,
 * styles and assets compiled by Webpack.
 *
 * @since 1.0.0
 *
 * @link https://github.com/swashata/wp-webpack-script
 *
 * @var AssetLoader $asset_loader
 */
class AssetLoader extends Enqueue {

    public function __construct() {
        parent::__construct(
            'zephyrChild',
            'dist',
            '1.0.0',
            'theme',
            false,
            'child'
        );
    }

}