const Encore = require('@symfony/webpack-encore');

const tailwindcss = require('tailwindcss');
const autoprefixer = require('autoprefixer');
const path = require('path');


const purgecss = require('@fullhuman/postcss-purgecss')({
    content: [
        './templates/**/*.twig',
        './assets/js/**/*.vue',
        './assets/js/**/*.js',
    ],
    defaultExtractor: content => content.match(/[\w-/:]+(?<!:)/g) || [],
});

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './assets/app.js')
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .enableSassLoader(sassOptions => {
    }, {
        resolveUrlLoader: false
    })
    .enablePostCssLoader(options => {
        options.postcssOptions = {
            // directory where the postcss.config.js file is stored
            config: path.resolve(__dirname, 'postcss.config.js'),
            pluginsplugins: [
                tailwindcss,
                autoprefixer,
            ]
        };
    })
    .enableVueLoader()
;

module.exports = Encore.getWebpackConfig();