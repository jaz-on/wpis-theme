Bundled webfonts (local hosting, no Google Fonts CDN):

- Fraunces (variable, Latin) — SIL Open Font License 1.1
  Source: https://github.com/googlefonts/fraunces
  Files extracted via @fontsource-variable/fraunces (same glyphs).

- JetBrains Mono (400/500, Latin, normal + italic) — SIL Open Font License 1.1
  Source: https://github.com/JetBrains/JetBrainsMono
  Files extracted via @fontsource/jetbrains-mono.

To refresh from npm (dev machine only):
  npm install @fontsource-variable/fraunces @fontsource/jetbrains-mono
  cp node_modules/@fontsource-variable/fraunces/files/fraunces-latin-wght-*.woff2 ./
  cp node_modules/@fontsource/jetbrains-mono/files/jetbrains-mono-latin-{400,500}-*.woff2 ./
