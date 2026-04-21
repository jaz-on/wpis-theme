# Relevanssi et `wpis-theme`

[Relevanssi](https://www.relevanssi.com/) remplace la recherche WordPress de base. Installez-le comme extension habituelle. Il n’est **pas** requis : sans lui, le gabarit [templates/search.html](../templates/search.html) s’appuie sur la requête de recherche standard.

## Gabarit

- [search.html](../templates/search.html) : bloc *Query* en **`inherit: true`**, pour suivre la requête principale (c’est celle qu’enrichit Relevanssi sur `?s=`).  
- Les résultats utilisent le part [parts/quote-feed-card.html](../parts/quote-feed-card.html) : adaptez si vous mélangez d’autres types de contenu (voir le README du **plugin**).

## Styles

- Le groupe de page a le style de bloc `WPIS search` (`.is-style-wpis-search`) ; des règles complémentaires pour le bloc *Search* et la *Query* se trouvent dans [assets/css/wpis-global.css](../assets/css/wpis-global.css) sous ce sélecteur.

## Configuration

- Côté **plugin** WordPress Is…, voir [RELEVANSSI.md](https://github.com/jaz-on/wpis-plugin/blob/main/docs/RELEVANSSI.md) (indexation, types de publication, reconstruction).
