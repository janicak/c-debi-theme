const pkg = require('./package.json');

const {
	getFileLoaderOptions,
	issuerForNonStyleFiles,
	issuerForStyleFiles,
	babelLoader,
	fileLoader,
} = require('@wpackio/scripts');

module.exports = {
	// Project Identity
	appName: 'cdebiTheme', // Unique name of your project
	type: 'theme', // Plugin or theme
	slug: 'c-debi-theme', // Plugin or Theme slug, basically the directory name under `wp-content/<themes|plugins>`
	// Used to generate banners on top of compiled stuff
	bannerConfig: {
		name: 'cdebiTheme',
		author: '',
		license: 'UNLICENSED',
		link: 'UNLICENSED',
		version: pkg.version,
		copyrightText:
			'This software is released under the UNLICENSED License\nhttps://opensource.org/licenses/UNLICENSED',
		credit: true,
	},
	// Files we need to compile, and where to put
	files: [
		{
			name: 'app',
			entry: {
				tinymce: [ './src/Routes/EditPost/tinymce.js' ],
				front_end: [ './src/Routes/FrontEnd/index.js' ],
				page: ['./src/Routes/Page/index.js'],
				search: ['./src/Routes/Search/index.js'],
				home: [
					'./src/Routes/Home/index.js',
					'./src/Shortcodes/CDEBISlider/index.js',
					'./src/Shortcodes/NewsGrid/index.js'
				],
				alt_search: ['./src/Routes/AltSearch/index.js'],
				shortcode_callout: ['./src/Shortcodes/Callout/index.js'],
				shortcode_eo_resources: ['./src/Shortcodes/EoResources/index.js'],
				shortcode_mc4wp_gridform: ['./src/Shortcodes/MC4WPGridform/index.js'],
			},
			webpackConfig: (config, merge, appDir, isDev) => {
				const svgoLoader = {
					loader: 'svgo-loader',
					options: {
						plugins: [
							{ removeTitle: true },
							{ convertColors: { shorthex: false } },
							{ convertPathData: false },
						],
					},
				};
				// create module rules
				const newConfig = {
					module: {
						rules: [
							// SVGO Loader
							// https://github.com/rpominov/svgo-loader
							// This rule handles SVG for javascript files
							{
								test: /\.svg(\?v=\d+\.\d+\.\d+)?$/,
								use: [
									{
										loader: fileLoader,
										options: getFileLoaderOptions(
											appDir,
											isDev,
											false
										),
									},
									svgoLoader,
								],
								issuer: issuerForNonStyleFiles,
							},
							// This rule handles SVG for style files
							{
								test: /\.svg(\?v=\d+\.\d+\.\d+)?$/,
								use: [
									{
										loader: fileLoader,
										options: getFileLoaderOptions(
											appDir,
											isDev,
											true
										),
									},
									svgoLoader,
								],
								issuer: issuerForStyleFiles,
							},
						],
					},
					resolve: {
						mainFiles: [ 'index' ]
					}
				};
				// merge the new module.rules with webpack-merge api
				return merge(config, newConfig);
			},
		}
	],
	// Output path relative to the context directory
	// We need relative path here, else, we can not map to publicPath
	outputPath: 'dist',
	// Project specific config
	// Needs react(jsx)?
	hasReact: true,
	// Disable react refresh
	disableReactRefresh: false,
	// Needs sass?
	hasSass: true,
	// Needs less?
	hasLess: false,
	// Needs flowtype?
	hasFlow: false,
	// Externals
	// <https://webpack.js.org/configuration/externals/>
	externals: {
		jquery: 'jQuery',
	},
	// Webpack Aliases
	// <https://webpack.js.org/configuration/resolve/#resolve-alias>
	alias: undefined,
	// Show overlay on development
	errorOverlay: true,
	// Auto optimization by webpack
	// Split all common chunks with default config
	// <https://webpack.js.org/plugins/split-chunks-plugin/#optimization-splitchunks>
	// Won't hurt because we use PHP to automate loading
	optimizeSplitChunks: true,
	// Usually PHP and other files to watch and reload when changed
	watch: './inc|includes/**/*.php',
	// Files that you want to copy to your ultimate theme/plugin package
	// Supports glob matching from minimatch
	// @link <https://github.com/isaacs/minimatch#usage>
	packageFiles: [
		'inc/**',
		'vendor/**',
		'dist/**',
		'*.php',
		'*.md',
		'readme.txt',
		'languages/**',
		'layouts/**',
		'LICENSE',
		'*.css',
	],
	// Path to package directory, relative to the root
	packageDirPath: 'package',
	jsBabelOverride: defaults => ({
		...defaults,
		plugins: ['react-hot-loader/babel'],
	}),
};
