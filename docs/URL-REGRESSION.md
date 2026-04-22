# Parcours URL (T2.6) — vérification manuelle

Exécuter sur une install avec le **plugin** actif, permaliens rechargés (*Réglages → Permaliens* → Enregistrer). Pour les pages démo du thème : `wp wpis-seed import`. Pour des citations de démo côté plugin : `wp wpis seed_demo` (voir le README du plugin). Sinon données réelles. Cocher en local.


| URL / contexte                                                                | Attendu                                                                                                                                                        |
| ----------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `/` (page d’accueil)                                                          | Le modèle *Front Page* s’applique ; le contenu vient de la page d’accueil (souvent *home* en Query sur `quote`).                                              |
| `/quote/{slug}/` (single)                                                     | [single-quote.html](../templates/single-quote.html) : titre (citation) + contenu.                                                                              |
| `/quote/` (post type archive)                                                 | [archive-quote.html](../templates/archive-quote.html) : liste (Query `inherit: true` sur l’archive `quote`) + pagination.                                      |
| `/claim/{term}/` (taxonomie enregistrée `claim_type`, joli permalien `claim`) | [taxonomy-claim_type.html](../templates/taxonomy-claim_type.html) quand le plugin enregistre la taxo ; 404 de taxo si le plugin est inactif.                   |
| Page `submit`, `about`, etc. (pages *seed* WPIS)                              | [page.html](../templates/page.html) + contenu *pattern* ; pas d’exigence REST pour la coquille.                                                                |
| 404                                                                           | [404.html](../templates/404.html) : pattern *empty* visible.                                                                                                   |
| `POST /wp-json/wpis/v1/quote-feed?...`                                        | JSON avec fragment HTML de cartes (debug plugin / démo) ; le thème FSE s’en passe si Query Loop + template part.                                               |


**Note** : les termes `sentiment` / `claim_type` existent côté plugin ; l’index sur `quote` ne doit pas 404 dès qu’il y a des publications.