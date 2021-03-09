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
                //original: '#000080',
                //light: '#151EA6',
                //DEFAULT: '#1F266B',
                //dark: '#00017A',
                //darkest: '#03005B',

                DEFAULT: '#003d7a',
                '900': '#0066cc',
                '800': '#005cb8',
                '700': '#0052a3',
                '600': '#00478f',
                '500': '#003d7a',
                '400': '#003366',
                '300': '#002952',
                '200': '#001f3d',
                '100': '#001429',
                '50': '#000a14',
            },
            gold: {
                //light: '#FED701',
                //DEFAULT: '#FEC203',
                //dark: '#FCB305',
                //darkest: '#F08E00',

                DEFAULT: '#998100',
                '900': '#ffd700',
                '800': '#e6c200',
                '700': '#ccac00',
                '600': '#b39700',
                '500': '#998100',
                '400': '#806c00',
                '300': '#665600',
                '200': '#4d4100',
                '100': '#332b00',
            },
        },

        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                'lg-black': '0 10px 15px -3px rgba(1, 1, 1, 0.1), 0 4px 6px -2px rgba(1, 1, 1, 0.05)',
                'md-black': '0 4px 6px -1px rgba(1, 1, 1, 0.1), 0 2px 4px -1px rgba(1, 1, 1, 0.06)',
                'md-gold-900': '0 4px 6px -1px rgba(255, 215, 0, 0.1), 0 2px 4px -1px rgba(255, 215, 0, 0.06)',
            },
        },
    },

    variants: {
        extend: {

            backgroundColor: ['active'],
            borderWidth: ['hover', 'active'],
            borderColor: ['hover', 'active', 'focus'],
            display: ['group-hover'],
            margin: ['first', 'last'],
            opacity: ['disabled'],
            padding: ['first', 'last'],
            ringColor: ['hover', 'active'],
            ringOpacity: ['hover', 'active'],
            ringWidth: ['hover', 'active'],
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
