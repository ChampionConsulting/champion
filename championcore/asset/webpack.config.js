const path = require('path');

const { DefinePlugin } = require('webpack');

module.exports = {

	mode: 'development',

	devtool: 'inline-source-map', // set to false for production
	
	entry: {

		// widgets
		'widget/ai-image-generation': './js/widget/ai-image-generation/ai-image-generation.ts',
		'widget/blog_featured_image': './js/widget/blog_featured_image/blog_featured_image.js',
		'widget/inline-edit':         './js/widget/inline-edit/inline-edit.ts',
		'widget/list':                './js/widget/list/list.js',
		'widget/manage-navigation':   './js/widget/manage_navigation/manage-navigation.ts',
		'widget/status_message':     './js/widget/status_message/status_message.js',
		'widget/blog-title-slug':    './js/widget/blog-title-slug.js',
		'widget/championcms-header':     './js/widget/championcms-header.ts'
	},
	
	mode: 'development',

	module: {
		rules: [
			{
				test:    /\.tsx?$/,
				use:     'ts-loader',
				exclude: /node_modules/,
			},
		],
	},
	
	output: {
		filename: '[name].js',
		path: path.resolve(__dirname, 'dist'),
	},
	
	plugins: [
		new DefinePlugin(
			{
				__VUE_OPTIONS_API__:   JSON.stringify(true),
				__VUE_PROD_DEVTOOLS__: JSON.stringify(true)
			}
		)
	],
	
	resolve: {
		alias: {

			'vue': 'vue/dist/vue.esm-bundler.js',
			
			// convenience for imports
			ChampionCore: path.resolve(__dirname, './js/'),
		},

		//modules: [path.resolve(__dirname, 'node_modules'), 'node_modules'],

		extensions: [ '.tsx', '.ts', '.js' ],
	},
};
