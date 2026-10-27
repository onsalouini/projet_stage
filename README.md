<p align="center">
  <img src="assets/banner.svg" alt="RH Platform" width="100%">
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.1%2B-777BB4?logo=php&logoColor=white" alt="PHP 8.1+">
  <img src="https://img.shields.io/badge/Symfony-6.4-000000?logo=symfony&logoColor=white" alt="Symfony 6.4">
  <img src="https://img.shields.io/badge/MySQL-8-4479A1?logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Auth-JWT-22C55E" alt="JWT">
</p>

# RH Platform

Plateforme de gestion des ressources humaines réalisée dans le cadre d'un stage. Le backend est une API REST Symfony sécurisée par JWT qui gère les employés, les départements, les projets et le suivi des présences.

## Fonctionnalités

| Module | Ce qu'il permet |
| --- | --- |
| **Authentification** | Inscription et connexion par email / mot de passe, jeton JWT. Un email contenant `admin` crée un compte administrateur. |
| **Employés** | Liste, création, modification et suppression, avec rattachement à un département. |
| **Départements** | CRUD complet des départements de l'entreprise. |
| **Projets** | Création et suivi de projets (dates de début et de fin) et affectation des employés. |
| **Présences** | Pointage (arrivée, départ, statut), historique et statistiques par jour. |
| **Profil** | Consultation et mise à jour du profil de l'utilisateur connecté. |
| **Utilisateurs** | Gestion des comptes par l'administrateur. |

## Structure

```
projet_stage/
├── assets/                  # logo et bannière du projet
├── rh-platform-backend/     # API Symfony 6.4
│   ├── src/Controller/Api/  # endpoints REST
│   ├── src/Entity/          # User, Employe, Departement, Projet, Presence, Profile
│   ├── src/Security/        # authentificateur de connexion
│   └── migrations/
└── rh-platform-frontend/    # interface (voir la note ci-dessous)
```

## Lancer le projet

Prérequis : PHP 8.1+, Composer et MySQL (ou MariaDB).

```bash
cd rh-platform-backend

# 1. Dépendances
composer install

# 2. Base de données : adapter DATABASE_URL dans .env.local si besoin
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force

# 3. Clés JWT
php bin/console lexik:jwt:generate-keypair

# 4. Serveur
symfony server:start          # ou : php -S 127.0.0.1:8000 -t public
```

L'API est alors disponible sur `http://127.0.0.1:8000/api`.

## Endpoints principaux

| Méthode | Route | Description |
| --- | --- | --- |
| `POST` | `/api/register` | Créer un compte |
| `POST` | `/api/login` | Se connecter, renvoie le jeton JWT |
| `GET` `POST` | `/api/profile` | Lire / modifier son profil |
| `GET` `POST` `PUT` `DELETE` | `/api/employes` | Gestion des employés |
| `GET` `POST` `PUT` `DELETE` | `/api/departements` | Gestion des départements |
| `GET` `POST` `PUT` `DELETE` | `/api/projets` | Gestion des projets |
| `POST` | `/api/presence` | Enregistrer une présence |
| `GET` | `/api/presences` | Historique des présences |
| `GET` | `/api/presences/stats` | Statistiques de présence par jour |
| `GET` `POST` `PUT` `DELETE` | `/api/users` | Gestion des utilisateurs |

Toutes les routes sauf `/api/register` et `/api/login` attendent l'en-tête `Authorization: Bearer <token>`.

Exemple :

```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@rh.tn","password":"Test1234"}'
```

## Note sur le frontend

Le dossier `rh-platform-frontend` a été enregistré comme référence vers un autre dépôt git, son code n'est donc pas inclus ici. Pour l'ajouter :

```bash
git rm --cached rh-platform-frontend
rm -rf rh-platform-frontend/.git      # dans la copie locale qui contient le code
git add rh-platform-frontend
git commit -m "Ajout du code du frontend"
git push
```

## Auteure

**Ons Alouini** — [github.com/onsalouini](https://github.com/onsalouini)
