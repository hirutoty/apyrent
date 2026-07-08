@if ($paginator->hasPages())
<nav role="navigation" aria-label="Pagination Navigation">
    <div class="flex items-center justify-center gap-1 py-3">

        {{-- Previous Button --}}
        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-300 cursor-not-allowed select-none">
                <i class="bi bi-chevron-left text-sm"></i>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
               class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-purple-50 hover:border-purple-300 hover:text-purple-600 transition-colors">
                <i class="bi bi-chevron-left text-sm"></i>
            </a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($elements as $element)

            {{-- Ellipsis --}}
            @if (is_string($element))
                <span class="inline-flex items-center justify-center w-8 h-8 text-sm text-gray-400 select-none">
                    &hellip;
                </span>
            @endif

            {{-- Array of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page"
                              class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-purple-600 text-white text-sm font-semibold shadow-sm select-none">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}"
                           class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 text-gray-600 text-sm hover:bg-purple-50 hover:border-purple-300 hover:text-purple-600 transition-colors">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif

        @endforeach

        {{-- Next Button --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
               class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-purple-50 hover:border-purple-300 hover:text-purple-600 transition-colors">
                <i class="bi bi-chevron-right text-sm"></i>
            </a>
        @else
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-300 cursor-not-allowed select-none">
                <i class="bi bi-chevron-right text-sm"></i>
            </span>
        @endif

    </div>

    {{-- Info teks: "Showing X to Y of Z results" --}}
    <p class="text-center text-xs text-gray-400 pb-1">
        Menampilkan
        <span class="font-medium text-gray-600">{{ $paginator->firstItem() }}</span>
        &ndash;
        <span class="font-medium text-gray-600">{{ $paginator->lastItem() }}</span>
        dari
        <span class="font-medium text-gray-600">{{ $paginator->total() }}</span>
        data
    </p>
</nav>
@endif
