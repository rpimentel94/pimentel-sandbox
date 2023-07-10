/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    'templates/**/*.twig',
    'tailwind.theme',
    'components/**/*.twig'
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}