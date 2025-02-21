export default {
    plugins: {
        tailwindcss: {},
        autoprefixer: {},
    },
};

// module.exports = {  // Gunakan module.exports jika Anda menambahkan PurgeCSS
//     plugins: [
//       require('tailwindcss'),
//       require('autoprefixer'),
//       require('@fullhuman/postcss-purgecss')({
//         content: [
//           './resources/**/*.blade.php',
//           './resources/**/*.js',
//           './resources/**/*.vue', // Jika Anda menggunakan Vue.js
//         ],
//         defaultExtractor: (content) => content.match(/[\w-/:]+(?<!-)/g) || [],
//         // safelist: []  // Tambahkan safelist jika diperlukan
//       })
//     ]
//   }
