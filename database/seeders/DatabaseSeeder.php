<?php

namespace Database\Seeders;

use App\Models\PortfolioItem;
use App\Models\TeamMember;
use App\Models\User;
use App\Models\Video;
use Illuminate\Database\Seeder;

/**
 * Contenu récupéré de l'ancienne version du site (elementiumstudio.com).
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@elementiumstudio.com')],
            [
                'name' => 'Administrateur',
                'password' => env('ADMIN_PASSWORD', 'Elementium2026!'),
                'is_admin' => true,
            ],
        );

        $this->seedTeam();
        $this->seedPortfolio();
        $this->seedVideos();
    }

    private function seedTeam(): void
    {
        $members = [
            ['Jamal ABOUL OLA', 'Directeur général & animateur 2D', 'Animation 2D, illustration, coordination des relations internes et externes.', 'jamal', 'feu'],
            ['Armel POUYA', 'Réalisateur & monteur', 'Conçoit et finalise les productions audiovisuelles.', 'armel', 'eau'],
            ['Abdallah ABOUL OLA', 'Architecture & design d’intérieur', 'Suivi, conseil — artiste modéliste 3D et graphiste.', 'aboudhi', 'terre'],
            ['Gaël TINDANO', 'Développeur web & applications', 'Développeur web et mobile, programmeur Unity / Unreal Engine spécialisé dans les expériences interactives immersives.', 'gael', 'eau'],
            ['Nelson', 'Ingénieur du son', 'Expert en production audio, sound design, sonorisation et acoustique architecturale.', 'nelson', 'feu'],
            ['Stéphanie ZIDA', 'Créatrice multiple', 'Créatrice aux multiples talents, plus de 10 ans d’expérience de graphiste en agence de communication.', 'dame', 'terre'],
        ];

        foreach ($members as $i => [$name, $role, $bio, $photo, $element]) {
            TeamMember::updateOrCreate(['name' => $name], [
                'role' => $role,
                'bio' => $bio,
                'photo' => "images/team/{$photo}.webp",
                'element' => $element,
                'socials' => [],
                'position' => $i,
            ]);
        }
    }

    private function seedPortfolio(): void
    {
        $items = [
            ['Birdie', 'Animation 3D — personnage', 'Création et animation d’un personnage 3D.', 'animation', 'birdie'],
            ['Caïman', 'Illustration concept art', 'Design d’environnement fantastique.', 'illustration', 'caiman'],
            ['Les Poissons', 'Jeu mobile — aventure', 'Développement complet d’un jeu mobile.', 'gaming', 'poissons'],
            ['Sound design — Film', 'Production audio', 'Création de l’ambiance sonore complète d’un film.', 'audio', 'sound-design'],
            ['Motion design — Logo', 'Animation de marque', 'Animation de logo pour une marque.', 'animation', 'motion-logo'],
            ['Illustration marketing', 'Campagne publicitaire', 'Campagne publicitaire illustrée.', 'illustration', 'illustration-marketing'],
            ['Jeu PC — Stratégie', 'Développement de jeu', 'Développement d’un jeu de stratégie.', 'gaming', 'jeu-strategie'],
            ['Forêt africaine', 'Composition musicale', 'Bande originale pour un documentaire.', 'audio', 'foret'],
            ['Vue du ciel', 'Animation 2D — publicité', 'Spot publicitaire animé.', 'animation', 'route-ciel'],
            ['Savane', 'Character design', 'Création de personnages originaux.', 'illustration', 'savane'],
            ['Jeu VR — Immersif', 'Réalité virtuelle', 'Expérience de réalité virtuelle.', 'gaming', 'jeu-vr'],
            ['Voix off — Commercial', 'Production audio', 'Enregistrement et post-production.', 'audio', 'voix-off'],
            ['Studio d’enregistrement', 'Composition musicale', 'Bande originale pour un documentaire.', 'audio', 'composition'],
            ['Affiche illustrée', 'Illustration marketing', 'Campagne publicitaire illustrée.', 'illustration', 'illustration-2'],
        ];

        foreach ($items as $i => [$title, $subtitle, $description, $category, $image]) {
            PortfolioItem::updateOrCreate(['title' => $title], [
                'subtitle' => $subtitle,
                'description' => $description,
                'category' => $category,
                'image' => "images/portfolio/{$image}.webp",
                'position' => $i,
            ]);
        }
    }

    private function seedVideos(): void
    {
        Video::updateOrCreate(['title' => 'Earth Animation'], [
            'description' => 'Animation 3D de la Terre réalisée sous Blender.',
            'category' => 'animation',
            'video_type' => 'youtube',
            'url' => 'https://www.youtube.com/watch?v=u7WFLtJUh98',
            'thumbnail' => 'images/videos/earth-animation.jpg',
            'is_published' => true,
            'is_featured' => true,
            'views' => 11,
        ]);

        // Liens perdus lors de la récupération : à compléter depuis l'admin, puis publier.
        Video::updateOrCreate(['title' => 'Dans l’ombre des plumes'], [
            'description' => 'À compléter : collez le lien YouTube de la vidéo puis publiez-la.',
            'category' => 'animation',
            'video_type' => 'url',
            'url' => 'https://www.youtube.com/@ElementiumStudio',
            'thumbnail' => 'images/videos/ombre-des-plumes.jpg',
            'is_published' => false,
            'views' => 8,
        ]);

        Video::updateOrCreate(['title' => 'RCJG'], [
            'description' => 'À compléter : collez le lien YouTube de la vidéo puis publiez-la.',
            'category' => 'publicite',
            'video_type' => 'url',
            'url' => 'https://www.youtube.com/@ElementiumStudio',
            'thumbnail' => 'images/videos/rcjg.jpg',
            'is_published' => false,
            'views' => 13,
        ]);
    }
}
