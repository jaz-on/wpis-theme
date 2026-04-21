# WPIS Theme (`wpis-theme`)

Block theme (FSE) for the WordPress Is… project: product-aligned layouts with core blocks and patterns, ready for quote archives, explore and submit flows and contributor profile UI.

## Requirements

- WordPress **6.9+**
- PHP **8.2+**
- The **WordPress Is… Core** plugin (`wpis-plugin`) when you wire dynamic quotes, submissions and REST. The theme runs standalone for static screen content until the plugin is connected.

## WordPress metadata

- `**style.css`** headers are what the theme installer uses (including `**Requires PHP`**). Keep them in sync with your host.
- `**readme.txt`** follows the WordPress theme readme format (documentation; some tools read `**Requires PHP`** here too).
- **Fonts** live under `assets/fonts/` (Fraunces + JetBrains Mono, OFL). Declared in `theme.json` (`fontFace`). No Google Fonts CDN. See `assets/fonts/README.txt` to refresh files via npm/fontsource on a dev machine.

## Installation

1. Clone or copy this repository into:
  ```text
   wp-content/themes/wpis-theme/
  ```
2. Activate **WPIS Theme** under **Appearance → Themes**.
3. On first activation the theme creates **pages** when missing by reading each manifest seed from `[content/html/](./content/html/)` through `wpis_theme_get_content_html()` (the same helper used by `patterns/*-body.php`). It sets **Reading** to a static front page (`home`) once and ensures a **WPIS Primary** menu on the `primary` location. Existing page content is never overwritten on reactivation.
4. Operator notes and checklists: **[contribution/README.md](../contribution/README.md)**. Architecture contract: **[docs/wpis-fse-architecture.md](../docs/wpis-fse-architecture.md)**.

## Updates with Git Updater

This theme declares a [Git Updater](https://git-updater.com/knowledge-base/required-headers/) source in `style.css`:

- `GitHub Theme URI: https://github.com/jaz-on/wpis-theme`
- `Primary Branch: main` (required because the default branch is `main`, not `master`)

Bump the `**Version:`** field in `style.css` when you ship changes you want sites to pull.

## Development

No Node build step is required for the theme on the server. Front-end behaviour uses `assets/js/theme-toggle.js` and `assets/css/wpis-chrome.css`. Optional: use npm only to refresh font files (see `assets/fonts/README.txt`).

**FSE migration checklist** (tokens, URLs): see [docs/wpis-fse-migration-baseline.md](../docs/wpis-fse-migration-baseline.md) in the mono-repo, or copy that doc beside this theme if you use a single-theme repo.

**PHP:** `composer install` then `composer run phpcs` (WordPress rules in `phpcs.xml.dist`). CI on GitHub runs `php -l` on all PHP files and PHPCS on push / pull request to `main`.

## License

GPL-2.0-or-later