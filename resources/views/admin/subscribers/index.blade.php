@extends('admin.layout')
@section('title', 'Newsletter')

@section('content')
    <div class="page-head">
        <div>
            <h1>Abonnés à la newsletter</h1>
            <p>{{ $subscribers->total() }} inscrit(s).</p>
        </div>
        <a href="{{ route('admin.subscribers.export') }}" class="btn"><i class="bi bi-download"></i> Exporter (CSV)</a>
    </div>

    <div class="card table-wrap">
        <table>
            <thead><tr><th>E-mail</th><th>Inscrit le</th><th></th></tr></thead>
            <tbody>
            @forelse ($subscribers as $subscriber)
                <tr>
                    <td>{{ $subscriber->email }}</td>
                    <td class="small muted">{{ $subscriber->created_at->format('d/m/Y') }}</td>
                    <td class="actions">
                        <form method="post" action="{{ route('admin.subscribers.destroy', $subscriber) }}" data-confirm="Retirer cet abonné ?">
                            @csrf @method('delete')
                            <button class="btn btn--sm btn--danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3" class="muted">Aucun abonné pour l’instant.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $subscribers->links('admin.pagination') }}</div>
@endsection
