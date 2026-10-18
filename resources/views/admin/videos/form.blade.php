@extends('admin.layout')
@section('title', $video->exists ? 'Modifier la vidéo' : 'Nouvelle vidéo')

@section('content')
    <div class="page-head">
        <h1>{{ $video->exists ? 'Modifier la vidéo' : 'Nouvelle vidéo' }}</h1>
        <a href="{{ route('admin.videos.index') }}" class="btn"><i class="bi bi-arrow-left"></i> Retour</a>
    </div>

    <form method="post" enctype="multipart/form-data" class="card form"
          action="{{ $video->exists ? route('admin.videos.update', $video) : route('admin.videos.store') }}">
        @csrf
        @if ($video->exists) @method('put') @endif

        @if ($video->submitter_name)
            <div class="hint"><i class="bi bi-person"></i> Proposée par <strong>{{ $video->submitter_name }}</strong> — <a href="mailto:{{ $video->submitter_email }}">{{ $video->submitter_email }}</a></div>
        @endif

        <label class="label">Titre
            <input type="text" name="title" value="{{ old('title', $video->title) }}" required>
            @error('title')<span class="error">{{ $message }}</span>@enderror
        </label>

        <div class="form-row">
            <label class="label">Catégorie
                <select name="category">
                    @foreach (\App\Models\Video::CATEGORIES as $key => $label)
                        <option value="{{ $key }}" @selected(old('category', $video->category) === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label class="label">Durée <small>ex. 3:25 (facultatif)</small>
                <input type="text" name="duration" value="{{ old('duration', $video->formatted_duration) }}" placeholder="3:25">
                @error('duration')<span class="error">{{ $message }}</span>@enderror
            </label>
            <label class="label">Ordre d’affichage <small>plus petit = en premier</small>
                <input type="number" name="position" min="0" value="{{ old('position', $video->position ?? 0) }}">
            </label>
        </div>

        <fieldset>
            <legend>Source de la vidéo</legend>
            <label class="label">Lien YouTube, Vimeo ou lien direct (.mp4)
                <input type="url" name="url" value="{{ old('url', $video->video_type === 'file' ? '' : $video->url) }}" placeholder="https://www.youtube.com/watch?v=…">
                @error('url')<span class="error">{{ $message }}</span>@enderror
            </label>
            <label class="label">… ou envoyer un fichier vidéo <small>MP4, WebM ou MOV</small>
                <input type="file" name="file" accept="video/mp4,video/webm,video/quicktime">
                @error('file')<span class="error">{{ $message }}</span>@enderror
            </label>
            @if ($video->video_type === 'file')
                <video src="{{ $video->source_url }}" controls class="preview" style="width:320px"></video>
                <span class="muted small">Fichier actuel conservé si vous ne renseignez ni lien ni nouveau fichier.</span>
            @endif
        </fieldset>

        <fieldset>
            <legend>Miniature</legend>
            @if ($video->thumbnail_url)
                <img src="{{ $video->thumbnail_url }}" alt="" class="preview">
            @endif
            <label class="label">Image de couverture <small>facultatif — pour YouTube, la miniature est récupérée automatiquement</small>
                <input type="file" name="thumbnail_file" accept="image/*">
                @error('thumbnail_file')<span class="error">{{ $message }}</span>@enderror
            </label>
            @if ($video->thumbnail)
                <label class="check"><input type="checkbox" name="remove_thumbnail" value="1"> Retirer la miniature actuelle</label>
            @endif
        </fieldset>

        <label class="label">Description
            <textarea name="description" rows="4">{{ old('description', $video->description) }}</textarea>
        </label>

        <div class="actions" style="gap:24px">
            <label class="check"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $video->is_published))> Publiée sur le site</label>
            <label class="check"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $video->is_featured))> Mise en avant (grande carte)</label>
        </div>

        <div><button type="submit" class="btn btn--primary"><i class="bi bi-check-lg"></i> Enregistrer</button></div>
    </form>
@endsection
