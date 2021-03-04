const colors = require('tailwindcss/colors')
const plugin = require('tailwindcss/plugin')
const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {

    purge: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/**/*.md',
        './resources/**/*.js',
        './resource/**/*.php',
    ],

    theme: {
        colors: {
            transparent: 'transparent',
            current: 'currentColor',
            black: colors.black,
            white: colors.white,
            navy: {
                light: '#151EA6',
                DEFAULT: '#1F266B',
                dark: '#00017A',
                darkest: '#03005B',
            },
            gold: {
                light: '#FED701',
                DEFAULT: '#FEC203',
                dark: '#FCB305',
                darkest: '#F08E00',
            },
        },

        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
            //
        },
    },

    variants: {
        extend: {
            opacity: ['disabled'],
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
