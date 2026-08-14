import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

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
            }
        },
    },

    plugins: [forms],
};
