import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import statamic from '@statamic/cms/vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        statamic(),
        laravel({
            input: [
                'resources/js/justbetter-statamic-base.js',
                'resources/css/justbetter-statamic-base.css',
            ],
            publicDirectory: 'resources/dist',
        }),
        tailwindcss(),
    ],
});
