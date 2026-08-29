/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/views/**/*.blade.php",
    "./app/**/*.php",
    "./*.php",
    "./RulesSrc/**/*.php"
  ],
  theme: {
    extend: {
      colors: {
        primary: '#4f46e5',
        secondary: '#d97706',
      }
    },
  },
  plugins: [],
}
