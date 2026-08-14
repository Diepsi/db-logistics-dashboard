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
                // Warna brand website Amanah Nusantara Logistik (ANL)
                anl: {
                    navy: '#0F2B48',          // Deep Navy (Hero/Dark Section)
                    'navy-dark': '#0B132B',   // Midnight Charcoal (Footer)
                    'navy-light': '#16345C',  // Navy Hover
                    blue: '#2563EB',          // Royal Blue (Tombol Utama / Link Aktif)
                    'blue-dark': '#1E40AF',   // Blue Hover
                    'blue-light': '#DBEAFE',  // Tint Background Halus
                    amber: '#F59E0B',         // Amber Accent (Badge / Highlight)
                    'amber-dark': '#D97706',  // Amber Hover
                    'amber-light': '#FEF3C7', // Amber Background Halus
                }
            }
        },
    },

    plugins: [forms],
};
