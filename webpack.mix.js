const mix = require('laravel-mix');

mix
    .webpackConfig({ stats: { children: true }})
    .js('resources/js/app.js', 'public/js')

    // TODO: IMPORTANT! Are comments dropped from the resulting CSS on prod?
    //       Check out: https://www.npmjs.com/package/postcss-discard-comments

    .postCss('resources/css/app.pcss', 'public/css', [
        require('postcss-import'),
        //require('postcss-nested'),
        require('tailwindcss/nesting'),
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
