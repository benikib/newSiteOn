# BISIKA

BISIKA est une application Laravel de découverte et de gestion d’établissements. Elle comprend un site web public, des espaces de gestion pour les administrateurs, les établissements et les organisations patronales, ainsi qu’une API mobile versionnée.

## Documentation

- [Guide fonctionnel du site web](docs/web-functional-documentation.md) : rôles, parcours, comptes, établissements, cotisations, publications, publicités et services.
- [Documentation technique de l’API mobile](docs/mobile-technical-documentation.md) : architecture, authentification mobile, autorisations et endpoints `/api/v1`.

Les fonctions propres à l’administration et aux organisations patronales sont disponibles sur le site web. Elles ne sont pas toutes exposées par l’API mobile.

## Prérequis

- PHP 8.2 ou ultérieur et Composer.
- Node.js et npm.
- Une base de données prise en charge par Laravel et configurée dans `.env`.

## Installation locale

Depuis la racine du dépôt :

```powershell
composer install
if (-not (Test-Path .env)) { Copy-Item .env.example .env }
php artisan key:generate
npm install
```

Configurez ensuite la connexion à la base de données et les paramètres de messagerie dans `.env`, puis exécutez :

```powershell
php artisan migrate
php artisan storage:link
npm run build
php artisan serve
```

Le site est servi par défaut à l’adresse `http://127.0.0.1:8000`. Les téléversements publics (autorisations, médias publicitaires et photos) nécessitent le lien `storage:link` et un disque `public` correctement configuré.

Pour le développement avec rechargement des ressources front :

```powershell
npm run dev
```

## Tests

Lancer la suite de tests Laravel :

```powershell
php artisan test
```

Les tests d’interface web et d’API utilisent l’environnement de test défini dans `phpunit.xml`.
