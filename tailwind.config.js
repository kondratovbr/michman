const colors = require('tailwindcss/colors')
const plugin = require('tailwindcss/plugin')
const defaultTheme = require('tailwindcss/defaultTheme');

/*
 Custom smaller sizing scale for extending various utilities, like minWidth
 */
smallSizingScale = {
    '0.5': '0.125rem',
    '1': '0.25rem',
    '1.5': '0.375rem',
    '2': '0.5rem',
    '2.5': '0.625rem',
    '3': '0.75rem',
    '3.5': '0.875rem',
    '4': '1rem',
    '5': '1.25rem',
    '6': '1.5rem',
    '7': '1.75rem',
    '8': '2rem',
    '9': '2.25rem',
    '10': '2.5rem',
    '11': '2.75rem',
    '12': '3rem',
    '14': '3.5rem',
    '16': '4rem',
    '20': '5rem',
    '24': '6rem',
    '28': '7rem',
    '32': '8rem',
    '36': '9rem',
    '40': '10rem',
    '44': '11rem',
    '48': '12rem',
    '52': '13rem',
    '56': '14rem',
    '60': '15rem',
    '64': '16rem',
    '72': '18rem',
    '80': '20rem',
    '96': '24rem',
};

module.exports = {

    // Utilities (NOT including custom ones) will be generated with "!important",
    // so they will override components styling and defaults.
    important: true,

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
            borderWidth: {
                '1.5': '1.5px',
                '3': '3px',
            },
            boxShadow: {
                'lg-black': '0 10px 15px -3px rgba(1, 1, 1, 0.1), 0 4px 6px -2px rgba(1, 1, 1, 0.05)',
                'md-black': '0 4px 6px -1px rgba(1, 1, 1, 0.1), 0 2px 4px -1px rgba(1, 1, 1, 0.06)',
                'xl-black': '0 20px 25px -5px rgba(1, 1, 1, 0.1), 0 10px 10px -5px rgba(1, 1, 1, 0.04)',
                'md-gold-900': '0 4px 6px -1px rgba(255, 215, 0, 0.1), 0 2px 4px -1px rgba(255, 215, 0, 0.06)',
            },
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
            // Some specific additional inset (top/right/bottom/left) values
            inset: {
                '2px': '2px',
            },
            // Some specific additional margin values
            margin: {
                '2px': '2px',
            },
            // Extending min-width utilities using a custom sizing scale (see above).
            minWidth: smallSizingScale,
            // Add some specific padding values
            padding: {
                '11-sub-2': 'calc(2.75rem - 2px)',
            },
            // Reusable transition durations
            transitionDuration: {
                'quick': '100ms',
                'normal': '150ms',
            },
            // Transition properties specifically optimized for each use-case
            transitionProperty: {
                'ring-background': 'box-shadow, background-color',
                'border-background': 'border-color, background-color',
                'opacity-transform': 'opacity, transform',
                'border-ring': 'border-color, box-shadow',
                'border': 'border-color',
                'text': 'color',
            },
        },

    },

    variants: {
        extend: {

            backgroundColor: ['hover', 'active', 'disabled', 'checked'],
            backgroundOpacity: ['active', 'group-hover', 'group-active', 'checked'],
            borderWidth: ['hover', 'active'],
            borderColor: ['hover', 'active', 'focus'],
            borderOpacity: ['group-hover', 'group-active', 'group-focus'],
            cursor: ['disabled'],
            display: [],
            margin: ['first', 'last'],
            opacity: ['disabled', 'group-hover', 'group-active'],
            padding: ['first', 'last'],
            ringColor: [],
            ringOpacity: [],
            ringWidth: [],
            rotate: [],
            scale: ['group-hover', 'group-focus'],
            textColor: ['hover', 'active', 'group-hover', 'group-focus', 'disabled', 'sibling-focus'],
            transform: [],
            translate: [],
            visibility: [],
            zIndex: [],

        },
    },

    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
        require('@tailwindcss/aspect-ratio'),
        require('@tailwindcss/line-clamp'),
        require('tailwindcss-debug-screens'),

        // "group-active" variant for various interactive elements
        plugin(function({ addVariant, e }) {
            addVariant('group-active', ({ modifySelectors, separator }) => {
                modifySelectors(({ className }) => {
                    return `.group:active .${e(`group-active${separator}${className}`)}`;
                });
            });
        }),

        // "sibling-focus" variant for various interactive elements (mostly for transitioning icons on inputs)
        plugin(function({ addVariant, e }) {
            addVariant('sibling-focus', ({ modifySelectors, separator }) => {
                modifySelectors(({ className }) => {
                    return `.sibling:focus ~ .${e(`sibling-focus${separator}${className}`)}`;
                });
            });
        }),

        // "group-selected" variant for styling various toggle-switches and checkboxes
        // TODO: Does it even work?
        plugin(function({ addVariant, e }) {
            addVariant('selected', ({ modifySelectors, separator }) => {
                modifySelectors(({ className }) => {
                    return `.${e(`active${separator}${className}`)}.selected`;
                });
            });
            addVariant('group-selected', ({ modifySelectors, separator }) => {
                modifySelectors(({ className }) => {
                    return `.group.selected .${e(`group-selected${separator}${className}`)}`;
                });
            });
        }),
    ],

}
