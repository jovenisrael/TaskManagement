import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import { fileURLToPath, URL } from 'node:url';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const appUrl = env.APP_URL || 'http://localhost:8080';

    return {
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
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
            tailwindcss(),
        ],
        resolve: {
            alias: {
                '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
            },
        },
        server: {
            host: '0.0.0.0',
            port: 5173,
            strictPort: true,
            origin: 'http://localhost:5173',
            cors: {
                origin: allowedOrigins(appUrl),
            },
            hmr: {
                host: 'localhost',
            },
            watch: {
                ignored: ['**/storage/framework/views/**'],
                usePolling: true,
            },
        },
    };
});

function allowedOrigins(appUrl) {
    const origins = new Set(['http://localhost:5173', 'http://127.0.0.1:5173']);

    try {
        const url = new URL(appUrl);
        origins.add(url.origin);

        const port = url.port || (url.protocol === 'https:' ? '443' : '80');

        if (url.hostname === 'localhost') {
            origins.add(`${url.protocol}//127.0.0.1:${port}`);
        }

        if (url.hostname === '127.0.0.1') {
            origins.add(`${url.protocol}//localhost:${port}`);
        }
    } catch {
        origins.add('http://localhost:8080');
        origins.add('http://127.0.0.1:8080');
    }

    return [...origins];
}
