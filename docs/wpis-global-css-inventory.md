# Inventaire `wpis-global.css` (roadmap audit)

Correspondance **zone de lignes** → **fichiers / role** → **action**. Les classes `.nav-bar` et `.screen` (maquette outil) **ne figurent pas** dans le fichier actuel (vérification 2026).

## C0.1 — L1–22 (en-tête, `html`, `.wp-site-blocks`)

- **Rôle** : commentaire d’intention ; fond `html` ; conteneur racine site.
- **Alignement** : tous les [templates](../templates/) (`front-page`, `page`, `index`, etc.) déversent dans `.wp-site-blocks`.
- **Action** : **garder** ; migration `theme.json` limitée (le `min-height` et le padding racine restent souvent en CSS).

## C0.2 — L23–82 (aliases sémantiques, dark OS, `data-theme`)

- **Rôle** : `--bg`, `--ink`, etc. mappés sur les presets + variantes nuit.
- **Alignement** : [theme.json](../theme.json) (palette) + [assets/js/theme-toggle.js](../assets/js/theme-toggle.js) (`data-theme`).
- **Action** : **garder** ; toute la chaîne des maquettes repose sur ces alias côté contenu enregistré.

## C0.3 — L84–116 (`.skip-link`, marque, langue, toggle)

- **Rôle** : accessibilité ; titre site ; `lang-switcher` ; bouton thème.
- **Alignement** : [parts/header.html](../parts/header.html) (skip généré en PHP [functions.php](../functions.php)).
- **Action** : **garder** ; le skip reste en PHP par choix d’éviter les blocs invalides en éditeur.

## C0.4 — L117–220 (header bande, footer, nav)

- **Rôle** : grilles en-tête / pied, soulignement onglet courant, `is-style-wpis-footer-top`, `is-style-wpis-footer-trademark`.
- **Alignement** : [parts/header.html](../parts/header.html), [parts/footer.html](../parts/footer.html).
- **Action** : **garder** ; partiellement migrable vers styles de blocs long terme.

## C0.5 — L234–361 (explore, cartes tax / plateforme)

- **Rôle** : `is-style-wpis-explore-section`, `.tax-card`, `.platform-card`, `wpis-sr-only`.
- **Alignement** : [content/html/explore.html](../content/html/explore.html) ; patterns via [inc/register-patterns.php](../inc/register-patterns.php).
- **Action** : **garder** jusqu’à requêtes taxo / archives dynamiques côté plugin.

## C0.6 — L364–396 (`:has`, héro, `wpis-prose` em, excerpt)

- **Rôle** : paragraphe type accroche feed ; héro ; emphase *About*.
- **Alignement** : [content/html/home.html](../content/html/home.html), [content/html/about.html](../content/html/about.html).
- **Action** : **garder** ; le `:has` cible le passage progressif de feed HTML → blocs.

## C0.7 — L397~480 — How (timeline)

- **Rôle** : `.is-style-wpis-how`, `.how-step`, `.num`, etc.
- **Alignement** : [content/html/how-it-works.html](../content/html/how-it-works.html) (reste partiellement `core/html` dans le contenu).
- **Action** : finaliser remplacement `core/html` par blocs natifs (chantier C3a.2).

## C0.8 — L482~602 — Profil

- **Rôle** : `.is-style-wpis-profile`, cartes stats, file de soumissions.
- **Alignement** : [content/html/profile.html](../content/html/profile.html) (largement `core/html` ; données = plugin + C5).
- **Action** : **garder** ; raccorder champs quand l’API profil est stable.

## C0.9 — L603–628 — Hero stats (pattern)

- **Rôle** : grille `.hero-stats` pour 4 [paragraph] dans le groupe.
- **Alignement** : [patterns/hero-stats-row.php](../patterns/hero-stats-row.php).
- **Action** : **garder** ; chiffres encore statiques (remplacer par données réelles = plugin / options).

## C0.10 — L629–678 — Feed, cartes, filtres, `feed-demo.js`

- **Rôle** : barre feed, `quote-card`, filtres, `load-more` ; alimente la **dette C4** si JS actif.
- **Alignement** : [content/html/home.html](../content/html/home.html), [assets/js/feed-demo.js](../assets/js/feed-demo.js).
- **Action** : remplacer par Query Loop (plugin) ; désenregistrer le JS (constante `WPIS_DISABLE_FEED_DEMO` ou retrait).

## C0.11 — L680–694 — Formulaire submit

- **Rôle** : champs, zone upload, `rgpd-notice`, boutons.
- **Alignement** : [content/html/submit.html](../content/html/submit.html) ; logique d’envoi = [wpis-plugin](../../wpis-plugin/) (voir [SUBMIT-BOUNDARY.md](SUBMIT-BOUNDARY.md)).
- **Action** : **garder** styles coquille ; ne pas mettre de logique métier dans le thème.

## C0.12 — L695–710 — Recherche démo, `subcat-bar`

- **Rôle** : UI recherche type maquette (pas le template de recherche WP natif seul).
- **Alignement** : [content/html/search-demo.html](../content/html/search-demo.html).
- **Action** : **garder** ; rapprocher du template [templates/search.html](../templates/search.html) en production si besoin.

## C0.13 — L711–768 — Media queries et `:hover`

- **Rôle** : renforce tailles, flex feed, `tax-grid` desktop, hover boutons.
- **Alignement** : recouvre les mêmes classes que C0.4–C0.12.
- **Action** : pas d’orphelin identifié ; toute règle modifiée ici doit être re-testée sur mobile.

## C0.14 — Synthèse action

| Décision | Zones |
| -------- | ----- |
| Garder (contrat sémantique + chrome) | C0.1–2, C0.3–4, C0.5–6, C0.9, C0.11–12, C0.13 |
| Réduire après dynamique (plugin) | C0.5, C0.6, C0.7, C0.8, C0.10 |
| Retrait partiel (JS supprimé sur l’**accueil**) | C0.10 : conserver règles *feed* / filtres tant que [security.html](../content/html/security.html) charge `feed-demo.js` (voir also [wpis-fse-theme-audit](wpis-fse-theme-audit.md)) |
