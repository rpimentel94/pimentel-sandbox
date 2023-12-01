/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    'templates/**/*.{html,js,twig}',
    'tailwind.theme',
    'components/*.scss',
    'components/**/*.{html,js,twig, scss}',
    'src/Plugin/Preprocess/**/*.php',
    'assets/src/js/*.js',
    'node_modules/flowbite/**/*.js',
    '../../../modules/custom/htlf_alert/Plugin/Block/AlertsBlock.php'
  ],
  theme: {
    extend: {
      colors: {
        htlfDarkGray: '#757575',
        htlfGray: '#a6a6a6',
        htlfLightGray: '#F2F2F2',
        htlfLighterGray: '#f5f5f5',
        htlfWhite: '#FFFFFF',
        htlfDarkBlue: '#1C1F2A',
        htlfBlue: '#202945',
        htlfLightBlue: '#1C3685',
        htlfBody: '#e7e7e7',
        transparent: 'transparent',
        htlfBlack: '#333333',
        htlfMessage: '#E5F2F1',
        htlfMessageText: '#2C5C57',
        htlfNotice: '#F7DF6E',
        htlfNoticeText: '#857840',
        htlfWarning: '#E68992',
        htlfWarningText: '#5C2C31',
      },
    },
  },
  plugins: [
    require('flowbite/plugin')
  ]
}