const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */
 const webpack = require('webpack')
 let ImageminPlugin = require( 'imagemin-webpack-plugin' ).default;
 mix.webpackConfig ({
     plugins: [
         new webpack.DefinePlugin({
            __VUE_OPTIONS_API__: false,
            __VUE_PROD_DEVTOOLS__: true
         }),
         new ImageminPlugin( {
                        disable: process.env.NODE_ENV !== 'production', // Disable during development
                        pngquant: {
                            quality: '95-100',
                        },
                        test: /\.(jpe?g|png|gif|svg)$/i,
                    } ),
     ],
 })

mix.copy('resources/js/images','public/images');

mix.js('resources/js/app.js', 'public/js')
.sass('resources/css/app.scss', 'public/css').vue()
.browserSync({
    proxy:'lcdt.local',
    files: [
        'resources/views/**/*.php',
        'public/js/**/*.js',
        'public/css/**/*.css'
    ]
}).version();

