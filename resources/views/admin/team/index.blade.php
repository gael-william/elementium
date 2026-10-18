@extends('admin.layout')
@section('title', 'Équipe')

@section('content')
    <div class="page-head">
        <div>
            <h1>Équipe</h1>
            <p>Les membres présentés dans la section « Notre équipe créative ».</p>
        </div>
        <a href="{{ route('admin.team.create') }}" class="btn btn--primary"><i class="bi bi-plus-lg"></i> Ajouter</a>
    </div>

    <div class="card table-wrap">
        <table>
            <thead><tr><th></th><th>Nom</th><th>Rôle</th><th>Élément</th><th>Ordre</th><th>Statut</th><th></th></tr></thead>
            <tbody>
            @forelse ($members as $member)
                <tr>
                    <td>@if ($member->photo_url)<img src="{{ $member->photo_url }}" alt="" class="thumb thumb--square">@else<span class="thumb thumb--square thumb--empty"><i class="bi bi-person"></i></span>@endif</td>
                    <td><strong>{{ $member->name }}</strong></td>
                    <td>{{ $member->role }}</td>
                    <td><span class="badge" style="color:var(--{{ $member->element }})">{{ ucfirst($member->element) }}</span></td>
                    <td>{{ $member->position }}</td>
                    <td><span class="badge {{ $member->is_published ? 'badge--ok' : 'badge--wait' }}">{{ $member->is_published ? 'Visible' : 'Masqué' }}</span></td>
                    <td class="actions">
                        <a href="{{ route('admin.team.edit', $member) }}" class="btn btn--sm"><i class="bi bi-pencil"></i></a>
                        <form method="post" action="{{ route('admin.team.destroy', $member) }}" data-confirm="Retirer ce membre de l’équipe ?">
                            @csrf @method('delete')
                            <button class="btn btn--sm btn--danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="muted">Aucun membre.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
