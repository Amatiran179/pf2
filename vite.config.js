import { defineConfig } from 'vite';
import legacy from '@vitejs/plugin-legacy';
import path from 'path';

const themeRoot = __dirname;

export default defineConfig({
  plugins: [legacy()],
  server: {
    port: 5173,
    strictPort: true,
    hmr: {
      host: 'localhost',
    },
  },
  build: {
    outDir: path.resolve(themeRoot, 'assets'),
    emptyOutDir: false,
    rollupOptions: {
      input: {
        front: path.resolve(themeRoot, 'assets/js/front.js'),
        admin: path.resolve(themeRoot, 'assets/js/admin.js'),
      },
      output: {
        entryFileNames: (chunk) => `js/${chunk.name}-bundle.js`,
        assetFileNames: (assetInfo) => {
          if (assetInfo.name && assetInfo.name.endsWith('.css')) {
            return 'css/[name]-bundle[extname]';
          }

          return 'assets/[name][extname]';
        },
      },
    },
  },
});

