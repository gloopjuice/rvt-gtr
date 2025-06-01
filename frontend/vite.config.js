import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vitejs.dev/config/
export default defineConfig({
    build: {
      sourcemap: true
    },
    server: {
      port: 5173,
      host: '0.0.0.0',
      hmr: {
          host: '64.227.105.115',
          port: 5173
      },
      watch: {
          usePolling: true
      }
    },
  plugins: [
    vue(),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  }
})
