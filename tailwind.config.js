/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
    ],

    theme: {
        extend: {
            colors: {
                // Marten Do corporate brand palette. Use these hex values only.
                brand: {
                    'red-1': '#EE3A25', // Primary Red 1: solid fills, indicators, focus on light surfaces
                    'red-2': '#EB4C40', // Primary Red 2: icons and accents on dark surfaces, hover fills
                },
            },
        },
    },

    plugins: [],
};
