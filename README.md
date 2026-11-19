# Gestion d'une clinique médicale

## 1. Présentation du projet

**Gestion d'une clinique médicale** est une application web développée dans le cadre d'un projet universitaire. Elle permet de gérer les principales activités d'une clinique à travers une interface centralisée : les patients, les consultations médicales et les utilisateurs.

L'application est développée en **PHP natif**, sans framework, et utilise **MySQL** pour le stockage des données.

## 2. Objectifs du projet

Les objectifs principaux sont les suivants :

- Centraliser les informations des patients.
- Faciliter la gestion des consultations médicales.
- Organiser les comptes des utilisateurs.
- Mettre en place une authentification sécurisée.
- Gérer les droits d'accès selon le rôle de chaque utilisateur.
- Assurer la cohérence des données grâce aux relations entre les tables.

## 3. Technologies utilisées

| Technologie | Rôle dans le projet |
|---|---|
| PHP natif | Traitement des requêtes et logique de l'application |
| HTML5 | Structure des pages web |
| CSS3 | Présentation et mise en forme de l'interface |
| JavaScript natif | Interactions côté client |
| MySQL | Stockage et gestion des données |
| PDO | Connexion à MySQL et exécution des requêtes |
| Sessions PHP | Authentification et gestion des rôles |

Le projet n'utilise pas de framework tel que Laravel, Bootstrap ou React.

## 4. Fonctionnalités principales

### 4.1. Authentification

L'application propose les fonctionnalités suivantes :

- Inscription d'un nouvel utilisateur.
- Connexion à l'application.
- Déconnexion.
- Gestion des sessions PHP.
- Contrôle de l'accès aux pages selon le rôle de l'utilisateur.

L'inscription publique crée un compte ayant le rôle **Assistante**. La création d'un compte **Médecin** est réservée à un médecin déjà autorisé.

### 4.2. Gestion des patients

Le module des patients permet de :

- Ajouter un patient.
- Consulter la liste des patients.
- Modifier les informations d'un patient.
- Supprimer un patient, sous réserve des contraintes de la base de données.

Les informations d'un patient comprennent notamment son nom, son prénom, sa date de naissance, son numéro de téléphone et son adresse.

### 4.3. Gestion des consultations

Le module des consultations permet de :

- Enregistrer une consultation.
- Sélectionner le patient concerné.
- Associer la consultation au médecin responsable.
- Consulter la liste des consultations.
- Modifier les informations d'une consultation.
- Supprimer une consultation.

Les informations d'une consultation comprennent la date, le motif et le diagnostic. La liste des consultations affiche également le patient et le médecin associés.

### 4.4. Gestion des utilisateurs

Le module des utilisateurs permet au médecin de :

- Consulter la liste des utilisateurs.
- Ajouter un utilisateur.
- Modifier un utilisateur.
- Supprimer un utilisateur, sous réserve des contraintes définies dans l'application.

### 4.5. Tableau de bord

Le tableau de bord constitue la page d'accueil de l'application après authentification. Il présente des statistiques ainsi que les dernières consultations afin de donner une vue générale de l'activité de la clinique.

## 5. Gestion des rôles et des permissions

L'application distingue deux rôles principaux :

| Fonctionnalité | Assistante | Médecin |
|---|:---:|:---:|
| Consulter les patients | Oui | Oui |
| Ajouter des patients | Oui | Non |
| Modifier des patients | Oui | Non |
| Supprimer des patients | Oui | Non |
| Consulter les consultations | Oui | Oui |
| Ajouter des consultations | Non | Oui |
| Modifier des consultations | Non | Oui |
| Supprimer des consultations | Non | Oui |
| Gérer les utilisateurs | Non | Oui |

Un utilisateur non connecté peut uniquement accéder aux pages publiques d'inscription et de connexion.

Les permissions sont vérifiées côté serveur afin de protéger les pages et les opérations selon le rôle de l'utilisateur.

## 6. Structure de la base de données

La base de données utilisée par l'application s'appelle **`gestion_clinique`**. Elle repose sur trois tables principales.

### 6.1. Table `utilisateurs`

Cette table contient les informations des comptes :

- `id` : identifiant de l'utilisateur.
- `nom` : nom.
- `prenom` : prénom.
- `email` : adresse électronique unique.
- `mot_de_passe` : mot de passe haché.
- `role` : rôle de l'utilisateur (assistante ou médecin).

### 6.2. Table `patients`

Cette table contient les informations personnelles des patients :

- `id` : identifiant du patient.
- `nom` : nom.
- `prenom` : prénom.
- `date_naissance` : date de naissance.
- `telephone` : numéro de téléphone.
- `adresse` : adresse.

### 6.3. Table `consultations`

Cette table contient les informations des consultations :

- `id` : identifiant de la consultation.
- `date_consultation` : date de la consultation.
- `motif` : motif de la consultation.
- `diagnostic` : diagnostic établi par le médecin.
- `patient_id` : référence au patient concerné.
- `medecin_id` : référence au médecin responsable.

### 6.4. Relations entre les tables

Les relations principales sont les suivantes :

- `consultations.patient_id` référence `patients.id`.
- `consultations.medecin_id` référence `utilisateurs.id`.

Ainsi, un patient peut être associé à plusieurs consultations et un médecin peut être associé à plusieurs consultations.

Les clés étrangères permettent de préserver l'intégrité référentielle des données. Par conséquent, un patient ou un médecin associé à des consultations ne peut pas être supprimé directement.

## 7. Organisation du projet

Le code est organisé en plusieurs dossiers afin de séparer les différentes responsabilités de l'application.

```text
gestion_clinique/
│
├── config/
│   └── database.php
│
├── includes/
│   ├── header.php
│   ├── navbar.php
│   ├── footer.php
│   ├── auth.php
│   └── functions.php
│
├── auth/
│   └── Gestion de l'inscription, connexion et déconnexion
│
├── patients/
│   └── Opérations de gestion des patients
│
├── consultations/
│   └── Opérations de gestion des consultations
│
├── utilisateurs/
│   └── Opérations de gestion des utilisateurs
│
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── script.js
│   └── images/
│
├── index.php
├── accueil.php
├── database.sql
└── README.md
```

### Rôle des principaux dossiers et fichiers

- **`config/`** : contient la configuration de la connexion à la base de données et de la session.
- **`includes/`** : regroupe les composants communs, les vérifications d'authentification et les fonctions utilitaires.
- **`auth/`** : contient les fonctionnalités d'inscription, de connexion et de déconnexion.
- **`patients/`** : regroupe les opérations liées aux patients.
- **`consultations/`** : regroupe les opérations liées aux consultations.
- **`utilisateurs/`** : regroupe les opérations de gestion des utilisateurs.
- **`assets/`** : contient les ressources CSS, JavaScript et les images.
- **`index.php`** : assure la redirection de l'utilisateur selon son état de connexion.
- **`accueil.php`** : affiche le tableau de bord.
- **`database.sql`** : contient la structure de la base de données et des données de test.

## 8. Organisation du fonctionnement

Le fonctionnement général de l'application peut être résumé en plusieurs étapes :

1. L'utilisateur accède à l'application.
2. Il s'authentifie à l'aide de son adresse e-mail et de son mot de passe.
3. L'application vérifie les identifiants et initialise une session.
4. Le rôle de l'utilisateur détermine les pages et les opérations auxquelles il peut accéder.
5. L'utilisateur effectue une opération sur les patients, les consultations ou les utilisateurs, selon ses permissions.
6. PHP traite la demande et communique avec MySQL à l'aide de PDO.
7. Le résultat de l'opération est affiché à l'utilisateur.

## 9. Sécurité et validation des données

Plusieurs mécanismes sont utilisés pour renforcer la sécurité de l'application :

- **`password_hash()`** : hachage des mots de passe avant leur enregistrement.
- **`password_verify()`** : vérification du mot de passe lors de la connexion.
- **PDO et requêtes préparées** : limitation des risques d'injection SQL.
- **`htmlspecialchars()`** : protection de l'affichage des données contre les injections HTML.
- **Jeton CSRF** : vérification de l'origine des requêtes sensibles.
- **Sessions PHP** : maintien de l'état de connexion de l'utilisateur.
- **Contrôle des rôles** : restriction de l'accès aux fonctionnalités selon les permissions.
- **Validation des formulaires** : vérification des données avant leur traitement.
- **Requêtes POST pour les suppressions** : les opérations de suppression ne sont pas déclenchées par un simple lien GET.

## 10. Contraintes et règles de gestion

L'application applique certaines règles afin de préserver la cohérence des données :

- Un patient associé à des consultations ne peut pas être supprimé.
- Un médecin associé à des consultations ne peut pas être supprimé.
- Un utilisateur ne peut pas supprimer son propre compte.

Ces règles évitent de supprimer des enregistrements encore référencés par d'autres données.

## 11. Conclusion

Le projet **Gestion d'une Clinique Médicale** met en pratique les notions fondamentales du développement web avec PHP natif et MySQL. Il regroupe la gestion des patients, des consultations et des utilisateurs dans une même application, tout en intégrant l'authentification, la gestion des sessions, les permissions et plusieurs mécanismes de sécurité.

Son organisation modulaire facilite la compréhension du code, sa maintenance et son évolution.
