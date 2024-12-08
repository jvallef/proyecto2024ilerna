const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            fontSize: {
                'xs': '1rem',      // 16px
                'sm': '1.125rem',  // 18px
                'base': '1.5rem',  // 24px
                'lg': '1.75rem',   // 28px
                'xl': '2rem',      // 32px
                '2xl': '2.25rem',  // 36px
                '3xl': '2.5rem',   // 40px
                '4xl': '3rem',     // 48px
                '5xl': '3.5rem',   // 56px
                '6xl': '4rem',     // 64px
            },
            colors: {
                primary: '#fa5f30',    // Naranja cálido
                'primary-dark': '#e54d20', // Naranja más oscuro
                'primary-alt': '#ff7346', // Naranja alternativo para el logo
                secondary: '#2a9d8f',  // Verde azulado
                success: '#34d399',    // Verde menta
                info: '#60a5fa',       // Azul claro
                warning: '#fbbf24',    // Amarillo cálido
                danger: '#ef4444',     // Rojo
                gray: {
                    50: '#f9fafb',
                    100: '#f3f4f6',
                    200: '#e5e7eb',
                    300: '#d1d5db',
                    400: '#9ca3af',
                    500: '#6b7280',
                    600: '#4b5563',
                    700: '#374151',
                    800: '#1f2937',
                    900: '#111827',
                },
            },
            borderRadius: {
                'sm': '0.4rem',
                DEFAULT: '0.5rem',
                'lg': '0.5rem',
            },
            boxShadow: {
                DEFAULT: '0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06)',
            },
        },
    },

    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
};
