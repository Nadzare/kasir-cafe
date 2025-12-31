import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                nature: {
                    50: '#f2fbf5',
                    100: '#e0f7e9',
                    200: '#c2efd4',
                    300: '#92e0b0',
                    400: '#5bc886',
                    500: '#34af65',
                    600: '#4f8f5e', // Accent Light
                    700: '#1e7a44',
                    800: '#276749',
                    900: '#1a4d2e', // Primary Dark
                    950: '#0d2919',
                },
            },
        },
    },

    plugins: [forms],
};

