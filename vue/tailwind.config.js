/* eslint-disable @typescript-eslint/no-var-requires */
const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
  purge: [
    './src/**/*.vue',
    './src/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        ...defaultTheme.colors,
        placeholder: {
          default: 'rgba(51, 51, 51, .75)',
        },
        'gray-border': '#e1e9ed',
        indigo: {
          default: '#2f365f',
        },
        gray: {
          default: '#a1a1a1',
        },
      },
    },
  },
  variants: {},
  plugins: [],
};
