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
                sans: ['Montserrat', ...defaultTheme.fontFamily.sans],
                display: ['"Bebas Neue"', 'Oswald', 'Impact', 'sans-serif'],
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
                    // DIABA HOTEL Produits du Sénégal (D.H.P.S) charter: vert #009C4A, vert clair #1DBF63,
                    // noir #101818, blanc cassé #F0F0E8, gris #6B726D.
                    'green-light': '#1DBF63',
                    cream: '#F0F0E8',
                    brown: '#6B726D',
                    dark: '#101818',
                },
            },
            boxShadow: {
                soft: '0 10px 40px -12px rgba(16, 24, 24, 0.25)',
            },
            borderRadius: {
                xl2: '0.125rem',
            },
        },
    },
    plugins: [],
};
