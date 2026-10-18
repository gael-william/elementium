<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Support\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VideoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $videos = Video::published()
            ->when(
                $request->filled('category') && $request->category !== 'all',
                fn ($q) => $q->where('category', $request->category),
            )
            ->orderByDesc('is_featured')->orderBy('position')->latest()
            ->get();

        return response()->json(['videos' => $videos->map->toPublicArray()]);
    }

    public function show(Request $request, Video $video): JsonResponse
    {
        abort_unless($video->is_published, 404);

        // Une vue comptée par vidéo et par session.
        $seen = $request->session()->get('seen_videos', []);
        if (! in_array($video->id, $seen, true)) {
            $video->increment('views');
            $request->session()->push('seen_videos', $video->id);
        }

        return response()->json([
            'video' => $video->toPublicArray(),
            'liked' => in_array($video->id, $request->session()->get('liked_videos', []), true),
        ]);
    }

    public function like(Request $request, Video $video): JsonResponse
    {
        abort_unless($video->is_published, 404);

        $liked = $request->session()->get('liked_videos', []);

        if (in_array($video->id, $liked, true)) {
            $video->likes > 0 && $video->decrement('likes');
            $liked = array_values(array_diff($liked, [$video->id]));
            $isLiked = false;
        } else {
            $video->increment('likes');
            $liked[] = $video->id;
            $isLiked = true;
        }

        $request->session()->put('liked_videos', $liked);

        return response()->json(['liked' => $isLiked, 'likes' => $video->likes]);
    }

    /**
     * Proposition d'une vidéo par un visiteur : elle reste invisible
     * jusqu'à validation dans l'administration.
     */
    public function store(Request $request): JsonResponse
    {
        // Champ piège invisible : rempli uniquement par les robots.
        if ($request->filled('website')) {
            return response()->json(['message' => 'Merci !']);
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'category' => ['required', Rule::in(array_keys(Video::CATEGORIES))],
            'description' => ['nullable', 'string', 'max:2000'],
            'submitter_name' => ['required', 'string', 'max:100'],
            'submitter_email' => ['required', 'email', 'max:150'],
            'url' => ['required_without:file', 'nullable', 'url', 'max:500'],
            'file' => ['required_without:url', 'nullable', 'file', 'mimetypes:video/mp4,video/webm,video/quicktime', 'max:204800'],
        ], [
            'url.required_without' => 'Indiquez un lien vidéo ou choisissez un fichier.',
            'file.required_without' => 'Indiquez un lien vidéo ou choisissez un fichier.',
            'file.max' => 'Le fichier ne doit pas dépasser 200 Mo.',
        ]);

        if ($request->hasFile('file')) {
            $data['video_type'] = 'file';
            $data['url'] = Media::store($request->file('file'), 'videos');
        } else {
            $data['video_type'] = Video::parseUrl($data['url'])['type'];
        }

        unset($data['file']);
        Video::create($data + ['is_published' => false]);

        return response()->json([
            'message' => 'Merci ! Votre vidéo a bien été reçue. Elle apparaîtra après validation par notre équipe.',
        ], 201);
    }
}
