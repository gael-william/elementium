@extends('admin.layout')
@section('title', $member->exists ? 'Modifier le membre' : 'Nouveau membre')

@section('content')
    <div class="page-head">
        <h1>{{ $member->exists ? 'Modifier le membre' : 'Nouveau membre' }}</h1>
        <a href="{{ route('admin.team.index') }}" class="btn"><i class="bi bi-arrow-left"></i> Retour</a>
    </div>

    <form method="post" enctype="multipart/form-data" class="card form"
          action="{{ $member->exists ? route('admin.team.update', $member) : route('admin.team.store') }}">
        @csrf
        @if ($member->exists) @method('put') @endif

        <div class="form-row">
            <label class="label">Nom
                <input type="text" name="name" value="{{ old('name', $member->name) }}" required>
                @error('name')<span class="error">{{ $message }}</span>@enderror
            </label>
            <label class="label">Rôle
                <input type="text" name="role" value="{{ old('role', $member->role) }}" required>
                @error('role')<span class="error">{{ $message }}</span>@enderror
            </label>
        </div>

        <div class="form-row">
            <label class="label">Élément <small>couleur d’accent de sa carte</small>
                <select name="element">
                    @foreach (\App\Models\TeamMember::ELEMENTS as $key => $label)
                        <option value="{{ $key }}" @selected(old('element', $member->element) === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label class="label">Ordre d’affichage
                <input type="number" name="position" min="0" value="{{ old('position', $member->position ?? 0) }}">
            </label>
        </div>

        <label class="label">Photo {{ $member->exists ? '(laisser vide pour garder l’actuelle)' : '' }}
            @if ($member->photo_url)<img src="{{ $member->photo_url }}" alt="" class="preview" style="width:140px">@endif
            <input type="file" name="photo_file" accept="image/*">
            @error('photo_file')<span class="error">{{ $message }}</span>@enderror
        </label>

        <label class="label">Présentation
            <textarea name="bio" rows="3">{{ old('bio', $member->bio) }}</textarea>
        </label>

        <fieldset>
            <legend>Réseaux sociaux (facultatif)</legend>
            <div class="form-row">
                @foreach (\App\Models\TeamMember::SOCIALS as $network)
                    <label class="label">{{ $network === 'x' ? 'X (Twitter)' : ucfirst($network) }}
                        <input type="url" name="socials[{{ $network }}]" value="{{ old("socials.$network", $member->socials[$network] ?? '') }}" placeholder="https://">
                        @error("socials.$network")<span class="error">{{ $message }}</span>@enderror
                    </label>
                @endforeach
            </div>
        </fieldset>

        <label class="check"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $member->is_published))> Visible sur le site</label>

        <div><button type="submit" class="btn btn--primary"><i class="bi bi-check-lg"></i> Enregistrer</button></div>
    </form>
@endsection
