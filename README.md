# WPIS Theme (`wpis-theme`)

Block theme (FSE) for the WordPress Is… project: quote archives and singles, explore and submit flows, contributor profile UI, and companion dynamic blocks.

## Requirements

- WordPress **6.9+**
- PHP **8.2+**
- The **WordPress Is… Core** plugin (folder and slug `wpis-plugin`, text domain `wpis-plugin`) for post types, submission handling, and REST data used by theme blocks

## WordPress metadata

- **`style.css`** headers are what the theme installer uses (including **`Requires PHP`**). Keep them in sync with your host.
- **`readme.txt`** follows the WordPress theme readme format (documentation; some tools read **`Requires PHP`** here too).
- **Fonts** live under `assets/fonts/` (Fraunces + JetBrains Mono, OFL). Declared in `theme.json` `fontFace` — no Google Fonts CDN. See `assets/fonts/README.txt` to refresh files via npm/fontsource on a dev machine.

## Installation

1. Clone or copy this repository into:

   ```text
   wp-content/themes/wpis-theme/
   ```

2. Activate **WPIS Theme** under **Appearance → Themes**.

3. Assign the bundled page templates (Submit, Submitted, Explore, Profile, etc.) to the corresponding pages in the admin, as needed.

## Updates with Git Updater

This theme declares a [Git Updater](https://git-updater.com/knowledge-base/required-headers/) source in `style.css`:

- `GitHub Theme URI: https://github.com/jaz-on/wpis-theme`
- `Primary Branch: main` (required because the default branch is `main`, not `master`)

Bump the **`Version:`** field in `style.css` when you ship changes you want sites to pull.

## Development

No Node build step is required for the current tree. Front-end behavior uses `assets/js/theme-toggle.js` and block `render.php` files under `blocks/`.

## License

GPL-2.0-or-later
