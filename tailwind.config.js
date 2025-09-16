import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        borderColor: {
            DEFAULT: '#e5e7eb', // Tailwind gray.200
        },
        extend: {
            fontFamily: {
                sans: ['Oswald', ...defaultTheme.fontFamily.sans],
            },

            colors: {
                transparent: "transparent",
            },
        },
    },

    plugins: [forms],
};
