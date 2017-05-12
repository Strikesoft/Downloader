const path = require('path');
const webpack = require('webpack');
const UglifyJSPlugin = require('uglifyjs-webpack-plugin');

module.exports = {
    entry: [
        './src/js/Utils.js',
        './src/js/Downloader.js',
        './src/js/ModalSecure.js'
    ],
    output: {
        filename: 'downloader.min.js',
        path: path.resolve(__dirname, 'web/js')
    },
    plugins: [
        new webpack.ProvidePlugin({
            $: 'jquery',
            jQuery: 'jquery',
            'window.jQuery': 'jquery',
            Tether: 'tether'
        }),
        new UglifyJSPlugin({
            mangle: {
                // Skip mangling these
                except: ['$', 'Tether']
            }
        })
    ],
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /(node_modules|bower_components)/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['env']
                    }
                }
            }
        ]
    }
};
