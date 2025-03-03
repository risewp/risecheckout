module.exports = {
	extends: [ require.resolve( '@wordpress/scripts/config/.eslintrc' ) ],
	globals: {
		jQuery: 'readonly',
	},
	rules: {
		'object-shorthand': [ 'error', 'never' ],
		'@wordpress/no-global-active-element': 'off',
	},
};
