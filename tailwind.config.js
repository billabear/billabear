/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      keyframes: {
        shake: {
          '0%, 50%, 100%': { transform: 'translate(-3px, 0px)' },
          '25%, 75%': { transform: 'translate(3px, 0px)' },
        }
      },
      animation: {
        shake: 'shake 0.5s',
      },
      height: {
        '630px': '630px'
      },
      padding: {
        '150px': '150px'
      }
    },
  },
  plugins: [
  ],
}
