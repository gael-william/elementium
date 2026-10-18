# Élémentium Studio — plateforme audiovisuelle

Site vitrine + galerie vidéo + espace d'administration d'Élémentium Studio (Laravel 13, PHP 8.3+).
Reconstruit à partir de la sauvegarde navigateur de l'ancien site (`../Élémentium Studio - Création Audiovisuelle Innovante.html`).

## Lancer le site en local

```bash
cd C:\xampp\htdocs\elementium\platform
php artisan serve            # → http://127.0.0.1:8000
```

Avec Apache (XAMPP démarré) : http://localhost/elementium/platform/public

Première installation sur une autre machine :

```bash
composer install
cp .env.example .env         # puis renseigner ADMIN_PASSWORD
php artisan key:generate
php artisan migrate --seed   # crée la base SQLite + le contenu d'origine + le compte admin
```

## Administration

- Adresse : `/admin` (ex. http://127.0.0.1:8000/admin)
- Compte créé par le seeder : `admin@elementiumstudio.com` / `Elementium2026!` (ou les valeurs `ADMIN_EMAIL` / `ADMIN_PASSWORD` du `.env`).
  **Changez ce mot de passe avant toute mise en ligne** (modifiez `ADMIN_PASSWORD` puis relancez `php artisan db:seed`).

Depuis l'admin on gère :

| Rubrique      | Ce qu'on peut faire |
|---------------|---------------------|
| Vidéos        | Ajouter un lien YouTube / Vimeo / MP4 ou envoyer un fichier, miniature, durée, mise en avant, publier / dépublier. Les vidéos proposées par les visiteurs arrivent « En attente ». |
| Réalisations  | Images du portfolio, catégorie (animation, illustration, audio, gaming), ordre, lien externe. |
| Équipe        | Membres, photo, rôle, couleur d'élément (Feu / Eau / Terre), réseaux sociaux. |
| Messages      | Demandes du formulaire de contact (lecture, réponse par e-mail, suppression). |
| Newsletter    | Liste des abonnés, export CSV. |

Deux vidéos de l'ancien site (« Dans l'ombre des plumes » et « RCJG ») ont perdu leur lien YouTube : elles sont en brouillon, il suffit de coller le lien puis de cocher « Publiée ».

## Où modifier quoi

- Coordonnées, téléphones, réseaux sociaux, carte, liste des services : `config/studio.php`
- Page d'accueil : `resources/views/home.blade.php`
- Styles : `public/css/site.css` (couleurs Feu / Eau / Terre en haut du fichier)
- Animations (défilement fluide, GSAP, curseur, filtres, lecteur…) : `public/js/site.js`
- Sphère 3D du hero (Three.js) : `public/js/hero3d.js`
- Admin : `app/Http/Controllers/Admin/*`, `resources/views/admin/*`

Les fichiers envoyés depuis l'admin sont stockés dans `public/uploads/`.

## Avant la mise en ligne

1. `.env` : `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://elementiumstudio.com`.
2. E-mails : le formulaire de contact enregistre toujours le message en base ; pour recevoir aussi un e-mail, configurez `MAIL_MAILER=smtp` et les `MAIL_*` (ex. un mot de passe d'application Gmail).
3. Envoi de vidéos : PHP limite par défaut les envois à 2 Mo. Dans `php.ini`, augmentez `upload_max_filesize` et `post_max_size` (ex. `256M`) pour accepter des fichiers vidéo.
4. Le serveur web doit pointer sur le dossier `public/`.
