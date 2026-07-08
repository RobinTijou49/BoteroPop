# BoteroPop — Back Office

Back Office d'administration pour une galerie d'œuvres et la gestion d'évènements,
développé avec **Laravel 12** et **Bootstrap 5**.

## Fonctionnalités

- **Dashboard** : compteurs (œuvres, réservations, évènements, inscriptions), dernières réservations, prochains évènements.
- **Œuvres** (`bp_image`, `bp_image_location`, `bp_tags`, `bp_image_tags`) : CRUD complet, upload de photo (jpg/jpeg/png/webp, 10 Mo max, stockée dans `storage/app/public/oeuvres`), tags, recherche + pagination, **géolocalisation automatique** de l'adresse via l'API OpenStreetMap Nominatim (coordonnées enregistrées dans `bp_image_location`).
- **Évènements** (`bp_event`) : CRUD complet, nombre d'inscrits, consultation détaillée.
- **Inscriptions** (`bp_inscription`) : liste, filtre par évènement, recherche par nom, tri par date.
- **Réservations** (`bp_reservation`) : suivi des réservations d'œuvres avec statut (en attente / confirmée / annulée).
- **Tags** : CRUD complet, association aux œuvres.
- **Utilisateurs** : liste, ajout, modification, réinitialisation du mot de passe, désactivation, suppression (protections : impossible de désactiver/supprimer son propre compte ou le dernier administrateur actif).
- **Paramètres** : profil et mot de passe de l'administrateur connecté.

## Sécurité

- Back office entièrement privé : middleware `auth` sur toutes les routes, redirection vers `/login`.
- Aucune inscription publique (routes register supprimées).
- Protection CSRF sur tous les formulaires, validation via Form Requests.
- Upload limité aux formats jpg, jpeg, png, webp — 10 Mo maximum.
- Mots de passe hashés (bcrypt), comptes désactivables (un compte inactif ne peut pas se connecter).

## Installation

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

php artisan migrate --seed
php artisan storage:link

npm run build        # ou npm run dev pendant le développement
php artisan serve
```

## Compte administrateur par défaut

Créé automatiquement par `Database\Seeders\AdminUserSeeder` :

- **Email** : `admin@monsite.fr`
- **Mot de passe** : `Admin123!`

> Changez ce mot de passe dès la première connexion en production (Paramètres → Changer mon mot de passe).

En environnement `local`, `DemoDataSeeder` ajoute des données de démonstration (œuvres, tags, évènements, inscriptions, réservations).

## Architecture

| Couche | Emplacement |
| --- | --- |
| Contrôleurs admin | `app/Http/Controllers/Admin/` |
| Form Requests | `app/Http/Requests/` |
| Services | `app/Services/` (`GeocodingService`, `ImageService`, `ImageUploadService`, `UserService`) |
| Modèles Eloquent | `app/Models/` (`Image`, `ImageLocation`, `Tag`, `Event`, `Inscription`, `Reservation`, `User`) |
| Vues Blade | `resources/views/admin/`, layout `resources/views/layouts/admin.blade.php` |
| Migrations | `database/migrations/2026_07_07_*` |

### Géocodage Nominatim

`App\Services\GeocodingService` appelle `https://nominatim.openstreetmap.org/search`
avec un `User-Agent` dédié (exigé par la politique d'utilisation de Nominatim),
configurable via `.env` :

```
NOMINATIM_URL=https://nominatim.openstreetmap.org
NOMINATIM_USER_AGENT="BoteroPop-BackOffice/1.0 (admin@monsite.fr)"
```

Un échec de géocodage (adresse introuvable, API indisponible) n'empêche jamais
l'enregistrement de l'œuvre : l'erreur est journalisée et les coordonnées restent vides.

## Tests

```bash
php artisan test
```

38 tests couvrent l'authentification (dont comptes désactivés et absence d'inscription publique),
la protection des routes admin, le CRUD des œuvres (upload, géocodage simulé, suppression en cascade),
les évènements/inscriptions (filtres, tri), les tags et la gestion des utilisateurs.
