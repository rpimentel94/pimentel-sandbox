/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    'templates/**/*.{html,js,twig}',
    'tailwind.theme',
    'components/styles.scss',
    'components/**/*.{html,js,twig, scss}',
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}