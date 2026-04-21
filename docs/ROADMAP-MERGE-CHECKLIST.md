# Checklist de merge (transversal, X + C6.3)

Avant de fusionner un lot thème / plugin (roadmap post-audit) :

1. **Fichiers** : noter chaque chemin modifié ; pour le CSS, indiquer les plages (réf. [wpis-global-css-inventory.md](wpis-global-css-inventory.md)).
2. **PHP** : `composer run phpcs` (thème) ; `composer run lint` / `test` (plugin) si touché.
3. **Blocs** : `php tools/verify-markup.php` (strict comme CI) depuis `wpis-theme/`.
4. **Rendu** : 2 écrans au navigateur (accueil + page ou archive `quote` avec permaliens) ; thème + plugin actifs.
5. **FSE** : si modèles *templates/* ou *parts/* : ouvrir l’**Éditeur de site* pour absence d’erreur de validation de blocs.
6. **Lecture** : `theme.json` non cassé (pas de JSON invalide) ; `wp is-theme` optionnel.
7. **Détente C4** : si `feed-demo` est retiré ou désactivé, confirmer qu’il ne reste pas de dépendance de contenu statique cachée sur la home (chantier C3b).

Cette liste complète les tâches optionnelles C6.1 (schéma `theme.json`) et C6.2 (CI `parse_blocks`) décrites dans [ci-hardening-notes.md](ci-hardening-notes.md).
