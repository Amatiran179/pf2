import { defineConfig } from 'vite'
import legacy from '@vitejs/plugin-legacy'
import path from 'path'

export default defineConfig({
  root: '.',
  plugins: [legacy()],
  build: {
    outDir: 'dist',
    emptyOutDir: true,
    rollupOptions: {
      input: {
        front: path.resolve(__dirname, 'assets/js/front.js'),
        admin: path.resolve(__dirname, 'assets/js/admin.js'),
      },
      output: {
        entryFileNames: (chunk) => `assets/js/${chunk.name}.js`,
        assetFileNames: (asset) => {
          if (asset.name && asset.name.endsWith('.css')) return 'assets/css/[name].[ext]'
          return 'assets/[name].[ext]'
        }
      }
    }
  },
  server: {
    port: 5173,
    strictPort: true,
    hmr: true
  }
})
