# Statut de migration `content/html` (C3a / C3b)

Dernière recension : `grep` `core/html` + revue ciblée (thème seul, hors logique **wpis-plugin**).

| Fichier | `core/html` / statut | Notes |
| ------- | -------------------- | ----- |
| [about.html](../content/html/about.html) | **Aucun** — contenu 100% blocs | C3a.1 |
| [how-it-works.html](../content/html/how-it-works.html) | **Aucun** — timeline en blocs | C3a.2 |
| [search-demo.html](../content/html/search-demo.html) | **Réduit** : barre + cartes (démo) dans **deux** blocs *Custom HTML* ; `input` en `readonly` + `aria-label` | C3a.3 (la recherche « vraie » reste le template [search.html](../templates/search.html) + `?s=`) |
| [submitted.html](../content/html/submitted.html) | **Blocs** + message [`.wpis-sr-only`](../assets/css/wpis-global.css) « Submission received » | C3a.4 (cohérence submit/profil côté données = extension plugin) |
| [profile.html](../content/html/profile.html) | **Oui** (mock) | C3a.5 / C3a.6 : coquille ; données = plugin |
| [submit.html](../content/html/submit.html) | **Oui** (forme de démonstration) | C3a.5 : coquille |
| [home.html](../content/html/home.html) | Query `quote` + *template* carte | C3b.1 : re-seed accueil si la base a l’ancien HTML |
| [explore.html](../content/html/explore.html) | Barres *tax* (HTML) + liens **claim** `/claim/…` | C3b.2 : mêmes slugs d’URL que le rewrite `claim` (plugin) |
| [security.html](../content/html/security.html) | **Oui** (démo + `feedlist` pour *feed-demo*) | C3b.3 |
| [empty.html](../content/html/empty.html), [sample.html](../content/html/sample.html) | Divers (démos) | C3b.4 |
| [taxonomy.html](../content/html/taxonomy.html) | Prose (réf. `/claim/`) | Texte pédagogique |

**C3a.7 / 404** : [404.html](../templates/404.html) + [empty.html](../content/html/empty.html).

**C3a.8** : `wp wpis-seed` (thème) ; les commandes *seed* côté plugin ne font pas partie du périmètre de ce document.

---

## C3b.1 (rappel)

L’enregistrement de [feed-demo.js](../assets/js/feed-demo.js) sur l’**accueil** est retiré. Page *security* : possible tant que le markup sert de cible. Désactiver partout : `define( 'WPIS_DISABLE_FEED_DEMO', true );` dans [functions.php](../functions.php).
