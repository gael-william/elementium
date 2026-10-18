@extends('admin.layout')
@section('title', $message->subject)

@section('content')
    <div class="page-head">
        <h1>{{ $message->subject }}</h1>
        <div class="actions">
            <a href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re: '.$message->subject) }}" class="btn btn--primary"><i class="bi bi-reply"></i> Répondre</a>
            <form method="post" action="{{ route('admin.messages.destroy', $message) }}" data-confirm="Supprimer ce message ?">
                @csrf @method('delete')
                <button class="btn btn--danger"><i class="bi bi-trash"></i> Supprimer</button>
            </form>
            <a href="{{ route('admin.messages.index') }}" class="btn"><i class="bi bi-arrow-left"></i> Retour</a>
        </div>
    </div>

    <div class="card">
        <dl class="meta">
            <dt>Nom</dt><dd>{{ $message->name }}</dd>
            <dt>E-mail</dt><dd><a href="mailto:{{ $message->email }}">{{ $message->email }}</a></dd>
            @if ($message->company)<dt>Entreprise</dt><dd>{{ $message->company }}</dd>@endif
            <dt>Service</dt><dd>{{ $message->service_label }}</dd>
            <dt>Reçu le</dt><dd>{{ $message->created_at->format('d/m/Y à H:i') }}</dd>
        </dl>
        <div class="message-body">{{ $message->message }}</div>
    </div>
@endsection
