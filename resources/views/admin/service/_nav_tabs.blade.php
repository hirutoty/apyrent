{{-- Nav Tabs untuk Service Module --}}
<div>
    <nav class="inline-flex gap-1 bg-gray-100 rounded-xl p-1">
        @php
            $navItems = [
                ['label' => 'Service History',  'url' => '/admin/service-history',    'icon' => 'bi bi-clock-history', 'role' => null],
                ['label' => 'Service Asuransi', 'url' => '/admin/service-asuransi',   'icon' => 'bi bi-shield-fill-check', 'role' => null],
                ['label' => 'Service Incident', 'url' => '/admin/service-incident',   'icon' => 'bi bi-exclamation-triangle-fill', 'role' => null],
                ['label' => 'Reminder Service', 'url' => '/admin/reminder-service',   'icon' => 'bi bi-bell-fill', 'role' => null],
                ['label' => 'Ceklist Kondisi',  'url' => '/admin/ceklist-kondisi',    'icon' => 'bi bi-clipboard-check-fill', 'role' => null],
                ['label' => 'Kategori Service', 'url' => '/admin/service-categories', 'icon' => 'bi bi-tags-fill', 'role' => 'superadmin'],
            ];
        @endphp
        @foreach ($navItems as $item)
            @if(!isset($item['role']) || (isset($item['role']) && auth()->user()->role === $item['role']))
                @php 
                    $isActiveTab = request()->is(ltrim($item['url'], '/')) || request()->is(ltrim($item['url'], '/') . '/*');
                    // Override dengan parameter $activeTab jika ada
                    if (isset($activeTab)) {
                        $isActiveTab = match($activeTab) {
                            'history' => $item['url'] === '/admin/service-history',
                            'asuransi' => $item['url'] === '/admin/service-asuransi',
                            'incident' => $item['url'] === '/admin/service-incident',
                            'reminder' => $item['url'] === '/admin/reminder-service',
                            'ceklist' => $item['url'] === '/admin/ceklist-kondisi',
                            'categories' => $item['url'] === '/admin/service-categories',
                            default => $isActiveTab
                        };
                    }
                @endphp
                <a href="{{ $item['url'] }}"
                    class="flex items-center gap-2 px-4 py-2 text-sm font-semibold whitespace-nowrap rounded-lg transition-all duration-150
                        {{ $isActiveTab ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-white/60' }}">
                    <i class="{{ $item['icon'] }}"></i> {{ $item['label'] }}
                </a>
            @endif
        @endforeach
    </nav>
</div>
