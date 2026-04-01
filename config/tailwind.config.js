/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
    ],
    // This is required for daisyUI to define your themes
    daisyui: {
        themes: ["light", "dark", "corporate"], 
        darkTheme: "dark",
        base: true, 
        styled: true, 
        utils: true,
    },
}