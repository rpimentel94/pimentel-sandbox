/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    'templates/**/*.twig',
    'tailwind.theme',
    'components/styles.scss',
    'components/**/*.twig',
    'components/**/*.scss',
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}