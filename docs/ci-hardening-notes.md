# Durcissement CI (C6) — options

Ces tâches ne sont **pas** branchées de façon obligatoire sur la CI actuelle (voir [.github/workflows/ci.yml](../.github/workflows/ci.yml)) ; elles servent de référence pour les prochaines itérations.

## C6.1 Valider `theme.json`

- Utiliser le schéma officiel référencé dans `theme.json` (`$schema`) : un job peut lancer un validateur JSON Schema (ex. *ajv-cli*) contre l’URL du schéma WordPress, ou s’assurer en revue qu’une montée de version *WordPress* n’introduit pas de clé obsolète.

## C6.2 `parse_blocks` avec WordPress

- Les chaînes de *patterns* / *content/html* peuvent être passées par `parse_blocks( $str )` dans un conteneur WordPress (Docker ou binaire *wp-env*) en CI, avec échec sur blocs inconnus ou JSON d’attributs invalide. Définir un **seuillage** (ex. uniquement les fichiers listés par `register-patterns.php`).

## C6.3 Voir

- [ROADMAP-MERGE-CHECKLIST.md](ROADMAP-MERGE-CHECKLIST.md) pour le contrôle humain reproductible avant merge.
