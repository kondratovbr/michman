const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        require('postcss-import'),
        require('postcss-nested'),
        require('tailwindcss'),
        require('autoprefixer'),
    ])
    .options({
        processCssUrls: false,
    })
    .version();

if (! mix.inProduction()) {
    mix.sourceMaps();
}
