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
            // Special values
            transparent: 'transparent',
            current: 'currentColor',

            // Default colors
            gray: colors.coolGray,
            red: colors.red,
            yellow: colors.amber,
            green: colors.emerald,
            blue: colors.blue,
            indigo: colors.indigo,
            purple: colors.violet,
            pink: colors.pink,

            // Custom colors
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

            backgroundColor: ['active'],
            borderWidth: ['active'],
            borderColor: ['active', 'focus'],
            display: ['group-hover'],
            margin: ['first', 'last'],
            opacity: ['disabled'],
            padding: ['first', 'last'],
            rotate: ['group-hover'],
            scale: ['group-hover'],
            textColor: ['active'],
            transform: ['group-hover'],
            translate: ['group-hover'],
            visibility: ['group-hover'],
            zIndex: ['hover', 'active'],

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
