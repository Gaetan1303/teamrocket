# Symfony Docker

A [Docker](https://www.docker.com/)-based installer and runtime for the [Symfony](https://symfony.com) web framework,
with [FrankenPHP](https://frankenphp.dev) and [Caddy](https://caddyserver.com/) inside!

![CI](https://github.com/dunglas/symfony-docker/workflows/CI/badge.svg)

## Getting Started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose build --pull --no-cache` to build fresh images
3. Run `docker compose up --wait` to set up and start a fresh Symfony project
4. Open `https://localhost` in your favorite web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)
5. Run `docker compose down --remove-orphans` to stop the Docker containers.

## Features

- Production, development and CI ready
- Just 1 service by default
- Blazing-fast performance thanks to [the worker mode of FrankenPHP](https://github.com/dunglas/frankenphp/blob/main/docs/worker.md) (automatically enabled in prod mode)
- [Installation of extra Docker Compose services](docs/extra-services.md) with Symfony Flex
- Automatic HTTPS (in dev and prod)
- HTTP/3 and [Early Hints](https://symfony.com/blog/new-in-symfony-6-3-early-hints) support
- Real-time messaging thanks to a built-in [Mercure hub](https://symfony.com/doc/current/mercure.html)
- [Vulcain](https://vulcain.rocks) support
- Native [XDebug](docs/xdebug.md) integration
- Super-readable configuration

**Enjoy!**

## Docs

1. [Options available](docs/options.md)
2. [Using Symfony Docker with an existing project](docs/existing-project.md)
3. [Support for extra services](docs/extra-services.md)
4. [Deploying in production](docs/production.md)
5. [Debugging with Xdebug](docs/xdebug.md)
6. [TLS Certificates](docs/tls.md)
7. [Using MySQL instead of PostgreSQL](docs/mysql.md)
8. [Using Alpine Linux instead of Debian](docs/alpine.md)
9. [Using a Makefile](docs/makefile.md)
10. [Updating the template](docs/updating.md)
11. [Troubleshooting](docs/troubleshooting.md)

## License

Symfony Docker is available under the MIT License.

## Credits

Created by [Kévin Dunglas](https://dunglas.dev), co-maintained by [Maxime Helias](https://twitter.com/maxhelias) and sponsored by [Les-Tilleuls.coop](https://les-tilleuls.coop).




## 🚀 Team Sbires : Plateforme de Recrutement Vilain (Symfony Fanmade)

Ce projet est une application web Symfony fanmade simulant la plateforme de recrutement et de gestion des "sbires" pour les différentes Organisations Vilaines de l'univers Pokémon (Team Rocket, Team Magma, Team Aqua, etc.).

L'objectif est de fournir une interface thématique pour l'enrôlement et la gestion administrative.

## 📋 Fonctionnalités Actuelles

Interface Publique

Route	Description	Composants Clés
/ (Accueil)	Page d'accueil thématique affichant la liste des Teams Vilains existantes. Elle intègre un appel à une API externe pour présenter un Pokémon cible aléatoire, illustrant l'objectif actuel des Teams.	HomeController.php (Service API), index.html.twig.
/register (Enrôlement)	Formulaire d'inscription pour les nouveaux sbires. L'interface utilise du JavaScript pour mettre à jour en temps réel l'affichage (logo, crédo, couleur de la carte) selon la Team Vilain choisie.	RegistrationController.php, register.html.twig, JavaScript dynamique.

Authentification & Admin

Fonctionnalité	Description	Technologies Clés
Authentification	Gestion complète des utilisateurs (User), incluant un champ unique de Codename et la vérification obligatoire de l'email (symfonycasts/verify-email-bundle).	User.php, Symfony Security.
Administration	Tableau de bord centralisé (via EasyAdminBundle) permettant la gestion complète des entités du projet (User, TeamVilain, Pokemon, Chat).	DashboardController.php, *CrudController.php.
Seed Data (CLI)	Commande pour injecter la liste initiale des Teams Vilains (Rocket, Magma, Aqua, etc.) et leurs codes couleurs dans la base de données.	LoadTeamsCommand.php.

## 💾 Structure de la Base de Données (ORM Doctrine)

Le projet utilise les entités suivantes, avec les relations clés :
Entité	Description	Relations (Clés étrangères)
User (Sbire)	L'utilisateur du système. Contient les données d'enrôlement (codename, starterPokemon).	➡️ ManyToOne vers <span style="color: #DC2626;">TeamVilain</span> (Un Sbire appartient à une Team).
<span style="color: #DC2626;">TeamVilain</span>	L'organisation criminelle. Contient le credo et le colorCode pour l'affichage dynamique.	⬅️ OneToMany vers <span style="color: #2563EB;">User</span> (Une Team a plusieurs Sbires).
<span style="color: #F59E0B;">Chat</span>	Utilisée pour les messages, probablement en vue d'un futur système de messagerie interne.	➡️ ManyToOne vers <span style="color: #2563EB;">User</span> (Un message est lié à son auteur).
Pokemon	Entité de catalogue pour suivre les Pokémon (Sauvage, Capturé, Volé).	Aucune relation directe définie pour le moment (catalogue simple).

Légende des couleurs : <span style="color: #DC2626;">TeamVilain</span> | <span style="color: #2563EB;">User</span> | <span style="color: #F59E0B;">Chat</span>

## ⚙️ Prérequis et Installation

Utilisation de Docker du haut de la présentation

NOTE : Ce projet utilise une structure de démarrage basée sur un template Docker Compose Symfony (FrankenPHP/Caddy) créé par une personne tierce. Cela simplifie la configuration de l'environnement de développement (PHP, serveur web Caddy, base de données).

Étapes

    Prérequis : Assurez-vous que Docker et Docker Compose sont installés sur votre machine.

    Clonage :
    Bash

git clone [VOTRE_RÉPERTOIRE_GIT]
cd [NOM_DU_PROJET]

Démarrage de l'environnement :
Bash

docker compose build --pull --no-cache
docker compose up -d

Installation des dépendances et de la base de données :
Exécutez ces commandes à l'intérieur du conteneur PHP (via docker compose exec php ou un shell) :
Bash

# Installation des dépendances PHP
composer install

# Création de la base de données et des tables
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# Chargement des Teams Vilains initiales

php bin/console app:load-teams

Nous avons tutilisé EasyAdmin 4 pour la gestion de ce projet.

Accès :

    Le site public est accessible à https://localhost (vous devrez peut-être accepter le certificat auto-généré du serveur Caddy).

    L'interface d'administration est disponible sur https://localhost/admin.