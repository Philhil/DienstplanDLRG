let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.copy('node_modules/fullcalendar/main.css', 'public/plugins/fullcalendar/css/main.css');
mix.copy('node_modules/fullcalendar/main.js', 'public/plugins/fullcalendar/js/main.js');
mix.copy('node_modules/fullcalendar/main.min.js', 'public/plugins/fullcalendar/js/main.min.js');
mix.copy('node_modules/fullcalendar/locales/de.js', 'public/plugins/fullcalendar/locales/de.js');
mix.copy('node_modules/fullcalendar/locales-all.js', 'public/plugins/fullcalendar/locales-all.js');
mix.copy('node_modules/fullcalendar/locales-all.min.js', 'public/plugins/fullcalendar/locales-all.min.js');

//mix.js('resources/assets/js/app.js', 'public/js').sass('resources/assets/sass/app.scss', 'public/css');
