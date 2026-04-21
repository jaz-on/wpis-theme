# Couleurs sémantiques et filtre `the_content` (C1)

## C1.1 Relevé d’usage `--wp--preset--color` (grep)

Dans le thème, les occurrences ciblent surtout le **contenu** et quelques en-têtes :

| Zone | Fichiers |
| ---- | -------- |
| Filtre central | [functions.php](../functions.php) (`wpis_theme_semantic_colors_in_content` : la map des remplacements) |
| `content/html` | [submitted.html](../content/html/submitted.html), [empty.html](../content/html/empty.html), [sample.html](../content/html/sample.html) |
| `parts` | [footer.html](../parts/footer.html) (réduit) |
| Aucun usage dans d’autres `content/html` au scan courant | about, home (beaucoup de HTML brut sans preset dans les `core/html` statiques) |

**Note** : le filtre s’applique au flux filtrant `the_content` (et donc au bloc contenu d’une page, pas à tout le HTML du site). Les feuillettes `theme.json` et `wpis-global.css` utilisent en priorité les **alias** `--ink`, `--bg`, etc. pour le chrome.

## C1.2–C1.3 Décision : garder le filtre

**Stratégie retenue** : conserver le remplacement des `var(--wp--preset--color--*)` vers les alias sémantiques dans `the_content` pour le contenu déjà enregistré, au lieu d’importer toutes les pages vers des couleurs déjà mappées à la main. Cela évite une ré-édition massive et garde le mode clair / sombre cohérent sur les contenus importés (cartes, paragraphes de démo).

**Alternative écartée pour l’instant** : retirer le filtre et migrer chaque enregistrement de page vers des styles qui n’utilisent que `var(--ink)` (coût élevé, gain surtout esthétique / normalisation).

## C1.4 Vérifications manuelles

- Front : home, about, un écran avec `core/html` riche, mode `prefers-color-scheme` + bascule `data-theme` (thème nuit explicite).
- Éditeur : ouvrir une page concernée, confirmer qu’il n’y a pas d’artefacts liés à la **prévisualisation** (le filtre s’applique au rendu, pas à l’enregistrement des blocs).
