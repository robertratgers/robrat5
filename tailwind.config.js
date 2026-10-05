/**
 * Tailwind configuration converted from assets/gemini-code-1791214848201.js
 */
module.exports = {
  darkMode: ['class'],
  content: [
    './**/*.php',
    './assets/*.html',
    './assets/*.js',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['var(--font-sans)'],
        mono: ['var(--font-mono)'],
      },
      borderRadius: {
        lg: 'var(--radius)',
        md: 'calc(var(--radius) - 2px)',
        sm: 'calc(var(--radius) - 4px)',
      },
    },
  },
  plugins: [],
};
