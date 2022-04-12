const path = require('path');

module.exports = {
    resolve: {
        alias: {
            'laravel-nova': path.join(__dirname, 'vendor/laravel/nova/resources/js/mixins/packages.js'),
        },
    },
};
