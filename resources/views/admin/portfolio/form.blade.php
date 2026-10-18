@extends('admin.layout')
@section('title', $item->exists ? 'Modifier la réalisation' : 'Nouvelle réalisation')

@section('content')
    <div class="page-head">
        <h1>{{ $item->exists ? 'Modifier la réalisation' : 'Nouvelle réalisation' }}</h1>
        <a href="{{ route('admin.portfolio.index') }}" class="btn"><i class="bi bi-arrow-left"></i> Retour</a>
    </div>

    <form method="post" enctype="multipart/form-data" class="card form"
          action="{{ $item->exists ? route('admin.portfolio.update', $item) : route('admin.portfolio.store') }}">
        @csrf
        @if ($item->exists) @method('put') @endif

        <div class="form-row">
            <label class="label">Titre
                <input type="text" name="title" value="{{ old('title', $item->title) }}" required>
                @error('title')<span class="error">{{ $message }}</span>@enderror
            </label>
            <label class="label">Sous-titre <small>ex. « Animation 3D — personnage »</small>
                <input type="text" name="subtitle" value="{{ old('subtitle', $item->subtitle) }}">
            </label>
        </div>

        <div class="form-row">
            <label class="label">Catégorie
                <select name="category">
                    @foreach (\App\Models\PortfolioItem::CATEGORIES as $key => $label)
                        <option value="{{ $key }}" @selected(old('category', $item->category) === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label class="label">Ordre d’affichage
                <input type="number" name="position" min="0" value="{{ old('position', $item->position ?? 0) }}">
            </label>
        </div>

        <label class="label">Image {{ $item->exists ? '(laisser vide pour garder l’actuelle)' : '' }}
            @if ($item->image_url)<img src="{{ $item->image_url }}" alt="" class="preview">@endif
            <input type="file" name="image_file" accept="image/*" @required(! $item->exists)>
            @error('image_file')<span class="error">{{ $message }}</span>@enderror
        </label>

        <label class="label">Description
            <textarea name="description" rows="4">{{ old('description', $item->description) }}</textarea>
        </label>

        <label class="label">Lien externe <small>facultatif (Behance, YouTube, Play Store…)</small>
            <input type="url" name="link" value="{{ old('link', $item->link) }}">
            @error('link')<span class="error">{{ $message }}</span>@enderror
        </label>

        <label class="check"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $item->is_published))> Visible sur le site</label>

        <div><button type="submit" class="btn btn--primary"><i class="bi bi-check-lg"></i> Enregistrer</button></div>
    </form>
@endsection
