# Déploiement sur OVH

Ce guide vise un hébergement OVH avec accès SSH. Sur un mutualisé sans SSH/Composer, préparer `vendor/` et `public/build/` localement puis envoyer l’ensemble par SFTP.

**Prérequis impératif :** le domaine OVH doit être configuré en PHP 8.3, 8.4 ou 8.5. Laravel 13 ne fonctionne pas avec PHP 8.2.

## 1. Domaine et dossier public

Créer le sous-domaine `copil.escm.mg` dans OVH et définir sa racine sur :

```text
/home/VOTRE_IDENTIFIANT/www/escm-copil/public
```

Le projet complet reste dans `/home/VOTRE_IDENTIFIANT/www/escm-copil`. Seul son sous-dossier `public` doit être exposé au web.

## 2. Base MySQL

Dans l’espace client OVH :

1. Créer une base MySQL et un utilisateur dédié.
2. Autoriser cet utilisateur uniquement sur cette base.
3. Noter le serveur, le port, le nom de base, l’utilisateur et le mot de passe.

## 3. Envoyer les sources

Décompresser l’archive dans le dossier du projet, puis :

```bash
cd /home/VOTRE_IDENTIFIANT/www/escm-copil
cp .env.example .env
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

Si Node.js 20.19+ n’est pas disponible sur OVH, exécuter `npm install && npm run build` en local et envoyer le dossier `public/build` généré.

## 4. Configurer `.env`

```dotenv
APP_NAME="ESCM COPIL"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://copil.escm.mg

LOG_CHANNEL=stack
LOG_LEVEL=warning

DB_CONNECTION=mysql
DB_HOST=serveur_mysql_ovh
DB_PORT=3306
DB_DATABASE=nom_base_ovh
DB_USERNAME=utilisateur_ovh
DB_PASSWORD=mot_de_passe_ovh

FILESYSTEM_DISK=local
SESSION_DRIVER=file
SESSION_LIFETIME=120
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
```

Puis exécuter :

```bash
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan optimize
```

Le seeder affiche les mots de passe temporaires des comptes qu’il crée. Il ne remplace jamais le mot de passe d’un compte existant et n’écrase pas un rapport existant.

## 5. Droits d’écriture

Le serveur web doit pouvoir écrire dans `storage` et `bootstrap/cache` :

```bash
chmod -R ug+rwX storage bootstrap/cache
```

Ne pas exécuter `chmod -R 777`.

## 6. HTTPS et DNS

1. Faire pointer le DNS de `copil.escm.mg` vers l’hébergement OVH.
2. Activer le certificat SSL dans OVH.
3. Vérifier que `APP_URL=https://copil.escm.mg`.
4. Forcer HTTPS depuis la configuration OVH/Apache si ce n’est pas déjà fait.

## 7. Contrôles après mise en ligne

```bash
php artisan about
php artisan migrate:status
php artisan route:list
```

Vérifier ensuite : connexion, modification d’un KPI, aperçu d’une image, téléchargement privé, calcul M-1, création/clôture d’un projet, mode présentation, export PDF et export PPTX.

## 8. Mise à jour future

Avant chaque mise à jour, sauvegarder MySQL et `storage/app/private/copil`, puis :

```bash
php artisan down
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan migrate --force
php artisan optimize
php artisan up
```

## 9. Sauvegardes

Sauvegarder quotidiennement :

- la base MySQL ;
- `storage/app/private/copil` ;
- le fichier `.env` dans un coffre sécurisé.

Conserver au moins 30 jours d’historique et tester régulièrement une restauration.
