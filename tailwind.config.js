import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    darkMode: 'class',

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Warna DB Logistics
                dbl: {
                    green: '#10B981',       // Hijau Utama / Accent
                    'green-dark': '#059669', // Hijau Hover/Active
                    'green-light': '#D1FAE5',// Badge / Background Halus
                    dark: '#111827',         // Dark Charcoal (Sidebar & Header)
                    darker: '#030712',       // Surface Elemen Tergelap
                    gray: '#F9FAFB',         // Main Body Background
                    card: '#FFFFFF',         // Card Background
                },
                // Warna brand website Amanah Nusantara Logistik (ANL) — disamakan dengan logo (hijau, hitam, biru dongker)
                anl: {
                    navy: '#0F2B48',          // Biru Dongker (Hero/Section Gelap)
                    'navy-dark': '#070A12',   // Hitam Kebiruan (Footer/Navbar)
                    'navy-light': '#16345C',  // Navy Hover
                    blue: '#399310',          // Hijau Logo (Tombol Utama / Link Aktif)
                    'blue-dark': '#2E6E0F',   // Hijau Hover
                    'blue-light': '#EAF5DA',  // Tint Hijau Halus
                    amber: '#6CCF2E',         // Hijau Terang (Badge / Aksen di atas navy)
                    'amber-dark': '#4DA81F',  // Aksen Hover
                    'amber-light': '#EAF5DA', // Background Halus Aksen
                }
            },
            boxShadow: {
                card: '0 1px 2px 0 rgb(16 185 129 / 0.04), 0 1px 3px 0 rgb(17 24 39 / 0.06), 0 1px 2px -1px rgb(17 24 39 / 0.06)',
                lift: '0 12px 24px -8px rgb(17 24 39 / 0.15), 0 4px 8px -4px rgb(17 24 39 / 0.08)',
                glow: '0 0 0 1px rgb(16 185 129 / 0.15), 0 8px 24px -8px rgb(16 185 129 / 0.35)',
                'glow-lg': '0 0 0 3px rgb(16 185 129 / 0.2), 0 12px 32px -12px rgb(16 185 129 / 0.5)',
            },
            keyframes: {
                'fade-up': {
                    '0%': { opacity: '0', transform: 'translateY(16px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                'fade-in': {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                'scale-in': {
                    '0%': { opacity: '0', transform: 'scale(0.96)' },
                    '100%': { opacity: '1', transform: 'scale(1)' },
                },
                'pulse-soft': {
                    '0%, 100%': { opacity: '1' },
                    '50%': { opacity: '0.55' },
                },
                'shimmer': {
                    '0%': { backgroundPosition: '-200% 0' },
                    '100%': { backgroundPosition: '200% 0' },
                },
            },
            animation: {
                'fade-up': 'fade-up 0.6s cubic-bezier(0.22, 1, 0.36, 1) both',
                'fade-in': 'fade-in 0.5s ease both',
                'scale-in': 'scale-in 0.4s cubic-bezier(0.22, 1, 0.36, 1) both',
                'pulse-soft': 'pulse-soft 2.4s ease-in-out infinite',
                'shimmer': 'shimmer 1.8s linear infinite',
            },
        },
    },

    plugins: [forms],
};
