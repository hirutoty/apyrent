@if ($paginator && method_exists($paginator, 'hasPages') && $paginator->hasPages())
    @php
        $currentPage  = $paginator->currentPage();
        $lastPage     = $paginator->lastPage();
        $from         = $paginator->firstItem();
        $to           = $paginator->lastItem();
        $total        = $paginator->total();

        // Build window of page numbers around current page
        $window = 2; // pages on each side of current
        $pages  = [];
        for ($i = 1; $i <= $lastPage; $i++) {
            if ($i === 1 || $i === $lastPage || ($i >= $currentPage - $window && $i <= $currentPage + $window)) {
                $pages[] = $i;
            }
        }
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-3">
        {{-- Info --}}
        <p class="text-xs text-gray-500">
            Menampilkan <span class="font-semibold text-gray-700">{{ $from }}</span>
            &ndash;
            <span class="font-semibold text-gray-700">{{ $to }}</span>
            dari
            <span class="font-semibold text-gray-700">{{ $total }}</span> data
        </p>

        {{-- Buttons --}}
        <div class="flex items-center gap-1">

            {{-- Prev --}}
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center justify-center w-8 h-8 text-xs rounded-lg border border-gray-200 text-gray-300 cursor-not-allowed select-none">
                    <i class="fa fa-chevron-left text-[10px]"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   class="inline-flex items-center justify-center w-8 h-8 text-xs rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">
                    <i class="fa fa-chevron-left text-[10px]"></i>
                </a>
            @endif

            {{-- Page numbers --}}
            @php $prev = null; @endphp
            @foreach ($pages as $page)
                @if ($prev !== null && $page !== $prev + 1)
                    <span class="px-1 text-xs text-gray-400 select-none">&hellip;</span>
                @endif

                @if ($page === $currentPage)
                    <span class="inline-flex items-center justify-center w-8 h-8 text-xs rounded-lg bg-blue-600 text-white font-semibold border border-blue-600 select-none">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $paginator->url($page) }}"
                       class="inline-flex items-center justify-center w-8 h-8 text-xs rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">
                        {{ $page }}
                    </a>
                @endif

                @php $prev = $page; @endphp
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   class="inline-flex items-center justify-center w-8 h-8 text-xs rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">
                    <i class="fa fa-chevron-right text-[10px]"></i>
                </a>
            @else
                <span class="inline-flex items-center justify-center w-8 h-8 text-xs rounded-lg border border-gray-200 text-gray-300 cursor-not-allowed select-none">
                    <i class="fa fa-chevron-right text-[10px]"></i>
                </span>
            @endif

        </div>
    </div>
@endif
