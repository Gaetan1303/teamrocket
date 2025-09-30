Pokémon Fan-Made - Cahier des Charges
Introduction

Ce projet a pour but de créer une version fan-made d'un jeu Pokémon en utilisant le framework Symfony avec Symfony UX (Turbo, Live Components, etc.). Le jeu se déroule dans un environnement textuel et graphique inspiré des classiques jeux Pokémon, avec des éléments de gameplay comme l'exploration, le combat, la capture de Pokémon et la gestion de l'inventaire.

Objectifs

L'objectif est de recréer l'expérience d'un jeu Pokémon classique en ligne, tout en utilisant les outils modernes de développement web comme Symfony UX pour une interactivité fluide.

Fonctionnalités
1. Exploration de la carte

Description : Le joueur peut explorer une région de Pokémon en se déplaçant entre différentes villes, routes, et autres lieux.

Fonctionnalités associées :

Carte interactive avec des points d'intérêt (Villes, Routes, Forêts).

Chaque point d'intérêt peut être une ville, un lieu de rencontre de Pokémon sauvage, ou un élément interactif comme un PNJ ou une arène.

Lorsque le joueur clique sur un lieu, un Turbo Frame permet de charger le contenu associé sans recharger la page.

2. Système de combat

Description : Le joueur rencontre des Pokémon sauvages ou combat avec d’autres dresseurs.

Fonctionnalités associées :

Combat au tour par tour : Le joueur et le Pokémon sauvage ou d'un autre dresseur s'affrontent.

Le joueur peut choisir des attaques ou changer de Pokémon en fonction de la situation.

Stats des Pokémon : Chaque Pokémon possède des caractéristiques (HP, Attaque, Défense, Niveau, etc.) qui influent sur le combat.

Live Components sont utilisés pour mettre à jour l'état du combat en temps réel, sans rechargement de page.

Animations de combat avec des effets visuels (par exemple, impact des attaques, changement de Pokémon).

3. Capture de Pokémon

Description : Le joueur peut capturer des Pokémon sauvages pendant son exploration.

Fonctionnalités associées :

Lorsque le joueur rencontre un Pokémon sauvage, un bouton lui permet de lancer une Pokéball.

Le succès ou l’échec de la capture dépend d’une probabilité calculée en fonction de l’état du Pokémon.

Animations de capture avec des effets visuels montrant la Pokéball en action.

Si le Pokémon est capturé, il est ajouté à l’équipe du joueur.

4. Gestion de l’équipe Pokémon

Description : Le joueur peut gérer son équipe de Pokémon capturés.

Fonctionnalités associées :

Une page d’inventaire pour afficher les Pokémon capturés et leurs caractéristiques.

Le joueur peut choisir un Pokémon actif à utiliser dans les combats ou les remplacer.

Interface d'inventaire permettant de visualiser les objets (Pokéballs, potions, etc.).

5. Système d’inventaire

Description : Le joueur dispose d'un inventaire pour gérer ses objets et ses Pokémon.

Fonctionnalités associées :

Pokéballs : Le joueur peut utiliser des Pokéballs pour capturer des Pokémon.

Objets spéciaux : Potions, objets pour augmenter les stats des Pokémon, etc.

Le joueur peut utiliser les objets pendant les combats ou en dehors, par exemple pour soigner un Pokémon.

6. Système de progression

Description : Le joueur progresse dans l’histoire en accomplissant des défis et en gagnant des combats.

Fonctionnalités associées :

Les Pokémon gagnent des XP après chaque combat, ce qui leur permet de monter de niveau.

Lorsqu’un Pokémon atteint un certain niveau, il évolue en une forme plus puissante.

Des objectifs et missions secondaires peuvent être ajoutés pour rendre le jeu plus dynamique.

Technologies
Backend

Symfony 6.x : Framework PHP pour gérer la logique métier du jeu.

Symfony UX : Utilisation de Turbo et Live Components pour gérer les interactions en temps réel et les mises à jour sans rechargement de page.

Twig : Moteur de templates pour rendre les vues HTML.

Frontend

CSS (SCSS) : Pour le style de l'interface utilisateur.

JavaScript (optionnel) : Pour les animations avancées (par exemple, animations de combat ou de capture). Des librairies comme Anime.js ou GSAP pourraient être utilisées pour cela.

Base de données

Doctrine ORM : Pour la gestion des données du jeu (utilisateurs, Pokémon, inventaire, combats, etc.).

Structure du projet
Répertoires principaux

/src/Controller : Contient les contrôleurs principaux du jeu.

PokemonController.php : Gestion des interactions de la carte, capture, et détails des Pokémon.

CombatController.php : Gestion des combats, attaques, et évolutions.

InventaireController.php : Gestion de l’inventaire et de l’équipe Pokémon.

/templates : Contient les vues Twig.

pokemon/ : Vues pour la carte, l’exploration et les détails des Pokémon.

combat/ : Vues pour les combats (combat en temps réel, animations, résultats).

inventaire/ : Vues pour la gestion de l’inventaire et des objets.

/assets : Contient les fichiers CSS et JavaScript pour le frontend.

/js : Scripts pour l’animation et l'interaction avancée (par exemple, les animations de capture, combats).

/css : Fichiers de style (structure de la carte, des pages de combat, etc.).

Fonctionnalités par page

Page d’accueil :

Vue de la carte de la région avec des liens vers les villes et autres lieux.

Possibilité de démarrer l’aventure et de choisir un Pokémon de départ.

Page de combat :

Affichage des Pokémon en combat, avec des boutons pour choisir les actions (attaque, changer de Pokémon, utiliser un objet).

Mise à jour en temps réel des stats (PV, attaque, etc.) grâce aux Live Components.

Page de capture :

Lorsque le joueur rencontre un Pokémon sauvage, possibilité de lancer une Pokéball avec une animation.

Affichage du résultat de la capture (réussite/échec).

Page d’inventaire :

Affichage des Pokémon capturés et de leur niveau.

Gestion des objets (Pokéballs, potions, objets de guérison).

Page de progression :

Affichage du niveau du joueur, des Pokémon, et de la progression vers la prochaine arène ou défi.

Roadmap
Phase 1 - Mise en place des bases

Installation de Symfony et configuration du projet.

Création des entités de base (Pokémon, Combat, Inventaire).

Mise en place de la carte avec les points d’intérêt (villes, routes).

Phase 2 - Combat et capture

Implémentation du système de combat (tour par tour, choix des attaques).

Création de l’animation de capture de Pokémon avec des probabilités de réussite.

Mise en place du système d’expérience et de montée de niveau des Pokémon.

Phase 3 - Inventaire et gestion des objets

Implémentation de l’inventaire (Pokéballs, potions, objets).

Création de l’interface pour gérer les Pokémon et les objets.

Phase 4 - Système de progression

Mise en place de la progression de l’histoire et des défis.

Intégration des arènes et des combats contre d’autres dresseurs.

Phase 5 - Finitions

Tests et ajustements des animations.

Ajout de l’interface pour gérer les paramètres de jeu et les préférences.