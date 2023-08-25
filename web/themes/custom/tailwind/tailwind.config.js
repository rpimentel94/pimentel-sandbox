/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    'templates/**/*.{html,js,twig}',
    'tailwind.theme',
    'components/styles.scss',
    'components/**/*.{html,js,twig, scss}',
  ],
  theme: {
    extend: {
      colors: {
        htlfGray: '#757575',
        htlfLightGray: '#F2F2F2',
        htlWhite: '#FFFFFF',
        htlfDarkBlue: '#1C1F2A',
        htlfBlue: '#202945'
      },
    },
  },
  plugins: [],
}