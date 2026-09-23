# ESCM COPIL Communication

Application web privée de reporting mensuel, réalisée avec Laravel 13, Blade, JavaScript classique, Tailwind CSS 4 et MySQL. Aucun composant Livewire n’est utilisé.

## Fonctions incluses

- Connexion email/mot de passe et comptes administrés en interne.
- Rôles : Administrateur, Direction, Responsable Communication, Lecture seule.
- Périodes mensuelles enregistrées, clôturables et consultables dans les archives.
- Calcul automatique des écarts M-1, des totaux de leads, de la portée et du budget.
- Facebook, Instagram, TikTok, LinkedIn, Site & Google, Email et SMS séparés.
- Graphiques en camembert et comparaison mensuelle, avec couleurs des plateformes.
- Meilleur contenu / contenu à améliorer pour chaque plateforme, avec visuels distincts.
- Graphisme & visuels, Formation Pro, Informatique & outils, Événements et Projets.
- Ajout, clôture et suppression logique des projets dans les formulaires mensuels.
- Vrais fichiers privés (images, PDF, PPTX, Word, Excel, CSV) avec aperçu des images.
- Plan d’actions et décisions avec responsable, échéance, priorité, KPI et statut.
- Mode présentation plein écran avec sommaire et transitions visuelles.
- Exports PDF et PowerPoint `.pptx`.
- Journal d’audit des principales modifications.

## Prérequis

- PHP 8.3, 8.4 ou 8.5 avec `ctype`, `curl`, `dom`, `fileinfo`, `gd`, `intl`, `mbstring`, `openssl`, `pdo_mysql`, `session`, `tokenizer`, `xml`, `zip`.
- Composer 2.
- MySQL 5.7.8 minimum ou MySQL 8 recommandé.
- Node.js 20.19+ ou 22.12+ et npm/pnpm pour compiler Tailwind et JavaScript.

## Installation locale

```bash
cp .env.example .env
composer install
php artisan key:generate
```

Créer une base MySQL puis compléter dans `.env` :

```dotenv
APP_NAME="ESCM COPIL"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=escm_copil
DB_USERNAME=escm_copil
DB_PASSWORD=mot_de_passe_mysql
```

Créer les tables et les données de démonstration :

```bash
php artisan migrate --seed
```

La commande affiche une seule fois les mots de passe temporaires aléatoires des quatre comptes. Les conserver dans un gestionnaire de mots de passe, se connecter avec `admin@escm.mg`, puis modifier les comptes dans **Utilisateurs**.

Compiler l’interface et démarrer :

```bash
npm install
npm run build
php artisan serve
```

Ouvrir `http://127.0.0.1:8000`.

## Premier administrateur sans données de démonstration

Après `php artisan migrate`, utiliser :

```bash
php artisan escm:create-admin admin@votre-domaine.mg
```

Pour obtenir les mois de démonstration, exécuter ensuite `php artisan db:seed`. Le seeder n’écrase ni les comptes ni les rapports déjà existants.

## Utilisation par les équipes

1. L’administrateur crée les comptes et attribue les rôles.
2. Le Responsable Communication choisit le mois et remplit chaque rubrique.
3. Chaque bouton **Enregistrer** sauvegarde la rubrique dans MySQL.
4. Les visuels sont envoyés dans leur bloc ; leur aperçu apparaît après l’envoi.
5. La Direction consulte les indicateurs, le mode présentation, le PDF ou le PPTX.
6. L’administrateur clôture le mois dans **Archives**. Le mois devient non modifiable pour les autres rôles.
7. Le nouveau mois reprend la structure des outils, avec les indicateurs remis à zéro.

## Structure utile

- `app/Http/Controllers` : authentification et fonctionnalités métier.
- `app/Services/ReportCalculator.php` : calculs M-1 et indicateurs consolidés.
- `app/Services/ReportBlueprint.php` : structure d’un nouveau mois.
- `app/Services/PptxExportService.php` : génération PowerPoint.
- `database/migrations` : structure MySQL.
- `database/seeders/DatabaseSeeder.php` : démonstration Juillet-Septembre 2026.
- `resources/views` : écrans Blade, présentation et PDF.
- `resources/css/app.css` : charte Tailwind CSS 4 ESCM rouge et blanc.
- `resources/js/app.js` : navigation, graphiques et aperçu des fichiers.
- `storage/app/private/copil` : fichiers privés envoyés par les utilisateurs.

## Déploiement

Le guide complet se trouve dans [`DEPLOIEMENT_OVH.md`](DEPLOIEMENT_OVH.md).

## Sécurité

- `APP_DEBUG` doit être `false` en production.
- Le dossier public du domaine doit pointer vers `public/`, jamais vers la racine du projet.
- Les pièces jointes sont conservées hors du dossier public et servies après authentification.
- Aucun mot de passe réel n’est présent dans le dépôt.
- Les sauvegardes MySQL et `storage/app/private/copil` doivent être quotidiennes.

## Tests

```bash
php artisan test
npm run build
```
