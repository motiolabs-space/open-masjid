/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./app-core/app/Views/**/*.php",
    "./public/**/*.php",
    "./index.php"
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#065F46',
          50: '#ecfdf5',
          100: '#d1fae5',
          200: '#a7f3d0',
          300: '#6ee7b7',
          400: '#34d399',
          500: '#10b981',
          600: '#059669',
          700: '#047857',
          800: '#065f46',
          900: '#064e3b',
        },
        secondary: {
          DEFAULT: '#24a871',
        }
      },
      fontFamily: {
        "display": ["Inter", "sans-serif"],
        "manrope": ["Manrope", "sans-serif"]
      },
      borderRadius: {
        "DEFAULT": "0.375rem",
        "lg": "0.625rem",
        "xl": "1rem",
        "full": "9999px"
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/container-queries'),
  ],
}
