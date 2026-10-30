# Documentation technique de BISIKA et de son API mobile

## 1. Objet

Ce document décrit l’architecture existante et le contrat de l’API mobile implémentée dans ce dépôt. Le backend Laravel et sa base de données restent la source de vérité; l’application mobile ne se connecte jamais directement à la base de données.

L’API couvre deux parcours :

- **Public** : découverte d’établissements, services et produits; réservation d’un service ou d’articles; demande de livraison.
- **Gestionnaire d’établissement** : authentification, consultation du tableau de bord, gestion de base du catalogue, stocks et mouvements, inventaire, ventes POS et traitement des demandes.

Les fonctionnalités d’administration globale (abonnements, utilisateurs, taux et paramètres administratifs) restent accessibles depuis le site web et ne sont pas exposées par l’API mobile.

## 2. Architecture du projet

### Technologies

- PHP 8.2+ et Laravel 12.
- Eloquent ORM et migrations Laravel pour le modèle relationnel.
- Interface web Laravel/Blade avec ressources front construites par Vite.
- Laravel Sanctum pour l’authentification mobile par jeton Bearer.
- DomPDF pour certains documents PDF du site.
- Tests backend Pest/PHPUnit; base de données configurée par environnement.

### Couches

```text
Application mobile
    └── HTTPS + JSON /api/v1
          ├── Routes API et authentification Sanctum
          ├── Contrôleurs Api\V1 et règles/contrôleurs métier partagés
          ├── Middleware d’accès à l’établissement et abonnement
          └── Modèles Eloquent et base relationnelle

Site web Blade
    └── Routes web (sessions, formulaires, vues)
          └── Contrôleurs métier et mêmes modèles/base
```

Le fichier `routes/api.php` définit les routes mobiles. Elles sont chargées par `bootstrap/app.php` et préfixées `/api/v1`. Les routes web existantes restent disponibles et conservent leurs réponses HTML/redirections.

### Modèles principaux

| Domaine | Tables et relations principales |
|---|---|
| Identité | `users`; liaison utilisateur/établissement via `user_etablissements`; jetons Sanctum via `personal_access_tokens` |
| Établissement | `etablissements`, `type_etablissements`, `abonnements`; adresse, coordonnées, statut, contacts et informations publiques |
| Services | `services`, `photos`, `reservations`; un service appartient à un établissement |
| Catalogue | `categories`, `units`, `products`; un produit appartient à un établissement, une catégorie et une unité |
| Stocks | `stocks`, `movements`, `inventories`, `inventory_items`; quantité, seuil, prix/devise, journal et inventaire physique |
| Vente POS | `customers`, `orders`, `order_items`, `taux_de_changes`; commande, articles, TVA, réduction, devise et paiement |
| Demandes publiques | `product_reservations`, `product_reservation_items`, `delivery_orders`, `delivery_order_items` |

Une ligne de stock est unique par couple établissement/produit. Le code et le code-barres des produits sont uniques dans leur établissement. Les migrations suivantes au schéma initial ajoutent notamment TVA, devises/taux de change, paiements et réductions.

## 3. Accès, authentification et format des réponses

### Connexion mobile

`POST /api/v1/auth/login` vérifie l’adresse e-mail et le mot de passe, puis délivre un jeton Sanctum lié au nom de l’appareil. Le jeton expire après 30 jours. Il n’existe pas de route de renouvellement : l’utilisateur doit se reconnecter après expiration. `POST /api/v1/auth/logout` révoque le jeton utilisé.

Toutes les autres routes privées utilisent :

```http
Authorization: Bearer <jeton>
```

L’API impose `Accept: application/json` pour ses propres réponses. Le client devrait toutefois envoyer cet en-tête et `Content-Type: application/json` pour expliciter son intention.

### Autorisations d’établissement

Les routes sous `/api/v1/establishments/{etablissement}` exigent à la fois :

1. un jeton Sanctum valide;
2. un utilisateur de rôle `etablissement`;
3. une association dans `user_etablissements`;
4. un établissement actif disposant d’un abonnement valide.

Un identifiant d’établissement ne confère jamais à lui seul un droit d’accès. Les routes renvoient 404 si l’établissement demandé n’est pas accessible ou actif, et 403 en cas de rôle ou d’abonnement invalide. Les gestionnaires admin/intégrateur ne sont pas autorisés par ce middleware mobile.

### Enveloppes JSON et erreurs

La plupart des lectures/écritures utilisent une enveloppe `data`; les listes ajoutent `meta` :

```json
{
  "data": [],
  "meta": {
    "current_page": 1,
    "per_page": 20,
    "total": 0,
    "last_page": 1
  }
}
```

Les erreurs de validation retournent HTTP 422 et un objet comportant `message` et `errors`; les identifiants de connexion incorrects sont également retournés comme erreur de validation 422. Le stock insuffisant est une validation 422 et n’enregistre pas la vente ou le mouvement. Les erreurs HTTP usuelles sont 401 (jeton absent/invalide), 403 (permission), 404 (ressource ou établissement inaccessible), 409 (créneau déjà confirmé), 422 (validation/règle métier) et 429 (limite de requêtes).

## 4. Référence des endpoints implémentés

Tous les chemins ci-dessous sont relatifs à la base de l’application, avec le préfixe `/api/v1`.

### 4.1 Authentification et profil

| Méthode | Route | Auth | Résultat |
|---|---|---|---|
| `POST` | `/auth/login` | Publique, 5 essais/minute | Délivre un jeton Bearer et le profil utilisateur |
| `POST` | `/auth/logout` | Jeton | Révoque le jeton courant |
| `GET` | `/me` | Jeton | Profil minimal et établissements associés |
| `GET` | `/establishments` | Jeton | Établissements actifs associés à l’utilisateur |

Corps de connexion :

```json
{
  "email": "gestionnaire@example.com",
  "password": "mot-de-passe",
  "device_name": "android-ou-ios"
}
```

Le profil ne retourne ni mot de passe ni jeton après la connexion. Le jeton brut n’est fourni qu’une seule fois dans la réponse de connexion et doit être conservé dans le stockage sécurisé iOS/Android.

### 4.2 Parcours public

| Méthode | Route | Paramètres/corps principaux | Résultat |
|---|---|---|---|
| `GET` | `/public/establishment-types` | — | Types d’établissement disponibles pour filtrer |
| `GET` | `/public/etablissements` | `search`, `type_id`, `per_page` | Établissements actifs paginés |
| `GET` | `/public/etablissements/{id}` | — | Détail, services, photos, promotions actives et publicités actives |
| `GET` | `/public/etablissements/{id}/services` | Pagination (20 par page) | Services disponibles |
| `GET` | `/public/etablissements/{id}/products` | `search`, `category_id`, `per_page` (max. 50) | Produits actifs et disponibilité effective |
| `POST` | `/public/reservations` | `service_id`, `date`, `client_name`, `client_phone?` | Crée une réservation de service |
| `POST` | `/public/product-reservations` | `etablissement_id`, coordonnées client, `items[]`, `message?` | Crée une réservation d’articles |
| `POST` | `/public/delivery-orders` | `etablissement_id`, client, adresse, `payment_plan`, `items[]`, `deposit_amount?` | Crée une demande de livraison |

Exemple de demande d’articles ou de livraison :

```json
{
  "etablissement_id": 12,
  "client_name": "Nom du client",
  "client_phone": "+243000000000",
  "items": [
    { "product_id": 34, "quantity": 2 }
  ]
}
```

Une livraison requiert également `delivery_address` et `payment_plan` égal à `one_time` ou `two_installments`. Pour deux versements, `deposit_amount` est requis, positif et inférieur ou égal au total calculé par le serveur.

Les réservations publiques sont limitées à 20 requêtes/minute par IP. Une réservation de service exige une date au moins égale à aujourd’hui; un créneau déjà confirmé pour ce service renvoie HTTP 409. Les demandes d’articles/livraison ne réduisent pas immédiatement le stock physique, mais engagent les quantités disponibles.

`available_quantity` pour les produits publics est calculé ainsi :

```text
max(0, stock physique
       - quantités de réservations d’articles en attente
       - quantités de livraisons en attente, partiellement payées ou payées)
```

### 4.3 Gestionnaire d’établissement

Toutes les routes de cette section exigent l’authentification et le middleware décrit ci-dessus.

| Méthode | Route | Paramètres principaux | Fonction |
|---|---|---|---|
| `GET` | `/establishments/{id}/dashboard` | — | Indicateurs de stock et 10 mouvements récents |
| `GET` | `/establishments/{id}/categories` | — | Catégories actives (référentiel partagé) |
| `GET` | `/establishments/{id}/units` | — | Unités actives (référentiel partagé) |
| `GET` | `/establishments/{id}/products` | `search`, `status`, `per_page` (max. 100) | Catalogue paginé avec stock |
| `POST` | `/establishments/{id}/products` | catégorie, unité, code, nom, etc. | Création d’un produit sans quantité initiale |
| `PUT`, `PATCH` | `/establishments/{id}/products/{product}` | Champs produit à mettre à jour | Mise à jour limitée à l’établissement |
| `GET` | `/establishments/{id}/stocks` | `filter=low\|out`, `per_page` | Stocks et seuils |
| `GET` | `/establishments/{id}/movements` | `product_id`, `type`, `date_from`, `date_to`, `per_page` | Historique paginé |
| `POST` | `/establishments/{id}/movements` | `product_id`, `type`, `quantity`, prix/devises, `note?` | Entrée, sortie ou ajustement |
| `POST` | `/establishments/{id}/inventories` | `inventory_date`, `items[]`, `notes?` | Inventaire physique et mouvements d’écart |
| `GET` | `/establishments/{id}/sales/products` | `search?` | Recherche POS avec quantités disponibles et taux TVA proposés |
| `POST` | `/establishments/{id}/sales` | Articles, TVA, paiement, devise et réduction | Vente POS et décrément transactionnel du stock |
| `GET` | `/establishments/{id}/sales` | `search`, `date_from`, `date_to`, `per_page` | Historique POS/direct paginé |
| `GET` | `/establishments/{id}/reservations/services` | `status?`, `per_page` | Demandes de rendez-vous |
| `PATCH` | `/establishments/{id}/reservations/services/{reservation}` | `status=confirmé\|rejeté` | Traitement d’un rendez-vous en attente |
| `GET` | `/establishments/{id}/reservations/products` | `status?`, `per_page` | Demandes d’articles |
| `PATCH` | `/establishments/{id}/reservations/products/{id}` | `status=confirmed\|rejected` | Confirme/rejette; confirmation décrémente le stock |
| `GET` | `/establishments/{id}/delivery-orders` | `status?`, `per_page` | Demandes de livraison |
| `PATCH` | `/establishments/{id}/delivery-orders/{id}` | `action` | Paiement et progression de la livraison |

#### Mouvements de stock

- `type=in` : `quantity` est l’entrée à ajouter; `purchase_price` et `purchase_currency` (`CDF`/`USD`) sont requis. `selling_price` et `selling_currency` sont facultatifs. Le coût d’achat moyen pondéré est recalculé.
- `type=out` : `quantity` est la quantité à retirer; la sortie échoue si le stock disponible est insuffisant.
- `type=adjust` : `quantity` est la nouvelle quantité absolue, y compris zéro; `note` est obligatoire. Le type du mouvement enregistré devient `adjust_positive` ou `adjust_negative`.

Les entrées, sorties et ajustements verrouillent l’établissement et le stock concernés dans une transaction. Les entrées en USD utilisent le dernier taux de change USD/CDF défini par le serveur.

#### Inventaires

Corps :

```json
{
  "inventory_date": "2026-10-29",
  "notes": "Inventaire du jour",
  "items": [
    { "product_id": 34, "physical_quantity": 7 }
  ]
}
```

Chaque produit ne peut apparaître qu’une seule fois; il doit déjà posséder un stock dans l’établissement. Le serveur compare les quantités système/physique, enregistre la différence, met à jour le stock et crée un mouvement pour chaque écart. L’inventaire passe à `completed` après validation de toutes les lignes.

#### Vente POS

Exemple de corps :

```json
{
  "items": [
    { "product_id": 34, "quantity": 2, "tva_rate": 16 }
  ],
  "payment_method": "cash",
  "payment_currency": "CDF",
  "discount_type": "amount",
  "discount_value": 0,
  "customer_name": "Client comptoir"
}
```

Valeurs de paiement : `cash`, `card`, `transfer`, `other`; devises : `CDF`, `USD`. Les informations client sont facultatives. Les taux de TVA doivent être transmis pour chaque ligne; la recherche POS propose actuellement `[0, 5.5, 10, 20]`. Le champ optionnel `sold_price_ht` permet de transmettre un prix unitaire HT négocié par le gestionnaire. Les montants finaux et conversions sont recalculés/validés par le serveur. La remise peut être `amount` ou `percentage`; une remise en pourcentage ne dépasse pas 100 % et une remise ne peut excéder le total initial.

Pour une vente en USD, un taux USD/CDF valide doit être configuré. Le résultat contient `order_number`, totaux HT/TVA/TTC, réduction, devise, montant payé et une URL de facture web. Cette URL pointe vers une route web, pas vers une route de facture API dédiée.

Les articles, la commande, les mouvements et la décrémentation du stock sont enregistrés dans une transaction. Le stock est verrouillé et les quantités réservées par les demandes d’articles/livraisons sont exclues de la disponibilité POS.

#### Actions sur les livraisons

`PATCH /establishments/{id}/delivery-orders/{order}` accepte `action` égal à :

- `confirm_payment` : enregistre le paiement initial;
- `confirm_balance` : confirme le solde d’une commande partiellement payée;
- `start_delivery` : exige le paiement complet, vérifie les quantités, décrémente le stock et crée les mouvements;
- `mark_delivered` : termine une livraison déjà en cours.

Une transition invalide renvoie une erreur 422; les opérations sensibles au stock sont transactionnelles.

## 5. Règles métier et conventions

- Rôles présents dans l’application : `client`, `etablissement`, `admin`, `integrateur`. L’API mobile ne donne des fonctions de gestion qu’au rôle `etablissement`.
- Les produits, stocks, clients, commandes, réservations et mouvements sont contextualisés par établissement.
- État de stock : `quantity <= 0` signifie rupture; `0 < quantity <= minimum_stock` signifie stock bas.
- Les écritures créent un journal `movements` avec quantité avant/après, type, auteur et note.
- Les montants de stock normalisés sont exprimés en CDF; la devise et le montant d’origine peuvent aussi être conservés.
- Les statuts historiques mêlent termes anglais et français; l’API utilise les valeurs explicites documentées par endpoint, sans exposer de mécanisme de traduction des statuts.
- La disponibilité affichée est indicative; le backend revalide les quantités au moment de toute création/confirmation sensible.

## 6. Intégration de l’application mobile

Le dépôt ne fixe pas le framework mobile. Flutter ou React Native peuvent consommer la même API. Séparer au minimum :

```text
Écrans/navigation → état et cas d’usage → dépôts → client HTTP/DTO JSON
```

Recommandations :

- Conserver le jeton uniquement dans Keychain/Keystore ou le stockage sécurisé équivalent; l’effacer à la déconnexion.
- Définir la base URL par environnement et utiliser HTTPS en staging/production.
- Ne pas stocker de clé serveur, identifiant DB ou mot de passe dans le binaire.
- Centraliser la gestion des réponses 401/403/409/422/429/5xx et des pertes réseau.
- Afficher les montants, devises, erreurs métier et états de stock fournis par le serveur.
- Le backend ne fournit pas de protocole hors ligne. Ne pas rejouer automatiquement une vente, un mouvement ou une demande après timeout sans vérifier son état.
- Les images retournent des URL accessibles via l’hôte web; leur cache doit être effectué dans l’application.
- Il n’existe pas de route mobile dédiée à l’inscription, au changement/réinitialisation de mot de passe, au suivi public des demandes, à la consultation individuelle d’une facture, aux paiements électroniques ou à la gestion des photos.

## 7. Mise en service et configuration

1. Configurer `.env` avec les paramètres de l’environnement et une `APP_URL` HTTPS publique.
2. Installer les dépendances Composer verrouillées (`composer install`).
3. Appliquer les migrations en production avec sauvegarde préalable et déploiement contrôlé :

   ```powershell
   php artisan migrate --force
   ```

   Cette étape crée notamment `personal_access_tokens` pour Sanctum. Elle doit être exécutée sur l’environnement cible, après vérification des migrations existantes.
4. Vérifier la configuration des abonnements et le taux USD/CDF avant d’activer les parcours concernés.
5. Limiter les origines CORS aux clients autorisés si le client est une application WebView/PWA; une application native n’a pas à contourner la politique de sécurité.
6. Vérifier les routes avec `php artisan route:list --path=api/v1`.

Dans le poste de développement utilisé pour cette analyse, `.env` pointe vers MySQL local, mais le serveur MySQL n’était pas disponible; aucune migration n’a donc été appliquée sur cette base. Les tests applicatifs utilisent SQLite en mémoire.

## 8. Tests

Tests API ajoutés dans `tests/Feature/MobileApiTest.php` :

- connexion, délivrance de jeton et accès au profil;
- entrée de stock, écriture du mouvement et rejet d’une sortie insuffisante;
- refus de l’accès à l’établissement d’un autre utilisateur;
- calcul de la disponibilité publique après réservation et demande de livraison;
- création JSON d’une réservation de service.

Exécution ciblée :

```powershell
php artisan test --filter=MobileApiTest
```

Autres tests recommandés pour les prochaines évolutions : renouvellement/révocation de jetons, transition de statut par rôle, concurrence entre ventes et demandes, paiements fractionnés, prix/TVA, taux de change manquant, clés d’idempotence et quotas en production.

## 9. Limites connues et tâches restantes

- Les écritures de vente/mouvement/réservation n’acceptent pas encore de clé d’idempotence. Une application ne doit pas retenter automatiquement les écritures après un timeout. Ajouter un mécanisme d’idempotence persistant avant d’activer une synchronisation réseau agressive ou des paiements.
- Le jeton Sanctum n’a pas de route de renouvellement; après 30 jours, l’application redemande une connexion.
- Le paiement POS enregistre un moyen et un montant; aucun prestataire de paiement mobile/online n’est intégré.
- L’historique des ventes est paginé, mais aucun endpoint de détail JSON/PDF dédié n’est exposé.
- Le catalogue mobile peut lire/créer/modifier les champs principaux et lire les référentiels catégories/unités, mais pas supprimer les produits, modifier les images ni créer le stock initial. Une entrée de stock séparée est nécessaire; les référentiels restent gérés depuis le web.
- Il n’y a pas d’endpoint API pour le profil public d’un client, ses demandes, une liste de paiements ou une inscription.
- Le middleware de gestion mobile est limité aux comptes établissement avec abonnement actif; il ne couvre pas les administrateurs.
- L’API ne dispose pas encore d’un fichier OpenAPI généré; cette documentation décrit le contrat manuel à synchroniser avec les changements de routes/validations.
- Les routes de rendez-vous et de réservations utilisent certaines règles métier des contrôleurs web partagés; les tests API doivent être maintenus si ces contrôleurs évoluent.

## 10. Fichiers de référence

- Routes API : `routes/api.php`.
- Démarrage Laravel, chargement API et gestion JSON des erreurs : `bootstrap/app.php`.
- Contrôleurs API : `app/Http/Controllers/Api/V1/AuthController.php`, `app/Http/Controllers/Api/V1/MobileController.php`.
- Accès mobile par établissement : `app/Http/Middleware/MobileEstablishmentMiddleware.php`.
- JSON forcé pour l’API : `app/Http/Middleware/ForceJsonResponse.php`.
- Authentification : `app/Models/User.php`, `composer.json`, migration Sanctum dans `database/migrations/`.
- Ventes et stocks réutilisés : `app/Http/Controllers/Client/SaleController.php`, `app/Http/Controllers/ReservationController.php`, `app/Services/PurchasePrice.php`.
- Modèles de données : `app/Models/` et migrations dans `database/migrations/`.
