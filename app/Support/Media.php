<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Les médias sont stockés sous forme de chemin relatif à public/ (« images/… » pour
 * les fichiers livrés avec le site, « uploads/… » pour ceux envoyés depuis l'admin),
 * ou sous forme d'URL absolue (miniature YouTube, lien externe).
 */
class Media
{
    public static function url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return preg_match('~^https?://~i', $path) ? $path : asset($path);
    }

    public static function store(UploadedFile $file, string $folder): string
    {
        return 'uploads/'.Storage::disk('uploads')->putFile($folder, $file);
    }

    /** Supprime un fichier envoyé ; les images livrées avec le site ne sont jamais touchées. */
    public static function delete(?string $path): void
    {
        if ($path && str_starts_with($path, 'uploads/')) {
            Storage::disk('uploads')->delete(substr($path, strlen('uploads/')));
        }
    }
}
