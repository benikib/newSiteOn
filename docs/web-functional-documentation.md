# Guide fonctionnel du site web BISIKA

## 1. Objet et périmètre

Ce guide décrit les parcours disponibles dans l’application web BISIKA : comptes, établissements, organisations patronales, cotisations, publications, publicités et services. Les données sont gérées par l’application Laravel et stockées dans la base de données du projet.

L’API mobile est documentée séparément dans [la documentation technique mobile](./mobile-technical-documentation.md). Les fonctions Organisation patronale, validation des comptes, identification légale et affiliation décrites ici sont des fonctions web.

## 2. Profils et accès

| Profil | Fonctions principales |
|---|---|
| Administrateur (`admin`) | Gérer les comptes et les établissements, valider ou désactiver les comptes, accorder le droit de publication, gérer les publications, les publicités et les référentiels du site. |
| Gestionnaire d’établissement (`etablissement`) | Gérer les établissements qui lui sont associés, leur identification, leurs services, leur galerie et leurs publicités. L’accès aux fonctions de gestion dépend également des règles d’abonnement existantes. |
| Organisation patronale (`organisation_patronale`) | Gérer les demandes d’affiliation, les membres, les appels et paiements de cotisations et les publications. |
| Intégrateur (`integrateur`) | Accéder aux fonctions prévues par son rôle pour créer et gérer des établissements. |
| Client (`client`) | Utiliser les fonctions publiques du site auxquelles son compte donne accès et échanger avec les autres comptes. |

Les contrôles d’accès sont appliqués par les routes et les middleware. Un gestionnaire d’établissement ne peut gérer que les établissements liés à son compte.

Tout compte approuvé peut également échanger des messages privés avec un autre compte approuvé depuis la messagerie (`/messages`). Les conversations sont accessibles uniquement à leurs participants.

## 3. Comptes utilisateurs

### 3.1 Inscription et validation

1. Une personne choisit un compte client, une entité formalisée, une entité non formalisée ou une organisation patronale depuis `/register`.
2. Pour une entité, l’inscription crée aussi sa fiche et l’association avec son gestionnaire. Pour une organisation patronale, elle crée son profil.
3. Le système attribue à chaque compte un identifiant BISIKA. La connexion reste basée sur l’adresse e-mail et le mot de passe choisis lors de l’inscription.
4. Le nouveau compte reçoit le statut « en attente » et n’est pas connecté automatiquement.
5. Tant que le compte n’est pas approuvé, la connexion est refusée et le site indique qu’une validation administrative est nécessaire.
6. Un administrateur ouvre la gestion des utilisateurs (`/users`) et valide le compte.
7. Après approbation, l’utilisateur peut se connecter. L’administrateur peut aussi désactiver un compte ou accorder l’autorisation de publier.

Les comptes créés par un administrateur depuis l’interface de gestion sont approuvés à leur création. La création d’un compte portant le rôle Organisation patronale initialise aussi son profil organisation, utilisé dans l’espace patronal.

Un avis de complétion apparaît dans l’espace de chaque compte lorsque des coordonnées, informations d’établissement ou identifiants légaux sont absents. L’identifiant BISIKA est affiché dans le profil et l’administration.

## 4. Établissements

### 4.1 Informations de l’établissement

La fiche d’un établissement rassemble son nom, son type ou domaine d’activité, sa description, son téléphone et son adresse (ville, commune, quartier, avenue et numéro). Les types d’établissement disponibles sont gérés dans l’administration et servent aussi au ciblage des publications.

### 4.2 Identification et affiliation

Le gestionnaire associé peut compléter la section d’identification de sa fiche avec :

- la nature de l’entité : `formalisée` ou `clandestine` ;
- le RCCM, l’Id Nat, le NIF, les affiliations CNSS et INPP ;
- le fichier d’autorisation d’ouverture, facultatif (PDF, JPG ou PNG, jusqu’à 10 Mo) ;
- une organisation patronale auprès de laquelle demander l’affiliation.

Une nouvelle demande d’affiliation prend le statut `en_attente`. L’organisation choisie peut l’approuver ou la refuser. Les états d’affiliation sont `aucune`, `en_attente`, `approuvee` et `refusee`. Le gestionnaire peut modifier ses données légales et sa demande, mais la décision de statut appartient à l’organisation.

### 4.3 Services et galerie

Un service peut contenir un nom, une description, un commentaire, un prix facultatif et son état de disponibilité. Le gestionnaire peut modifier ou supprimer les services de son établissement.

La galerie associée à un service permet d’ajouter des photos (une image par envoi, maximum 5 Mo), de leur donner un titre et de supprimer les photos existantes.

## 5. Espace Organisation patronale

Après connexion avec un compte approuvé de rôle `organisation_patronale`, l’espace de travail est accessible à `/organisation/dashboard`.

L’organisation peut y modifier son profil public : nom, téléphone, e-mail de contact et adresse. Ces informations sont distinctes des identifiants de connexion du compte utilisateur ; ceux-ci sont gérés dans le profil utilisateur ou par un administrateur depuis `/users`.

### 5.1 Gestion des membres

Le tableau de bord liste les établissements affiliés et leur type d’activité. Pour chaque demande en attente, l’organisation peut :

- approuver l’affiliation, qui devient `approuvee` ;
- refuser la demande, qui devient `refusee`.

Une affiliation approuvée peut être retirée. L’établissement n’est alors plus rattaché à l’organisation et son statut redevient `aucune`.

### 5.2 Appels et paiements de cotisations

L’organisation peut créer un appel de cotisation pour un établissement dont l’affiliation est approuvée. Un appel contient un intitulé, une description facultative, un montant positif, une devise (`CDF` ou `USD`) et une date d’échéance.

Les paiements sont saisis dans l’espace de suivi avec leur montant, date et, facultativement, mode, référence et notes. Plusieurs paiements partiels peuvent être enregistrés. Le cumul payé ne peut pas dépasser le montant de l’appel. Le statut de la cotisation devient :

- `impayee` à la création ;
- `partielle` lorsqu’un paiement ne couvre pas encore le total ;
- `payee` lorsque le total est atteint.

Le tableau de bord conserve l’historique des paiements associés à chaque appel.

### 5.3 Publications et fil d’actualité

Les organisations patronales et les administrateurs peuvent publier. L’administrateur peut accorder le droit de publication aux autres comptes depuis la gestion des utilisateurs. Une publication comprend un titre, un synopsis facultatif, un contenu, une catégorie (`Vulgarisation`, `Rappel` ou `News`) et une cible :

- `tous` : publication affichée sans restriction de domaine ;
- `domaines` : publication affichée aux domaines d’activité sélectionnés (types d’établissement).

Une publication peut aussi être limitée à une ville et, facultativement, une commune. Les dates de publication et d’expiration peuvent être définies. Une publication n’est affichée dans le fil que lorsque sa date de publication est atteinte, que sa date d’expiration n’est pas dépassée et que les filtres choisis correspondent à son audience.

Le fil public est accessible à `/actualites`. Les filtres `type_id`, `categorie`, `ville` et `commune` permettent de filtrer par domaine et emplacement. Les publications générales restent visibles avec ces filtres. Les comptes approuvés concernés reçoivent une notification enregistrée dans leur espace Notifications. Les publications programmées sont notifiées par la tâche planifiée `notifications:publish-due`; l’ordonnanceur Laravel doit être exécuté en continu en développement (`php artisan schedule:work`) ou appelé par cron en production.

## 6. Publicités

Les publicités peuvent être créées et gérées depuis l’administration ou par le gestionnaire de l’établissement concerné. Une publicité comporte un titre, une description/synopsis facultative, une date, une durée en jours, un statut (`active`, `inactive` ou `planned`), un média et un mode de diffusion : généralisé ou ciblé sur une ville et, facultativement, une commune. Le ciblage géographique est géré par l’administration. La page d’accueil affiche les publicités généralisées et celles correspondant aux filtres d’emplacement fournis.

Les médias acceptés sont les images JPG/JPEG, PNG, GIF, WebP et les vidéos MP4, WebM ou MOV, jusqu’à 50 Mo. Le média peut être remplacé lors de la modification ; sa suppression supprime également le fichier associé. Les publicités actives dont la durée n’est pas expirée peuvent être affichées sur le site.

## 7. Principales routes web

Les routes ci-dessous sont relatives à la racine du site. Les actions de modification nécessitent une session authentifiée et les permissions du rôle concerné.

| Usage | Route | Accès |
|---|---|---|
| Inscription | `GET /register`, `POST /register` | Public |
| Gestion des utilisateurs | `/users` et actions associées | Administrateur |
| Approbation/désactivation d’un compte | `PATCH /users/{user}/approval` | Administrateur |
| Autorisation de publication | `PATCH /users/{user}/publishing` | Administrateur |
| Fil d’actualité | `GET /actualites` | Public |
| Créer/gérer une publication | `GET /publications/create`, `GET /publications/manage`, `POST /publications` | Organisation patronale, administrateur ou compte autorisé |
| Lire ses notifications | `GET /notifications` | Compte authentifié |
| Messagerie et recherche de comptes | `GET /messages?q=nom` | Compte approuvé |
| Envoi d’un message privé | `POST /messages/{user}` | Compte approuvé |
| Espace Organisation patronale | `GET /organisation/dashboard` | Organisation patronale |
| Modification du profil de l’organisation | `PATCH /organisation/profil` | Organisation patronale |
| Décision sur une affiliation | `PATCH /organisation/membres/{etablissement}/affiliation` | Organisation patronale |
| Retrait d’un membre | `DELETE /organisation/membres/{etablissement}` | Organisation patronale |
| Création d’un appel de cotisation | `POST /organisation/cotisations` | Organisation patronale |
| Enregistrement d’un paiement | `POST /organisation/cotisations/{cotisation}/paiements` | Organisation patronale |
| Création d’une publication | `POST /organisation/publications` | Organisation patronale |
| Mise à jour identification/affiliation | `PUT /etablissement/{etablissement}/identification` | Gestionnaire associé |
| Gestion des services et de leurs photos | Routes `etablissement/service/...` | Administrateur ou gestionnaire autorisé |
| Gestion des publicités | Routes `etablissement/publicite/...` | Administrateur ou gestionnaire autorisé |

La liste complète et les noms de routes de l’installation peuvent être consultés avec `php artisan route:list`.

## 8. Modèle de données ajouté

Les fonctionnalités organisationnelles reposent principalement sur les tables suivantes :

| Table | Contenu |
|---|---|
| `users.is_approved` | État d’approbation du compte. |
| `users.identifiant` | Identifiant BISIKA automatiquement attribué. |
| `users.can_publish` | Autorisation de publier accordée par l’administrateur. |
| `organisations_patronales` | Profil lié au compte utilisateur de l’organisation. |
| `etablissements` | Nature, identifiants légaux, autorisation et affiliation patronale. |
| `cotisations` | Appels de cotisation adressés à un établissement membre. |
| `paiements_cotisations` | Paiements enregistrés pour une cotisation. |
| `publications` | Informations, catégories, auteurs et ciblage de domaine/emplacement. |
| `publication_type_etablissement` | Domaines ciblés par une publication. |
| `notifications` | Notifications BISIKA reçues par les comptes concernés. |
| `publicites.ciblage` | Mode de diffusion et emplacement éventuel d’une publicité. |
| `publications.notifications_envoyees` | Empêche l’envoi multiple des notifications à l’arrivée d’une publication programmée. |
| `messages` | Messages privés entre deux comptes, avec date de lecture. |
| `services.commentaire` | Commentaire associé à un service. |

Les migrations `2026_11_02_000001_add_bisika_organization_features.php`, `2026_11_04_000001_create_messages_table.php` et `2026_11_04_000002_add_remaining_bisika_features.php` ajoutent ces schémas. Les modifications de base doivent être appliquées avec les migrations Laravel du projet.

## 9. Mise en route et vérification

Les prérequis, commandes d’installation et commandes de test sont décrits dans le [README](../README.md). Après configuration de la base et de l’environnement Laravel, appliquer les migrations :

```powershell
php artisan migrate
php artisan storage:link
```

La suite de tests peut être exécutée avec :

```powershell
php artisan test
```

Les parcours d’approbation, d’affiliation, de cotisations, de publications, de publicités et de services sont couverts notamment par `tests/Feature/OrganizationWebFeaturesTest.php`.
