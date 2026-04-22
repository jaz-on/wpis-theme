# FSE exceptions: shortcodes and Custom HTML policy

## Custom HTML (`core/html`)

Shipped theme markup under `templates/`, `parts/`, `content/html/`, and `patterns/*.php` should **not** use `<!-- wp:html -->` except as a last resort: native blocks and shortcodes keep the Site Editor stable. There is no automated scanner in this theme; review changes in the editor.

## Shortcode blocks (native `core/shortcode`)

These shortcodes are intentional; they render HTML that core blocks do not model.


| Shortcode              | Provided by                   | Used in                      |
| ---------------------- | ----------------------------- | ---------------------------- |
| `[wpis_lang_switcher]` | Theme (`inc/languages.php`)   | `parts/header.html`          |
| `[wpis_submit_form]`   | Plugin (`SubmitFormRenderer`) | `content/html/submit.html`   |
| `[wpis_repeat_badge]`  | Plugin (`SubmitFormRenderer`) | `parts/quote-feed-card.html` |


Dynamic profile and submission stats should move to **wpis-plugin** blocks or endpoints when available; until then, demo profile remains static block markup in `content/html/profile.html`.

## Related

- [wpis-fse-architecture.md](../../docs/wpis-fse-architecture.md)
- [wpis-plugin-boundary-submit.md](../../docs/wpis-plugin-boundary-submit.md)