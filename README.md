# WPIS Theme (`wpis-theme`)

Block theme (FSE) for the WordPress Is… project: product-aligned layouts with core blocks and patterns, ready for quote archives, explore and submit flows and contributor profile UI.

## Requirements

- WordPress **6.9+**
- PHP **8.2+**
- The **WordPress Is… Core** plugin (`wpis-plugin`) when you wire dynamic quotes, submissions and REST. The theme runs standalone for static screen content until the plugin is connected.
- **Relevanssi** (optional) replaces default site search; see [docs/RELEVANSSI.md](docs/RELEVANSSI.md) and the plugin’s [RELEVANSSI.md](https://github.com/jaz-on/wpis-plugin/blob/main/docs/RELEVANSSI.md) for indexing `quote`.

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
3. **Demo pages are not created on activation** (same idea as Twenty Twenty). **In the admin:** go to **Appearance → Import demo** and use **Import demo pages** (or **Remove demo pages** / **Reset demo**). Same options as the CLI, without WP-CLI. To import from the terminal, run **`wp wpis-seed import`** (overwrites existing demo page bodies from files by default; use `--no-sync` to skip). To trash those pages: **`wp wpis-seed clean`** (`--force` to delete permanently). **`wp wpis-seed reset`** runs clean then import. Without WP-CLI: `WP_LOAD_PATH=/path/to/wp-load.php php tools/seed-demo.php import` (same subcommands and flags). This sets **Reading** to static **Home** and rebuilds the **WPIS Primary** menu unless you uncheck the boxes in the admin or pass `--no-reading` or `--no-menu` in the CLI.
4. Operator notes and checklists: **[contribution/README.md](../contribution/README.md)**. Architecture contract: **[docs/wpis-fse-architecture.md](../docs/wpis-fse-architecture.md)**.

## Updates with Git Updater

This theme declares a [Git Updater](https://git-updater.com/knowledge-base/required-headers/) source in `style.css`:

- `GitHub Theme URI: https://github.com/jaz-on/wpis-theme`
- `Primary Branch: main` (required because the default branch is `main`, not `master`)

Bump the `**Version:`** field in `style.css` when you ship changes you want sites to pull.

## Development

No Node build step is required for the theme on the server. Front-end behaviour uses `assets/js/theme-toggle.js` and `assets/css/wpis-global.css`. Optional: use npm only to refresh font files (see `assets/fonts/README.txt`).

**FSE migration checklist** (tokens, URLs): see [docs/wpis-fse-migration-baseline.md](../docs/wpis-fse-migration-baseline.md) in the mono-repo, or copy that doc beside this theme if you use a single-theme repo.

**PHP:** `composer install` then `composer run phpcs` (WordPress rules in `phpcs.xml.dist`). CI on GitHub runs `php -l` on all PHP files and PHPCS on push / pull request to `main`.

**Demo import:** `wp wpis-seed help` (requires [WP-CLI](https://wp-cli.org/) with this theme active).

## License

GPL-2.0-or-later