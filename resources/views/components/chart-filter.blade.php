@props([
    'id' => 'chartFilter',
    'defaultFilter' => 'month',
    'showCustomRange' => true,
    'containerClass' => '',
    'categories' => [],        // array of {id, nama} untuk filter kategori
    'showCategoryFilter' => false,
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl border border-gray-100 p-4 ' . $containerClass]) }} id="{{ $id }}">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

        {{-- Filter Label --}}
        <div class="flex items-center gap-2">
            <i class="fa fa-filter text-gray-400 text-sm"></i>
            <span class="text-sm font-semibold text-gray-700">Filter Periode:</span>
        </div>

        {{-- Filter Buttons + Year Picker + Category --}}
        <div class="flex flex-wrap items-center gap-2">

            {{-- Quick Filter Buttons --}}
            <button
                type="button"
                data-filter="today"
                class="chart-filter-btn px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200
                    {{ $defaultFilter === 'today' ? 'bg-blue-600 text-white active' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                <i class="fa fa-calendar-day text-xs mr-1"></i>
                Hari Ini
            </button>

            <button
                type="button"
                data-filter="week"
                class="chart-filter-btn px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200
                    {{ $defaultFilter === 'week' ? 'bg-blue-600 text-white active' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                <i class="fa fa-calendar-week text-xs mr-1"></i>
                Minggu Ini
            </button>

            <button
                type="button"
                data-filter="month"
                class="chart-filter-btn px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200
                    {{ $defaultFilter === 'month' ? 'bg-blue-600 text-white active' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                <i class="fa fa-calendar-alt text-xs mr-1"></i>
                Bulan Ini
            </button>

            {{-- Year Picker Dropdown (menggantikan tombol "Tahun Ini") --}}
            @php $currentYear = (int) date('Y'); @endphp
            <div class="relative flex items-center">
                <i class="fa fa-calendar text-gray-400 text-xs absolute left-3 pointer-events-none z-10"></i>
                <select
                    id="{{ $id }}_yearSelect"
                    onchange="applyYearFilter('{{ $id }}')"
                    class="chart-filter-year-select pl-8 pr-7 py-2 rounded-lg text-sm font-medium border cursor-pointer appearance-none transition-all duration-200
                        {{ in_array($defaultFilter, ['year','specific_year']) ? 'bg-blue-600 text-white border-blue-600 active' : 'bg-gray-100 text-gray-700 border-gray-200 hover:bg-gray-200' }}"
                    style="min-width:110px">
                    @for ($y = $currentYear; $y >= 2000; $y--)
                        <option value="{{ $y }}"
                            {{ in_array($defaultFilter, ['year','specific_year']) && $y === $currentYear ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
                <i class="fa fa-chevron-down text-gray-400 text-[10px] absolute right-2.5 pointer-events-none"></i>
            </div>

            @if($showCustomRange)
            <button
                type="button"
                data-filter="custom"
                onclick="toggleCustomRange('{{ $id }}')"
                class="chart-filter-btn px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200
                    {{ $defaultFilter === 'custom' ? 'bg-blue-600 text-white active' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                <i class="fa fa-calendar-range text-xs mr-1"></i>
                Custom
            </button>
            @endif

            {{-- ── CATEGORY FILTER DROPDOWN ── --}}
            @if($showCategoryFilter && count($categories) > 0)
            <div class="flex items-center gap-1.5 ml-1 pl-3 border-l border-gray-200">
                <i class="fa fa-tags text-gray-400 text-xs"></i>
                <select
                    id="{{ $id }}_categoryFilter"
                    onchange="applyChartCategoryFilter('{{ $id }}')"
                    class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white text-gray-700
                           focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400
                           hover:border-gray-300 transition-colors cursor-pointer">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id ?? $cat['id'] }}">
                            {{ $cat->nama ?? $cat['nama'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

        </div>
    </div>

    @if($showCustomRange)
    {{-- Custom Date Range --}}
    <div id="{{ $id }}_customRange" class="mt-4 pt-4 border-t border-gray-200 hidden">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Tanggal Mulai</label>
                <div class="relative">
                    <input
                        type="text"
                        id="{{ $id }}_startDate"
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Pilih tanggal mulai"
                        readonly>
                    <i class="fa fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                </div>
            </div>
            <div class="flex-1">
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Tanggal Akhir</label>
                <div class="relative">
                    <input
                        type="text"
                        id="{{ $id }}_endDate"
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Pilih tanggal akhir"
                        readonly>
                    <i class="fa fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                </div>
            </div>
            <div class="flex items-end">
                <button
                    type="button"
                    onclick="applyCustomRange('{{ $id }}')"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                    <i class="fa fa-check mr-1"></i>
                    Terapkan
                </button>
            </div>
        </div>
    </div>
    @endif

</div>

@push('scripts')
<script>
// Chart Filter Handler
(function() {
    const filterId = '{{ $id }}';
    const filterButtons = document.querySelectorAll(`#${filterId} .chart-filter-btn`);

    // Initialize date pickers if custom range enabled
    @if($showCustomRange)
    if (typeof flatpickr !== 'undefined') {
        flatpickr(`#${filterId}_startDate`, {
            dateFormat: 'd-m-Y',
            maxDate: 'today',
            locale: { firstDayOfWeek: 1 },
            onChange: function(selectedDates) {
                const endPicker = document.querySelector(`#${filterId}_endDate`)._flatpickr;
                if (endPicker && selectedDates[0]) {
                    endPicker.set('minDate', selectedDates[0]);
                }
            }
        });
        flatpickr(`#${filterId}_endDate`, {
            dateFormat: 'd-m-Y',
            maxDate: 'today',
            locale: { firstDayOfWeek: 1 }
        });
    }
    @endif

    // Helper: deactivate all buttons + year select
    function deactivateAll() {
        filterButtons.forEach(btn => {
            if (btn.getAttribute('data-filter') !== 'custom') {
                btn.classList.remove('bg-blue-600', 'text-white', 'active');
                btn.classList.add('bg-gray-100', 'text-gray-700');
            }
        });
        const yearSel = document.getElementById(`${filterId}_yearSelect`);
        if (yearSel) {
            yearSel.classList.remove('bg-blue-600', 'text-white', 'border-blue-600', 'active');
            yearSel.classList.add('bg-gray-100', 'text-gray-700', 'border-gray-200');
        }
    }

    // Helper: hide custom range panel + deactivate custom btn
    function hideCustomRange() {
        @if($showCustomRange)
        const customRange = document.getElementById(`${filterId}_customRange`);
        if (customRange) customRange.classList.add('hidden');
        const customBtn = document.querySelector(`#${filterId} [data-filter="custom"]`);
        if (customBtn) {
            customBtn.classList.remove('bg-blue-600', 'text-white', 'active');
            customBtn.classList.add('bg-gray-100', 'text-gray-700');
        }
        @endif
    }

    // Handle quick filter button clicks (today / week / month)
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filterType = this.getAttribute('data-filter');
            if (filterType === 'custom') return; // handled by toggleCustomRange

            deactivateAll();
            hideCustomRange();

            this.classList.remove('bg-gray-100', 'text-gray-700');
            this.classList.add('bg-blue-600', 'text-white', 'active');

            document.dispatchEvent(new CustomEvent('chartFilterChange', {
                detail: {
                    filterId,
                    filterType,
                    startDate: null,
                    endDate: null,
                    specificYear: null,
                    categoryId: document.getElementById(`${filterId}_categoryFilter`)?.value ?? ''
                }
            }));
        });
    });

    // Auto-dispatch default filter on page load
    const defaultFilterType = '{{ $defaultFilter }}';
    const defaultYear = {{ $currentYear }};
    if (defaultFilterType && defaultFilterType !== 'custom') {
        document.addEventListener('DOMContentLoaded', function () {
            const isYearFilter = defaultFilterType === 'year' || defaultFilterType === 'specific_year';
            document.dispatchEvent(new CustomEvent('chartFilterChange', {
                detail: {
                    filterId,
                    filterType: isYearFilter ? 'specific_year' : defaultFilterType,
                    startDate: null,
                    endDate: null,
                    specificYear: isYearFilter ? defaultYear : null,
                    categoryId: ''
                }
            }));
        });
    }
})();

// Apply year filter from dropdown
function applyYearFilter(filterId) {
    const yearSel = document.getElementById(`${filterId}_yearSelect`);
    if (!yearSel) return;
    const year = parseInt(yearSel.value, 10);

    // Deactivate all buttons
    document.querySelectorAll(`#${filterId} .chart-filter-btn`).forEach(btn => {
        btn.classList.remove('bg-blue-600', 'text-white', 'active');
        btn.classList.add('bg-gray-100', 'text-gray-700');
    });
    // Activate year select
    yearSel.classList.remove('bg-gray-100', 'text-gray-700', 'border-gray-200');
    yearSel.classList.add('bg-blue-600', 'text-white', 'border-blue-600', 'active');

    // Hide custom range
    const customRange = document.getElementById(`${filterId}_customRange`);
    if (customRange) customRange.classList.add('hidden');

    document.dispatchEvent(new CustomEvent('chartFilterChange', {
        detail: {
            filterId,
            filterType: 'specific_year',
            startDate: null,
            endDate: null,
            specificYear: year,
            categoryId: document.getElementById(`${filterId}_categoryFilter`)?.value ?? ''
        }
    }));
}

@if($showCustomRange)
// Toggle custom range visibility
function toggleCustomRange(filterId) {
    const customRange = document.getElementById(`${filterId}_customRange`);
    const customBtn   = document.querySelector(`#${filterId} [data-filter="custom"]`);
    const otherBtns   = document.querySelectorAll(`#${filterId} .chart-filter-btn:not([data-filter="custom"])`);
    const yearSel     = document.getElementById(`${filterId}_yearSelect`);

    if (customRange.classList.contains('hidden')) {
        customRange.classList.remove('hidden');
        customBtn.classList.remove('bg-gray-100', 'text-gray-700');
        customBtn.classList.add('bg-blue-600', 'text-white', 'active');
        otherBtns.forEach(btn => {
            btn.classList.remove('bg-blue-600', 'text-white', 'active');
            btn.classList.add('bg-gray-100', 'text-gray-700');
        });
        if (yearSel) {
            yearSel.classList.remove('bg-blue-600', 'text-white', 'border-blue-600', 'active');
            yearSel.classList.add('bg-gray-100', 'text-gray-700', 'border-gray-200');
        }
    } else {
        customRange.classList.add('hidden');
        customBtn.classList.remove('bg-blue-600', 'text-white', 'active');
        customBtn.classList.add('bg-gray-100', 'text-gray-700');
    }
}

// Apply custom date range
function applyCustomRange(filterId) {
    const startDate = document.getElementById(`${filterId}_startDate`)?.value;
    const endDate   = document.getElementById(`${filterId}_endDate`)?.value;

    if (!startDate || !endDate) {
        alert('Silakan pilih tanggal mulai dan tanggal akhir');
        return;
    }

    document.dispatchEvent(new CustomEvent('chartFilterChange', {
        detail: {
            filterId,
            filterType: 'custom',
            startDate,
            endDate,
            specificYear: null,
            categoryId: document.getElementById(`${filterId}_categoryFilter`)?.value ?? ''
        }
    }));
}
@endif

// Re-dispatch current active filter + new category
function applyChartCategoryFilter(filterId) {
    const categoryId = document.getElementById(`${filterId}_categoryFilter`)?.value ?? '';
    const activeBtn  = document.querySelector(`#${filterId} .chart-filter-btn.active`);
    const yearSel    = document.getElementById(`${filterId}_yearSelect`);
    const yearActive = yearSel?.classList.contains('active');

    let filterType   = activeBtn ? activeBtn.getAttribute('data-filter') : 'month';
    let specificYear = null;
    let startDate = null, endDate = null;

    if (yearActive) {
        filterType   = 'specific_year';
        specificYear = parseInt(yearSel.value, 10);
    } else if (filterType === 'custom') {
        startDate = document.getElementById(`${filterId}_startDate`)?.value ?? null;
        endDate   = document.getElementById(`${filterId}_endDate`)?.value ?? null;
    }

    document.dispatchEvent(new CustomEvent('chartFilterChange', {
        detail: { filterId, filterType, startDate, endDate, specificYear, categoryId }
    }));
}
</script>
@endpush
