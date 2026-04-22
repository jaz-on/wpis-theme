# WPIS Theme (`wpis-theme`)

Block theme (FSE) for the WordPress Is… project: product-aligned layouts with core blocks and patterns, ready for quote archives, explore and submit flows and contributor profile UI.

## Requirements

- WordPress **6.9+**
- PHP **8.2+**
- The **WordPress Is… Core** plugin (`wpis-plugin`) when you wire dynamic quotes, submissions and REST. The theme runs standalone for static screen content until the plugin is connected.
- **Relevanssi** (optional) for enhanced site search; see the [Relevanssi plugin page](https://wordpress.org/plugins/relevanssi/) if you use it.

## WordPress metadata

- `**style.css`** headers are what the theme installer uses (including `**Requires PHP`**). Keep them in sync with your host.
- `**readme.txt`** follows the WordPress theme readme format (documentation; some tools read `**Requires PHP`** here too).
- **Fonts** live under `assets/fonts/` (Fraunces + JetBrains Mono, OFL). Declared in `theme.json` (`fontFace`). No Google Fonts CDN.

## Installation

1. Clone or copy this repository into:

   ```text
   wp-content/themes/wpis-theme/
   ```

2. Activate **WPIS Theme** under **Appearance → Themes**.

3. **Demo pages are not created on activation** (same idea as Twenty Twenty). **In the admin:** go to **Appearance → Import demo** and use **Import demo pages** (or **Remove demo pages** / **Reset demo**). With **WP-CLI**: **`wp wpis-seed import`** (overwrites existing demo page bodies from files by default; use `--no-sync` to skip), **`wp wpis-seed clean`** (`--force` to delete permanently), or **`wp wpis-seed reset`** (clean then import). This sets **Reading** to static **Home** unless you uncheck the box in the admin or pass **`--no-reading`** in the CLI. **Navigation** is edited in **Appearance → Editor** (Header template part); there is no classic menu location.

## Updates with Git Updater

This theme declares a [Git Updater](https://git-updater.com/knowledge-base/required-headers/) source in `style.css`:

- `GitHub Theme URI: https://github.com/jaz-on/wpis-theme`
- `Primary Branch: main` (required because the default branch is `main`, not `master`)

Bump the `**Version:`** field in `style.css` when you ship changes you want sites to pull.

## Development

No Node build step is required for the theme on the server. Front-end behaviour uses `assets/js/theme-toggle.js` and `assets/css/wpis-global.css`.

**Demo import:** `wp wpis-seed help` (requires [WP-CLI](https://wp-cli.org/) with this theme active).

Cursor rules for agents live in [`.cursor/rules`](.cursor/rules). Maintainer-only notes may still live in a local `.doc/` folder (gitignored).

## License

GPL-2.0-or-later
