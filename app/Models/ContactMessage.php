<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'email', 'company', 'service', 'subject', 'message', 'read_at'])]
class ContactMessage extends Model
{
    public const SERVICES = [
        'animation' => 'Animation 2D/3D',
        'illustration' => 'Illustrations numériques',
        'audio' => 'Production audio',
        'gaming' => 'Développement de jeux vidéo',
        'communication' => 'Communication',
        'video' => 'Post-production vidéo',
        'vr' => 'Réalité virtuelle & augmentée',
        'other' => 'Autre',
    ];

    protected function casts(): array
    {
        return ['read_at' => 'datetime'];
    }

    public function getServiceLabelAttribute(): ?string
    {
        return self::SERVICES[$this->service] ?? $this->service;
    }
}
