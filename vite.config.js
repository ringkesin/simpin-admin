import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from 'tailwindcss'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'node_modules/preline/dist/preline.js'],
            refresh: true,
        }),
    ],
    // css: {
    //     postcss: {
    //       plugins: [tailwindcss()],
    //     },
    // },
});
