module.exports = {
  content: [
    "./*.php",       // Alle PHP-Dateien im Root-Verzeichnis
    "./includes/**/*.php",  // Alle PHP-Dateien in Unterordnern
    "./templates/**/*.php", // PHP-Templates
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
