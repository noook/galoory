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
      boxShadow: {
        popup: '0px 12px 32px rgba(26, 26, 26, 0.25)',
      },
      height: {
        9: '2.25rem',
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
          200: '#F3EBFF',
          default: '#2f365f',
        },
        lightgray: {
          300: '#F8FAFC',
        },
        gray: {
          400: '#656565',
          default: '#a1a1a1',
        },
      },
    },
  },
  variants: {},
  plugins: [],
};
