import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js'], // если нужен CSS, добавьте сюда 'resources/css/app.css'
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    server: {
        // Разрешаем Vite отвечать на запросы с любого хоста
        host: '0.0.0.0', 
        // Принудительно заставляем клиентский скрипт Vite (в браузере) 
        // стучаться за обновлениями кода строго на localhost
        hmr: {
            host: 'localhost',
        },
    },
});
