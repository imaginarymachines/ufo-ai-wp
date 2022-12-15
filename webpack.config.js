const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );

module.exports = {
    ...defaultConfig,
    entry: {
        ...defaultConfig.entry,
		index: path.resolve( process.cwd(), 'src', 'index.js' ),
        admin: path.resolve( process.cwd(), 'admin', 'index.js' ),
    }
};
