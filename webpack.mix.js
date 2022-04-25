let mix = require('laravel-mix')
let path = require('path')
require('./mix')

mix
  .setPublicPath('dist')
  .js('resources/js/field.js', 'js')
  .vue({ version: 3 })
  .webpackConfig(require('./webpack.config'))
  .sass('resources/sass/field.scss', 'css')
  .nova('dmitrybubyakin/nova-medialibrary-field')
// .sourceMaps()
// .version()

mix.alias({
  'laravel-nova': path.join(__dirname, 'vendor/laravel/nova/resources/js/mixins/packages.js'),
})
