/**
 * External Dependencies
 */
const path = require('path');

/**
 * WordPress Dependencies
 */
const defaultConfig = require('@wordpress/scripts/config/webpack.config.js');

module.exports = {
    ...defaultConfig,
    ...{
        entry: {
            "script": path.resolve(process.cwd(), 'src', 'js/announcement-bar.js'),
            "style": path.resolve(process.cwd(), 'src', 'scss/announcement-bar.scss'),
            "admin-script": path.resolve(process.cwd(), 'src', 'js/announcement-bar-admin.js'),
            "admin-style": path.resolve(process.cwd(), 'src', 'scss/announcement-bar-admin.scss'),
        },
    }
}
