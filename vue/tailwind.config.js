module.exports = {
  purge: [
    './src/**/*.vue',
    './src/**/*.js',
  ],
  experimental: {
    applyComplexClasses: true,
  },
  theme: {
    extend: {
      height: {
        14: '3.5rem',
      },
      width: {
        82: '20rem',
      },
      colors: {
        black: {
          default: '#333333',
        },
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
