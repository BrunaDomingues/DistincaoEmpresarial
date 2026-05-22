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
            screens: {
                // Menu horizontal acima de 1040px; hambúrguer em 1040px ou menos
                'nav-desktop': '1041px',
            },
            maxWidth: {
                '85': '85%', // Adiciona a classe max-w-85 com 85% de largura
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    darkMode: 'class',

    plugins: [forms],
};
