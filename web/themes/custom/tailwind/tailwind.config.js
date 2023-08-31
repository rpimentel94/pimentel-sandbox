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
        htlfDarkGray: '#757575',
        htlfGray: '#a6a6a6',
        htlfLightGray: '#F2F2F2',
        htlWhite: '#FFFFFF',
        htlfDarkBlue: '#1C1F2A',
        htlfBlue: '#202945',
        transparent: 'transparent'
      },
    },
  },
  plugins: [],
}