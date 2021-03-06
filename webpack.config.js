const Encore = require('@symfony/webpack-encore');
const WebpackShellPlugin = require('webpack-shell-plugin');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './assets/js/app.tsx')
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-proposal-optional-chaining');
    }, {
        useBuiltIns: 'usage',
        corejs: 3,
    })
    .configureCssLoader(options => {
        options.modules = true;
    })
    .enableSassLoader(options => {
        options.implementation = require('sass');
    })
    .enableTypeScriptLoader()
    // .enableForkedTypeScriptTypesChecking()
    .enableIntegrityHashes(Encore.isProduction())
    .enableReactPreset()
    .addRule({
        test: /\.(txt|pem)/i,
        use: [
            {
                loader: 'raw-loader',
                options: {
                    esModule: false,
                },
            },
        ],
    })
;

const config = Encore.getWebpackConfig();

config.plugins.push(
    new WebpackShellPlugin({
        onBuildStart: [
            'php bin/console heimdall:export-translations en -o ./assets/js/i18n/en.ts'
        ]
    })
);

module.exports = config;
