@extends('admin.layout')
@section('title', 'Messages')

@section('content')
    <div class="page-head">
        <div>
            <h1>Messages</h1>
            <p>Demandes reçues via le formulaire de contact.</p>
        </div>
    </div>

    <div class="card table-wrap">
        <table>
            <thead><tr><th>De</th><th>Sujet</th><th>Service</th><th>Reçu</th><th></th></tr></thead>
            <tbody>
            @forelse ($messages as $message)
                <tr class="{{ $message->read_at ? '' : 'is-unread' }}">
                    <td>{{ $message->name }}<br><span class="muted small">{{ $message->email }}</span></td>
                    <td><a href="{{ route('admin.messages.show', $message) }}">{{ $message->subject }}</a></td>
                    <td class="small">{{ $message->service_label }}</td>
                    <td class="small muted">{{ $message->created_at->format('d/m/Y H:i') }}</td>
                    <td class="actions"><a href="{{ route('admin.messages.show', $message) }}" class="btn btn--sm">Lire</a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="muted">Aucun message pour l’instant.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $messages->links('admin.pagination') }}</div>
@endsection
