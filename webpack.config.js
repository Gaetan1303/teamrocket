const Encore = require('@symfony/webpack-encore');
const path = require('path');

Encore
  // chemin de sortie
  .setOutputPath('public/build/')
  .setPublicPath('/build')

  // entrée principale
  .addEntry('app', './assets/app.jsx')

  // activer React
  .enableReactPreset()

  // Stimulus bridge
  .enableStimulusBridge('./assets/controllers.json')

  // active PostCSS si besoin
  .enablePostCssLoader()

  // nettoyage du dossier build à chaque compilation
  .cleanupOutputBeforeBuild()

  // versioning des fichiers en prod
  .enableVersioning(Encore.isProduction())

  // enable source maps en dev
  .enableSourceMaps(!Encore.isProduction())

  // ⚠️ Correction de l’erreur RuntimeChunk
  .enableSingleRuntimeChunk()

  // utiliser notre Babel externe
  .configureBabel(null)
;

module.exports = Encore.getWebpackConfig();
