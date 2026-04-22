# Stratégie de `index.html` (T2.1)

## Rôle

Le modèle [templates/index.html](../templates/index.html) est le **repli** WordPress quand aucun modèle plus spécifique ne s’applique. Il n’est **pas** la page d’accueil du site si une page statique sert de front (réglage *Réglages → Lecture*), mais il reste utile pour :

- la liste de blog **ou** celle de `quote` si on aligne l’**index** sur le contenu produit (plugin) ;
- les environnements où le front n’est pas encore câblé.

## Décision

1. `index.html` cible le CPT `**quote`** (plugin `wpis-plugin`) : `postType: "quote"`, `inherit: true` conservé pour hériter du **contexte** (archives, etc.) le cas échéant. Pour un vrai *blog* WordPress classique, il faudrait un `home.html` basé sur `post` **ou** une page d’archives personnalisée. Ce projet n’expose pas un blog d’articles séparé : l’entité de liste principale est la **citation** (`quote`).
2. **Pagination** : les blocs `query-pagination` couvrent les listes paged. Les archives de type `quote` utilisent [archive-quote.html](archive-quote.html) quand l’hierarchie s’y prête (priorité de modèle).
3. L’[audit](wpis-fse-theme-audit.md) (gap « index sur post ») est **fermé côté intention** : l’index ne liste plus les `post` de démo, mais le même modèle d’**expérience** (carte) que l’**archive** `quote` via le template part de carte (voir [parts/quote-feed-card.html](../parts/quote-feed-card.html)).

## Références

- [THEME-API-CONTRACT.md](../../wpis-plugin/docs/THEME-API-CONTRACT.md) (CPT, taxo, slugs, REST)
- [URL-REGRESSION.md](URL-REGRESSION.md) (par manuel local)