@if ($paginator->hasPages())
    <nav aria-label="Pagination">
        @if ($paginator->onFirstPage())
            <span class="btn btn--sm muted">← Précédent</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="btn btn--sm" rel="prev">← Précédent</a>
        @endif
        <span class="btn btn--sm muted">Page {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="btn btn--sm" rel="next">Suivant →</a>
        @else
            <span class="btn btn--sm muted">Suivant →</span>
        @endif
    </nav>
@endif
