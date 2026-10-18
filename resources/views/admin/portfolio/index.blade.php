@extends('admin.layout')
@section('title', 'Réalisations')

@section('content')
    <div class="page-head">
        <div>
            <h1>Réalisations</h1>
            <p>Les projets affichés dans la section « Portfolio » du site.</p>
        </div>
        <a href="{{ route('admin.portfolio.create') }}" class="btn btn--primary"><i class="bi bi-plus-lg"></i> Ajouter</a>
    </div>

    <div class="card table-wrap">
        <table>
            <thead><tr><th></th><th>Titre</th><th>Catégorie</th><th>Ordre</th><th>Statut</th><th></th></tr></thead>
            <tbody>
            @forelse ($items as $item)
                <tr>
                    <td><img src="{{ $item->image_url }}" alt="" class="thumb"></td>
                    <td><strong>{{ $item->title }}</strong><br><span class="muted small">{{ $item->subtitle }}</span></td>
                    <td>{{ $item->category_label }}</td>
                    <td>{{ $item->position }}</td>
                    <td><span class="badge {{ $item->is_published ? 'badge--ok' : 'badge--wait' }}">{{ $item->is_published ? 'Visible' : 'Masquée' }}</span></td>
                    <td class="actions">
                        <a href="{{ route('admin.portfolio.edit', $item) }}" class="btn btn--sm"><i class="bi bi-pencil"></i></a>
                        <form method="post" action="{{ route('admin.portfolio.destroy', $item) }}" data-confirm="Supprimer cette réalisation ?">
                            @csrf @method('delete')
                            <button class="btn btn--sm btn--danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="muted">Aucune réalisation.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
