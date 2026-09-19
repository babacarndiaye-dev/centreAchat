import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.jsx',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                display: ['"Fraunces"', 'serif'],
            },
            colors: {
                terroir: {
                    // Editable in Admin > Paramètres > Design: these three resolve from CSS
                    // custom properties injected server-side from Setting values, so admins
                    // can change the brand palette without a rebuild or code change.
                    green: 'rgb(var(--terroir-green) / <alpha-value>)',
                    terracotta: 'rgb(var(--terroir-terracotta) / <alpha-value>)',
                    gold: 'rgb(var(--terroir-gold) / <alpha-value>)',
                    // Structural/neutral tones, not exposed as admin settings.
                    'green-light': '#4FAE75',
                    cream: '#F7F8F5',
                    brown: '#6B7280',
                    dark: '#1F2328',
                },
            },
            boxShadow: {
                soft: '0 10px 40px -12px rgba(22, 32, 27, 0.25)',
            },
            borderRadius: {
                xl2: '1.25rem',
            },
        },
    },
    plugins: [],
};
