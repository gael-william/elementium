<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Support\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VideoController extends Controller
{
    public function index(Request $request): View
    {
        $videos = Video::query()
            ->when($request->status === 'attente', fn ($q) => $q->where('is_published', false))
            ->when($request->status === 'publiees', fn ($q) => $q->where('is_published', true))
            ->orderBy('is_published')->orderBy('position')->latest()
            ->paginate(20)->withQueryString();

        return view('admin.videos.index', compact('videos'));
    }

    public function create(): View
    {
        return view('admin.videos.form', ['video' => new Video(['category' => 'animation', 'is_published' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        Video::create($this->validated($request));

        return redirect()->route('admin.videos.index')->with('status', 'Vidéo ajoutée.');
    }

    public function edit(Video $video): View
    {
        return view('admin.videos.form', compact('video'));
    }

    public function update(Request $request, Video $video): RedirectResponse
    {
        $video->update($this->validated($request, $video));

        return redirect()->route('admin.videos.index')->with('status', 'Vidéo mise à jour.');
    }

    public function togglePublished(Video $video): RedirectResponse
    {
        $video->update(['is_published' => ! $video->is_published]);

        return back()->with('status', $video->is_published ? 'Vidéo publiée.' : 'Vidéo retirée du site.');
    }

    public function destroy(Video $video): RedirectResponse
    {
        if ($video->video_type === 'file') {
            Media::delete($video->url);
        }
        Media::delete($video->thumbnail);
        $video->delete();

        return redirect()->route('admin.videos.index')->with('status', 'Vidéo supprimée.');
    }

    private function validated(Request $request, ?Video $video = null): array
    {
        $hasFile = $video?->video_type === 'file';

        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'category' => ['required', Rule::in(array_keys(Video::CATEGORIES))],
            'url' => [$hasFile ? 'nullable' : 'required_without:file', 'nullable', 'url', 'max:500'],
            'file' => ['nullable', 'file', 'mimetypes:video/mp4,video/webm,video/quicktime', 'max:512000'],
            'thumbnail_file' => ['nullable', 'image', 'max:5120'],
            'duration' => ['nullable', 'regex:/^(\d+:)?\d{1,2}:\d{2}$|^\d+$/'],
            'position' => ['nullable', 'integer', 'min:0'],
        ], [
            'url.required_without' => 'Indiquez un lien vidéo ou envoyez un fichier.',
            'duration.regex' => 'Durée au format 3:25 ou 1:02:10.',
        ]);

        if ($request->hasFile('file')) {
            if ($hasFile) {
                Media::delete($video->url);
            }
            $data['video_type'] = 'file';
            $data['url'] = Media::store($request->file('file'), 'videos');
        } elseif (! empty($data['url'])) {
            if ($hasFile) {
                Media::delete($video->url);
            }
            $data['video_type'] = Video::parseUrl($data['url'])['type'];
        } else {
            unset($data['url']); // on garde le fichier déjà envoyé
        }

        if ($request->hasFile('thumbnail_file')) {
            Media::delete($video?->thumbnail);
            $data['thumbnail'] = Media::store($request->file('thumbnail_file'), 'thumbnails');
        } elseif ($request->boolean('remove_thumbnail')) {
            Media::delete($video?->thumbnail);
            $data['thumbnail'] = null;
        }

        $data['duration'] = $this->toSeconds($data['duration'] ?? null);
        $data['position'] = $data['position'] ?? 0;
        $data['is_published'] = $request->boolean('is_published');
        $data['is_featured'] = $request->boolean('is_featured');
        unset($data['file'], $data['thumbnail_file']);

        return $data;
    }

    private function toSeconds(?string $value): int
    {
        if (! $value) {
            return 0;
        }

        return array_reduce(explode(':', $value), fn ($total, $part) => $total * 60 + (int) $part, 0);
    }
}
