const colors = require('tailwindcss/colors')
const plugin = require('tailwindcss/plugin')

module.exports = {

    purge: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './resources/**/*.md',
        './resources/**/*.php',
    ],

    darkMode: false, // or 'media' or 'class'

    theme: {
        extend: {
            //
        },
    },

    variants: {
        extend: {
            //
        },
    },

    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
        require('@tailwindcss/aspect-ratio'),
        require('@tailwindcss/line-clamp'),
        require('tailwindcss-debug-screens'),
    ],

}
