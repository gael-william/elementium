<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'subtitle', 'description', 'category', 'image', 'link', 'is_published', 'position'])]
class PortfolioItem extends Model
{
    public const CATEGORIES = [
        'animation' => 'Animation',
        'illustration' => 'Illustration',
        'audio' => 'Audio',
        'gaming' => 'Gaming',
    ];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->orderBy('position')->orderBy('id');
    }

    public function getImageUrlAttribute(): ?string
    {
        return Media::url($this->image);
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? ucfirst($this->category);
    }
}
