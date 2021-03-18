const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')

    // TODO: Are comments dropped from the resulting CSS on prod?

    .postCss('resources/css/app.pcss', 'public/css', [
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
