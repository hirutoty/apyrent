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

        {{-- Filter Buttons + Category --}}
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

            <button 
                type="button"
                data-filter="year"
                class="chart-filter-btn px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200
                    {{ $defaultFilter === 'year' ? 'bg-blue-600 text-white active' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                <i class="fa fa-calendar text-xs mr-1"></i>
                Tahun Ini
            </button>

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
            locale: {
                firstDayOfWeek: 1
            },
            onChange: function(selectedDates, dateStr, instance) {
                // Update end date min date
                const endDatePicker = document.querySelector(`#${filterId}_endDate`)._flatpickr;
                if (endDatePicker && selectedDates[0]) {
                    endDatePicker.set('minDate', selectedDates[0]);
                }
            }
        });

        flatpickr(`#${filterId}_endDate`, {
            dateFormat: 'd-m-Y',
            maxDate: 'today',
            locale: {
                firstDayOfWeek: 1
            }
        });
    }
    @endif

    // Handle filter button clicks
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filterType = this.getAttribute('data-filter');
            
            // Skip if custom (handled separately)
            if (filterType === 'custom') return;
            
            // Update active state
            filterButtons.forEach(btn => {
                if (btn.getAttribute('data-filter') !== 'custom') {
                    btn.classList.remove('bg-blue-600', 'text-white', 'active');
                    btn.classList.add('bg-gray-100', 'text-gray-700');
                }
            });
            
            this.classList.remove('bg-gray-100', 'text-gray-700');
            this.classList.add('bg-blue-600', 'text-white', 'active');
            
            // Hide custom range if visible
            @if($showCustomRange)
            const customRange = document.getElementById(`${filterId}_customRange`);
            if (customRange) {
                customRange.classList.add('hidden');
                const customBtn = document.querySelector(`#${filterId} [data-filter="custom"]`);
                if (customBtn) {
                    customBtn.classList.remove('bg-blue-600', 'text-white', 'active');
                    customBtn.classList.add('bg-gray-100', 'text-gray-700');
                }
            }
            @endif
            
            // Dispatch custom event
            const event = new CustomEvent('chartFilterChange', {
                detail: {
                    filterId: filterId,
                    filterType: filterType,
                    startDate: null,
                    endDate: null,
                    categoryId: document.getElementById(`${filterId}_categoryFilter`)?.value ?? ''
                }
            });
            document.dispatchEvent(event);
        });
    });

    // Auto-dispatch default filter saat halaman load agar chart langsung terinisialisasi
    const defaultFilterType = '{{ $defaultFilter }}';
    if (defaultFilterType && defaultFilterType !== 'custom') {
        // Tunggu DOM + script lain selesai, lalu dispatch
        document.addEventListener('DOMContentLoaded', function () {
            document.dispatchEvent(new CustomEvent('chartFilterChange', {
                detail: {
                    filterId: filterId,
                    filterType: defaultFilterType,
                    startDate: null,
                    endDate: null,
                    categoryId: ''
                }
            }));
        });
    }
})();

@if($showCustomRange)
// Toggle custom range visibility
function toggleCustomRange(filterId) {
    const customRange = document.getElementById(`${filterId}_customRange`);
    const customBtn = document.querySelector(`#${filterId} [data-filter="custom"]`);
    const otherButtons = document.querySelectorAll(`#${filterId} .chart-filter-btn:not([data-filter="custom"])`);
    
    if (customRange.classList.contains('hidden')) {
        customRange.classList.remove('hidden');
        customBtn.classList.remove('bg-gray-100', 'text-gray-700');
        customBtn.classList.add('bg-blue-600', 'text-white', 'active');
        
        // Deactivate other buttons
        otherButtons.forEach(btn => {
            btn.classList.remove('bg-blue-600', 'text-white', 'active');
            btn.classList.add('bg-gray-100', 'text-gray-700');
        });
    } else {
        customRange.classList.add('hidden');
        customBtn.classList.remove('bg-blue-600', 'text-white', 'active');
        customBtn.classList.add('bg-gray-100', 'text-gray-700');
    }
}

// Apply custom date range
function applyCustomRange(filterId) {
    const startDateInput = document.getElementById(`${filterId}_startDate`);
    const endDateInput = document.getElementById(`${filterId}_endDate`);
    
    const startDate = startDateInput.value;
    const endDate = endDateInput.value;
    
    if (!startDate || !endDate) {
        alert('Silakan pilih tanggal mulai dan tanggal akhir');
        return;
    }
    
    const event = new CustomEvent('chartFilterChange', {
        detail: {
            filterId: filterId,
            filterType: 'custom',
            startDate: startDate,
            endDate: endDate,
            categoryId: document.getElementById(`${filterId}_categoryFilter`)?.value ?? ''
        }
    });
    document.dispatchEvent(event);
}
@endif

// Re-dispatch current active filter + new category
function applyChartCategoryFilter(filterId) {
    const categorySelect = document.getElementById(`${filterId}_categoryFilter`);
    const categoryId     = categorySelect ? categorySelect.value : '';
    const activeBtn      = document.querySelector(`#${filterId} .chart-filter-btn.active`);
    const filterType     = activeBtn ? activeBtn.getAttribute('data-filter') : 'month';

    let startDate = null, endDate = null;
    if (filterType === 'custom') {
        startDate = document.getElementById(`${filterId}_startDate`)?.value ?? null;
        endDate   = document.getElementById(`${filterId}_endDate`)?.value ?? null;
    }

    document.dispatchEvent(new CustomEvent('chartFilterChange', {
        detail: { filterId, filterType, startDate, endDate, categoryId }
    }));
}
</script>
@endpush
