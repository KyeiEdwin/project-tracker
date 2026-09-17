/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        primary: 'rgb(var(--primary-rgb))',
        secondary: 'rgb(var(--secondary-rgb))',
        success: 'rgb(var(--success-rgb))',
        danger: 'rgb(var(--danger-rgb))',
        warning: 'rgb(var(--warning-rgb))',
        info: 'rgb(var(--info-rgb))',
        light: 'rgb(var(--light-rgb))',
        dark: 'rgb(var(--dark-rgb))',
        defaulttextcolor: 'rgb(var(--default-text-color))',
        textmuted: 'rgb(var(--text-muted))',
        bodybg: 'rgb(var(--body-bg))',
        defaultborder: 'rgb(var(--default-border))',
      },
    },
  },
  plugins: [],
}
