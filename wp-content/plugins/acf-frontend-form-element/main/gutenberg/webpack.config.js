const defaultConfig = require("@wordpress/scripts/config/webpack.config");
const path = require('path');

module.exports = {
	...defaultConfig,
	entry: {
		'block': '/src/block.js'
	},
	output: {
		path: path.resolve(__dirname, 'assets/js'),
		filename: '[name].js'
	}
}