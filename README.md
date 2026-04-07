# Refonte Atais Informatique

## Installation
composer install

## Base de données
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

## Lancer le projet
symfony serve