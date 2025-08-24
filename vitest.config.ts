import { defineConfig } from 'vitest/config'
import Vue from '@vitejs/plugin-vue'
import VueJsx from '@vitejs/plugin-vue-jsx'
import path from 'path'

export default defineConfig({
    plugins: [Vue(), VueJsx()],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './assets'),
        },
    },
    optimizeDeps: {
        disabled: true,
    },
    test: {
        include: ['assets/**/*.{test,spec}.{js,mjs,cjs,ts,mts,cts,jsx,tsx}'],
        clearMocks: true,
        environment: 'jsdom',
       // setupFiles: ['./vitest.setup.ts'],
        transformMode: {
            web: [/\.[jt]sx$/],
        },
    },
})