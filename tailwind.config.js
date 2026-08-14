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
                    navy: '#0B1F3A',         // Navy Utama (Header/Hero)
                    'navy-dark': '#081527',  // Navy Tergelap (Footer)
                    'navy-light': '#16345C', // Navy Hover
                    gold: '#D4AF37',         // Gold Accent / CTA
                    'gold-dark': '#B8942C',  // Gold Hover
                    'gold-light': '#F7EFD4', // Gold Background Halus
                }
            }
        },
    },

    plugins: [forms],
};
