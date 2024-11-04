
# E-sportify

Bienvenue sur E-sportify, un site dédié aux e-sport français développé par Coyen etienne.

## Description

E-sportify est un projet open source qui permet la création et la participation à des événements d'e-sport, favorisant ainsi la rencontre entre passionnés et les discussions autour de l'e-sport.

## Fonctionnalités

- Recherche d'événements par nombre de joueurs, date et pseudo
- Inscriptions aux événements
- Création d'événements
- Et bien plus...


## Documentation

### Font-end :

J'ai utilisé du HTML5 avec TWIG, du CSS ainsi que du JAVASCRIPT ( Utilisation d'AJAX et de JSON).

### Back-end:

J'ai utilisé du PHP avec utilisation PDO (version 8.2) grâce au framework symfony 7. 

Ma base de donnée est une base de donnée relationnel, pour ma part j'ai utilisé MariaDB grâce a PhpMyAdmin via Docker en version 3.8.

### Sécurite :

- Mots de passe hashés
- Politique de mot de passe appliquée
- Protection contre les injections SQL et les failles XSS
- Protection par jetons CSRF activée


## Logo

https://github.com/eco59/app/a_mettre_en_dehors/asset/logoES.png

## Prerequisites

- Un ordinateur avec Windows
- Un IDE de développement
- Composer installé
- Symfony 7
- Docker Desktop installé

## Installation

1. Cloner le dépôt GitHub :

```bash
git clone https://github.com/eco59/app.git


Installer les dépendances :

composer install


- Créez un dossier nommé app et placez-y tous les dossiers sauf le dossier a_mettre_en_dehors.
- Déplacez les dossiers et fichiers du dossier
- a_mettre_en_dehors hors du dossier app pour qu'ils soient dans le même répertoire racine.


Votre base de donnée: esportify:

Table users {
  id int [pk, increment]
  pseudo varchar(180)
  role longtext
  email varchar(255)
  mdp varchar(255)
}

Table favori {
  id int [pk, increment]
  id_users int 
  id_event int 
  isBlocked int
  
}


Table event {
  id int [pk, increment]
  pseudo_id int
  titre varchar(255)
  description longtext
  nombre_de_joueur int
  autoriser tinyint
  date_heure_debut datetime
  date_heure_fin datetime
  status varchar(20)
}



ALTER TABLE favori
ADD CONSTRAINT fk_favori_users
FOREIGN KEY (id_users) REFERENCES users(id)
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE favori
ADD CONSTRAINT fk_favori_event
FOREIGN KEY (id_event) REFERENCES event(id)
ON DELETE CASCADE ON UPDATE CASCADE;



```
## Deployment

- Depuis la racine du projet (qui contient le dossier app), exécutez la commande suivante pour démarrer Docker :

```bash
docker-compose up
```


## Authors

- [@eco59](https://www.github.com/eco59)

