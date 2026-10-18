@extends('admin.layout')

@section('content')
    <div class="page-head">
        <div>
            <h1>Bonjour 👋</h1>
            <p>Voici l’activité d’Élémentium Studio.</p>
        </div>
        <a href="{{ route('admin.videos.create') }}" class="btn btn--primary"><i class="bi bi-plus-lg"></i> Ajouter une vidéo</a>
    </div>

    <div class="grid grid--stats" style="margin-bottom:24px">
        @foreach ($stats as $stat)
            <a href="{{ route($stat['route']) }}" class="card stat">
                <i class="bi {{ $stat['icon'] }}"></i>
                <div><strong>{{ number_format($stat['value'], 0, ',', ' ') }}</strong><span>{{ $stat['label'] }}</span></div>
            </a>
        @endforeach
    </div>

    <div class="grid grid--2">
        <section class="card">
            <h2 style="font-size:1.2rem">Vidéos en attente de validation</h2>
            @forelse ($pendingVideos as $video)
                <div class="list-item">
                    @if ($video->thumbnail_url)<img src="{{ $video->thumbnail_url }}" alt="" class="thumb">@else<span class="thumb thumb--empty"><i class="bi bi-film"></i></span>@endif
                    <div>
                        <strong>{{ $video->title }}</strong>
                        <span class="muted small">{{ $video->submitter_name ? 'Proposée par '.$video->submitter_name : 'Brouillon' }} · {{ $video->created_at->diffForHumans() }}</span>
                    </div>
                    <a href="{{ route('admin.videos.edit', $video) }}" class="btn btn--sm">Examiner</a>
                </div>
            @empty
                <p class="muted">Aucune vidéo en attente. 🎉</p>
            @endforelse
        </section>

        <section class="card">
            <h2 style="font-size:1.2rem">Derniers messages</h2>
            @forelse ($latestMessages as $message)
                <a href="{{ route('admin.messages.show', $message) }}" class="list-item">
                    <i class="bi {{ $message->read_at ? 'bi-envelope-open muted' : 'bi-envelope-fill' }}" style="color:var(--feu)"></i>
                    <div>
                        <strong>{{ $message->subject }}</strong>
                        <span class="muted small">{{ $message->name }} · {{ $message->created_at->diffForHumans() }}</span>
                    </div>
                </a>
            @empty
                <p class="muted">Aucun message pour l’instant.</p>
            @endforelse
        </section>
    </div>
@endsection
