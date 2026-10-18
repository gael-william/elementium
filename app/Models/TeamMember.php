<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'role', 'bio', 'photo', 'element', 'socials', 'is_published', 'position'])]
class TeamMember extends Model
{
    public const ELEMENTS = ['feu' => 'Feu (orange)', 'eau' => 'Eau (bleu)', 'terre' => 'Terre (vert)'];

    public const SOCIALS = ['facebook', 'instagram', 'linkedin', 'tiktok', 'x', 'youtube'];

    protected function casts(): array
    {
        return [
            'socials' => 'array',
            'is_published' => 'boolean',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->orderBy('position')->orderBy('id');
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return Media::url($this->photo);
    }
}
