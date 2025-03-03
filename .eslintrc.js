module.exports = {
	extends: [ require.resolve( '@wordpress/scripts/config/.eslintrc' ) ],
	globals: {
		jQuery: 'readonly',
	},
	rules: {
		'object-shorthand': [ 'error', 'never' ],
	},
};
