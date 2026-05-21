import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/chat.css',
                'resources/js/chat.js',
                'resources/css/welcome.css',
                'resources/css/auth/login.css',
                'resources/css/auth/register.css',
                'resources/css/auth/forgot-password.css'
            ],
            refresh: true,
        }),
    ],
});
