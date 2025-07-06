import {
    defineConfig
} from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    base: process.env.VITE_URL || '/',
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        https: false,
        host: '127.0.0.1', // Use localhost for local development
        port: 5173,
    },
});
