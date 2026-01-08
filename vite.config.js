import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss', // Keep your SCSS
                'resources/js/app.js'
            ],
            refresh: true,
        }),
    ],
    build: {
        // Better chunking strategy for production
        rollupOptions: {
            output: {
                manualChunks(id) {
                    // Split vendor code into separate chunk
                    if (id.includes('node_modules')) {
                        if (id.includes('alpinejs')) {
                            return 'alpine';
                        }
                        if (id.includes('sweetalert2')) {
                            return 'sweetalert';
                        }
                        if (id.includes('livewire')) {
                            return 'livewire';
                        }
                        return 'vendor';
                    }
                }
            }
        },
        // Optimize chunk size
        chunkSizeWarningLimit: 1000,
        // Better minification
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true, // Remove console.logs in production
                drop_debugger: true
            }
        },
        // Source maps for debugging (disable in production)
        sourcemap: false
    },
    // Optimize CSS
    css: {
        devSourcemap: false
    },
    // Better caching
    server: {
        hmr: {
            overlay: true
        }
    }
});