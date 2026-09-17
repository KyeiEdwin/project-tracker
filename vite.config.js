import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'

export default defineConfig({
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
  ],
  resolve: {
    alias: [
      { find: '@/assets/images', replacement: resolve(__dirname, 'resources/images') },
      { find: '@/assets/css', replacement: resolve(__dirname, 'resources/css') },
      { find: '@/components', replacement: resolve(__dirname, 'resources/js/Components') },
      { find: '@/Components', replacement: resolve(__dirname, 'resources/js/Components') },
      { find: '@', replacement: resolve(__dirname, 'resources/js') },
    ],
  },
  assetsInclude: ['**/*.woff', '**/*.woff2', '**/*.ttf', '**/*.eot', '**/*.svg'],
})
