import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        // 'node_modules/preline/dist/*.js',
        './resources/views/**/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
    ],

    safelist: [
        'bg-yellow-500', 'bg-yellow-600',
        'bg-green-500', 'bg-green-600',
        'bg-red-500', 'bg-red-600'
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [
        forms,
        // typography
        require('preline/plugin')
    ],
};
