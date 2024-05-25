/** @type {import('tailwindcss').Config} */
import preset from './vendor/filament/support/tailwind.config.preset'

const defaultTheme = require('tailwindcss/defaultTheme');
const colors = require('tailwindcss/colors')


export default {
    presets: [preset],
    content: [
        './modules/*/resources/**/*.blade.php',
        './resources/views/**/*.blade.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: colors.emerald,
            },
        },
    }
}
