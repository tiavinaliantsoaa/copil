# Architecture technique

## Choix

- Laravel 13 et PHP 8.3+.
- Blade côté serveur, sans Livewire.
- Tailwind CSS 4 compilé par Vite 8.
- JavaScript natif pour les onglets, listes dynamiques, aperçu des fichiers et présentation.
- Chart.js pour les camemberts et comparaisons.
- MySQL avec colonnes JSON pour les rubriques mensuelles flexibles.
- DomPDF pour les PDF et PHPPresentation pour les PowerPoint.

## Données

- `users` : comptes, rôle et activation.
- `periods` : mois, état ouvert/clôturé et auteur de la clôture.
- `communication_reports` : un rapport par mois ; chaque rubrique est stockée dans une colonne JSON structurée.
- `attachments` : métadonnées des fichiers privés.
- `audit_logs` : opérations sensibles et mises à jour.

Le modèle JSON permet d’ajouter plus tard une métrique ou un canal sans multiplier les migrations. Les entités nécessitant des recherches transversales avancées pourront être normalisées progressivement dans des tables dédiées.

## Extension prévue

Les futurs modules Pédagogie, Admissions et RH peuvent reprendre le couple `periods` + table de rapports dédiée. Les imports Excel/CSV peuvent alimenter les mêmes structures après validation. Les notifications peuvent écouter les événements de clôture et les échéances du plan d’action.
