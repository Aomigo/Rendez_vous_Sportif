@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between gap-4">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-neutral-400 bg-white border-2 border-neutral-300 rounded-lg cursor-not-allowed">
                {!! __('pagination.previous') !!}
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-white bg-black border-2 border-black rounded-lg hover:bg-neutral-800 transition">
                {!! __('pagination.previous') !!}
            </a>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-white bg-black border-2 border-black rounded-lg hover:bg-neutral-800 transition">
                {!! __('pagination.next') !!}
            </a>
        @else
            <span class="inline-flex items-center px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-neutral-400 bg-white border-2 border-neutral-300 rounded-lg cursor-not-allowed">
                {!! __('pagination.next') !!}
            </span>
        @endif
    </nav>
@endif
