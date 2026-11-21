# Clinique

Application web développée avec Laravel dans le cadre d’un exercice d’apprentissage.
Ce dépôt contient notamment une API, une interface Blade et une configuration prête pour l’utilisation de Sanctum.

## Technologies

- PHP 8.3+
- Laravel 13
- Laravel Sanctum
- Blade
- Bootstrap 5
- Tailwind CSS 4
- Vite 8
- MySQL
- Pest

## Structure du dépôt

```text
Clinique/
├── app/          # Modèles, contrôleurs et logique applicative
├── database/     # Migrations, usines et seeders
├── resources/    # Vues Blade et ressources frontend
├── routes/       # Définition des routes web et API
├── tests/        # Tests automatisés
└── public/       # Point d’entrée public et assets générés
```

## Prérequis

- PHP 8.3 ou supérieur
- Composer
- Node.js et npm
- MySQL

## Installation

1. Cloner le dépôt :

```bash
git clone https://github.com/M15lo16wa/Clinique.git
cd Clinique/Clinique
```

2. Installer les dépendances PHP et JavaScript :

```bash
composer install
npm install
```

3. Créer le fichier d’environnement :

```bash
cp .env.example .env
php artisan key:generate
```

4. Configurer MySQL dans `.env` :

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=clinique
DB_USERNAME=root
DB_PASSWORD=
```

Créez ensuite la base de données `clinique`, puis exécutez :

```bash
php artisan migrate
```

## Démarrage

### Avec les commandes séparées

```bash
php artisan serve
npm run dev
```

L’application sera disponible à l’adresse `http://localhost:8000`.

### Avec la commande Laravel

Le projet fournit aussi une commande de développement complète :

```bash
composer dev
```

Elle lance notamment le serveur Laravel, la file d’attente et Vite.

## Tests

Exécuter la suite de tests avec :

```bash
composer test
```

Ou directement :

```bash
php artisan test
```

## Compilation de production

```bash
npm run build
```

## Documentation de l’API

Une collection Postman est incluse à la racine de l’application :

```text
Clinique/Clinique_API.postman_collection.json
```

Elle peut être importée dans Postman pour tester les routes disponibles.

## Licence

Ce projet est distribué sous licence MIT.
