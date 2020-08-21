module.exports = {
  purge: [
    './src/**/*.vue',
    './src/**/*.js',
  ],
  theme: {
    extend: {
      width: {
        82: '20rem',
      },
      colors: {
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
