/** @type {import('tailwindcss').Config} */
module.exports = {
  plugins: [
    {
      tailwindcss: {},
      autoprefixer: {},
      'postcss-import': {}
    },
  ],
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
  mode: 'jit',
  module: {
    rules: [
      {
        test: /\.scss$/,
        use: [
          'style-loader',
          'css-loader',
          'sass-loader',
        ],
      },
    ],
  },
}