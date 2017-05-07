const path = require('path');
const webpack = require('webpack');

module.exports = {
    entry: [
       './src/js/index.js'
    ],
    output: {
        filename: 'downloader.js',
        path: path.resolve(__dirname, 'web/js')
    },
    plugins : [
        new webpack.ProvidePlugin({
            $: 'jquery',
            jQuery: 'jquery',
            'window.jQuery': 'jquery',
            Tether: 'tether'
        })
    ]
};