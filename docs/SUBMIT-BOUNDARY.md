# Périmètre submit / profil (C5) — thème vs plugin

## C5.1 Point de départ (mono-dépôt)

- La référence de boundary « officielle » reste [wpis-plugin-boundary-submit.md](../../docs/wpis-plugin-boundary-submit.md) à la racine du dépôt parent (copiée ou liée par convention interne). Ce document **thème** rappelle le découpage opérationnel pour l’**équipe thème** uniquement.

## C5.2 Rôle **wpis-theme**

- Fournir le **gabarit** de page submit : groupes, titres, texte, **formulaire de démonstration** en `core/html` ou blocs, classes `.form-group`, `.btn-primary`, `upload-zone`, etc. ([wpis-global.css](../assets/css/wpis-global.css)).
- **Aucun** enregistrement côté serveur, **aucun** **nonce** final de production, **aucun** point de terminaison authentique dans le thème seul.
- Accessibilité de la coquille : `label` associé aux champs *de démo* ; quand le plugin prendra le relais, les mêmes classes peuvent recouvrir le markup injecté (voir C5.4).

## C5.3 Rôle **wpis-plugin**

- Soumission, validation, pièces jointes, taux d’essai, compte, **REST** documentée dans [THEME-API-CONTRACT.md](../../wpis-plugin/docs/THEME-API-CONTRACT.md). Les endpoints « attendus » ne sont **pas** du ressort du thème.

## C5.4 Synchronisation des classes

- Si le plugin remplace le formulaire par un bloc enregistré ou un *mount* JS, les noms de classes (`.form-group`, etc.) doivent rester stables **ou** la feuille `wpis-global.css` doit appliquer les nouveaux sélecteurs. Toute refonte lourde = mise à jour conjointe de ce fichier + contrat d’API.