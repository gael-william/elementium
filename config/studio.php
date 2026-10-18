<?php

// Coordonnées et liens d'Élémentium Studio, utilisés dans tout le site.
return [
    'name' => 'Élémentium Studio',
    'tagline' => 'Création audiovisuelle innovante',
    'founded' => 2024,
    'team_size' => 8,

    'email' => env('STUDIO_CONTACT_EMAIL', 'elementium.studio@gmail.com'),
    'phones' => ['+226 44 76 72 72', '+226 72 62 62 36'],
    'address' => 'Wemtenga, Secteur 23 — Ouagadougou, Burkina Faso',
    'hours' => 'Lundi – Dimanche, 24h/24',

    'socials' => [
        'whatsapp' => 'https://wa.me/22672626236',
        'tiktok' => 'https://www.tiktok.com/@elementiumstudio',
        'facebook' => 'https://web.facebook.com/profile.php?id=61578975073035',
        'instagram' => 'https://www.instagram.com/elementiumstudio',
        'youtube' => 'https://www.youtube.com/@ElementiumStudio',
    ],

    'map_embed' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d7794.3404117197015!2d-1.489570162252194!3d12.371528631308419!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xe2ebe58d655e785%3A0x9355482e82121f80!2sWemtenga%2C%20Ouagadougou!5e0!3m2!1sfr!2sbf!4v1755014830584!5m2!1sfr!2sbf',

    'services' => [
        ['icon' => 'bi-film', 'element' => 'feu', 'title' => 'Animation 2D/3D', 'text' => 'Modélisation, rigging et animation de personnages et d’environnements.'],
        ['icon' => 'bi-music-note-beamed', 'element' => 'eau', 'title' => 'Production audio', 'text' => 'Enregistrement, mixage et design sonore immersif.'],
        ['icon' => 'bi-joystick', 'element' => 'terre', 'title' => 'Développement de jeux', 'text' => 'Unity et Unreal Engine pour des expériences interactives.'],
        ['icon' => 'bi-palette', 'element' => 'feu', 'title' => 'Illustration digitale', 'text' => 'Concept art et illustrations haute résolution.'],
        ['icon' => 'bi-play-circle', 'element' => 'eau', 'title' => 'Motion design', 'text' => 'Animation graphique et typographie animée.'],
        ['icon' => 'bi-sliders', 'element' => 'terre', 'title' => 'Post-production', 'text' => 'Étalonnage, compositing et effets visuels.'],
    ],
];
