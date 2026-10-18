<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title', 'description', 'category', 'video_type', 'url', 'thumbnail', 'duration',
    'views', 'likes', 'is_published', 'is_featured', 'submitter_name', 'submitter_email', 'position',
])]
class Video extends Model
{
    public const CATEGORIES = [
        'animation' => 'Animation',
        'documentaire' => 'Documentaire',
        'musique' => 'Musique',
        'publicite' => 'Publicité',
        'general' => 'Général',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /**
     * Déduit le type (youtube, vimeo, url) et l'identifiant d'un lien vidéo.
     *
     * @return array{type: string, id: string|null}
     */
    public static function parseUrl(string $url): array
    {
        if (preg_match('~(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/)|youtu\.be/)([\w-]{11})~i', $url, $m)) {
            return ['type' => 'youtube', 'id' => $m[1]];
        }

        if (preg_match('~vimeo\.com/(?:video/|channels/[\w-]+/|.*?/)?(\d+)~i', $url, $m)) {
            return ['type' => 'vimeo', 'id' => $m[1]];
        }

        return ['type' => 'url', 'id' => null];
    }

    public function getEmbedUrlAttribute(): ?string
    {
        $parsed = self::parseUrl($this->url);

        return match ($this->video_type) {
            'youtube' => "https://www.youtube-nocookie.com/embed/{$parsed['id']}?autoplay=1&rel=0&modestbranding=1",
            'vimeo' => "https://player.vimeo.com/video/{$parsed['id']}?autoplay=1&byline=0&title=0",
            default => null,
        };
    }

    /** URL lisible directement par une balise <video> (fichier envoyé ou lien direct). */
    public function getSourceUrlAttribute(): ?string
    {
        return in_array($this->video_type, ['file', 'url'], true) ? Media::url($this->url) : null;
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if ($this->thumbnail) {
            return Media::url($this->thumbnail);
        }

        if ($this->video_type === 'youtube') {
            return 'https://i.ytimg.com/vi/'.self::parseUrl($this->url)['id'].'/hqdefault.jpg';
        }

        return null;
    }

    public function getFormattedDurationAttribute(): string
    {
        if (! $this->duration) {
            return '';
        }

        $h = intdiv($this->duration, 3600);
        $m = intdiv($this->duration % 3600, 60);
        $s = $this->duration % 60;

        return $h ? sprintf('%d:%02d:%02d', $h, $m, $s) : sprintf('%d:%02d', $m, $s);
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? ucfirst($this->category);
    }

    public function toPublicArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category,
            'category_label' => $this->category_label,
            'video_type' => $this->video_type,
            'embed_url' => $this->embed_url,
            'source_url' => $this->source_url,
            'thumbnail' => $this->thumbnail_url,
            'duration' => $this->formatted_duration,
            'views' => $this->views,
            'likes' => $this->likes,
            'is_featured' => $this->is_featured,
        ];
    }
}
