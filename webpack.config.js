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
    .configureBabel(() => {}, {
        useBuiltIns: 'usage',
        corejs: 3
    })
    .configureCssLoader(options => {
        options.modules = true;
    })
    .enableSassLoader(options => {
        options.implementation = require('sass');
    })
    .enableTypeScriptLoader()
//    .enableForkedTypeScriptTypesChecking()
    .enableIntegrityHashes(Encore.isProduction())
    .enableReactPreset()
    .configureDefinePlugin(options => {
        const exec = require('child_process').execSync;

        options['process.env.API_BASE_URL'] = JSON.stringify(exec('php bin/console router:generate index', {encoding: 'utf8'}));
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
