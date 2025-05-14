import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

export default defineConfig({
  plugins: [react()],
  build: {
    outDir: '../extensions/swatches/assets',
    emptyOutDir: false,
    rollupOptions: {
      input: './src/main.jsx',
      output: {
        entryFileNames: 'react-app.js',
        assetFileNames: 'index.css',
      },
    },
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
    },
  },
});
