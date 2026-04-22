=== WPIS Theme ===
Contributors: jaz_on
Requires at least: 6.9
Tested up to: 6.9
Requires PHP: 8.2
Stable tag: 0.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Block theme (FSE) for WordPress Is…, quote archives, submission flow, contributor profile and product layouts.

== Description ==

Ships static page shells using core blocks, local fonts and theme patterns.

For the full data layer (custom post types, REST and submissions) pair with the **WordPress Is… Core** plugin (`wpis-plugin`).

== Installation ==

1. Upload or deploy the theme folder as `wp-content/themes/wpis-theme/`.
2. Activate under **Appearance → Themes**.
3. Manifest content is optional: use **Appearance → WPIS import** (`themes.php?page=wpis-import`) in the admin for pages and (with wpis-plugin) sample quotes, or run `wp wpis-seed import` to create or refresh pages from `content/html/` and set static front page to Home. Use `wp wpis-seed clean` or `reset` to remove or replace those pages; use `wp wpis seed_starter` or `seed_demo` for quotes from the CLI. The theme does not write pages on activation. Edit the header Navigation under **Appearance → Editor** (FSE); classic **Appearance → Menus** is not used.

== Changelog ==

= 0.1.0 =
* Initial release.
