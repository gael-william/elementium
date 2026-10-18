@extends('admin.layout')
@section('title', 'Vidéos')

@section('content')
    <div class="page-head">
        <div>
            <h1>Vidéos</h1>
            <p>Les vidéos proposées par les visiteurs arrivent ici « en attente » : vérifiez-les puis publiez-les.</p>
        </div>
        <a href="{{ route('admin.videos.create') }}" class="btn btn--primary"><i class="bi bi-plus-lg"></i> Ajouter</a>
    </div>

    <div class="tabs">
        <a href="{{ route('admin.videos.index') }}" class="{{ ! request('status') ? 'is-active' : '' }}">Toutes</a>
        <a href="{{ route('admin.videos.index', ['status' => 'attente']) }}" class="{{ request('status') === 'attente' ? 'is-active' : '' }}">En attente</a>
        <a href="{{ route('admin.videos.index', ['status' => 'publiees']) }}" class="{{ request('status') === 'publiees' ? 'is-active' : '' }}">Publiées</a>
    </div>

    <div class="card table-wrap">
        <table>
            <thead><tr><th></th><th>Titre</th><th>Catégorie</th><th>Source</th><th>Stats</th><th>Statut</th><th></th></tr></thead>
            <tbody>
            @forelse ($videos as $video)
                <tr>
                    <td>@if ($video->thumbnail_url)<img src="{{ $video->thumbnail_url }}" alt="" class="thumb">@else<span class="thumb thumb--empty"><i class="bi bi-film"></i></span>@endif</td>
                    <td>
                        <strong>{{ $video->title }}</strong> @if ($video->is_featured)<i class="bi bi-star-fill" style="color:var(--feu)" title="Mise en avant"></i>@endif
                        @if ($video->submitter_name)<br><span class="muted small">Proposée par {{ $video->submitter_name }} ({{ $video->submitter_email }})</span>@endif
                    </td>
                    <td>{{ $video->category_label }}</td>
                    <td><span class="badge">{{ ['youtube' => 'YouTube', 'vimeo' => 'Vimeo', 'file' => 'Fichier', 'url' => 'Lien'][$video->video_type] ?? $video->video_type }}</span></td>
                    <td class="small muted"><i class="bi bi-eye"></i> {{ $video->views }} · <i class="bi bi-heart"></i> {{ $video->likes }}</td>
                    <td>
                        <form method="post" action="{{ route('admin.videos.toggle', $video) }}">
                            @csrf @method('patch')
                            <button class="badge {{ $video->is_published ? 'badge--ok' : 'badge--wait' }}" style="cursor:pointer" title="Cliquer pour changer">
                                {{ $video->is_published ? 'Publiée' : 'En attente' }}
                            </button>
                        </form>
                    </td>
                    <td class="actions">
                        <a href="{{ route('admin.videos.edit', $video) }}" class="btn btn--sm"><i class="bi bi-pencil"></i></a>
                        <form method="post" action="{{ route('admin.videos.destroy', $video) }}" data-confirm="Supprimer définitivement cette vidéo ?">
                            @csrf @method('delete')
                            <button class="btn btn--sm btn--danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="muted">Aucune vidéo.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $videos->links('admin.pagination') }}</div>
@endsection
