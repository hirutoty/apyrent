@props([
    'id' => 'chartContainer',
    'period' => '',
    'pieTitle' => 'Distribusi',
    'pieId' => 'pieChart',
    'barTitle' => 'Perbandingan',
    'barId' => 'barChart',
    'lineTitle' => 'Trend',
    'lineId' => 'lineChart',
    'showStats' => true,
    'statsData' => [],
    'containerClass' => '',
    'layout' => 'grid',
])

<div id="{{ $id }}" class="{{ $containerClass }}">

    @if($layout === 'stacked')
    {{-- ── STACKED LAYOUT: 3 chart full-width berurutan vertikal (Bar → Pie → Line) ── --}}

        {{-- BAR CHART — full width --}}
        <div class="chart-card chart-fade-in mb-4">
            <div class="chart-card-header">
                <div><h3 class="chart-card-title">{{ $barTitle }}</h3></div>
                <div class="chart-card-icon bg-green-50 text-green-600"><i class="fa fa-chart-bar"></i></div>
            </div>
            <div id="{{ $barId }}_scrollOuter" class="chart-scroll-outer" style="overflow-x:auto;overflow-y:hidden;">
                <div id="{{ $barId }}_scrollInner" style="min-width:100%;height:260px;position:relative;">
                    <canvas id="{{ $barId }}" class="chart-canvas" style="height:260px;"></canvas>
                </div>
            </div>
        </div>

        {{-- PIE/DONUT CHART — full width --}}
        <div class="chart-card chart-fade-in mb-4" style="animation-delay:0.1s">
            <div class="chart-card-header">
                <div><h3 class="chart-card-title">{{ $pieTitle }}</h3></div>
                <div class="chart-card-icon bg-blue-50 text-blue-600"><i class="fa fa-chart-pie"></i></div>
            </div>
            {{-- Pie pakai aspectRatio bukan fixed height --}}
            <div class="chart-canvas-wrapper--pie" style="max-width:380px;margin:0 auto;">
                <canvas id="{{ $pieId }}" class="chart-canvas"></canvas>
            </div>
        </div>

        {{-- LINE CHART — full width --}}
        <div class="chart-card chart-fade-in mb-6" style="animation-delay:0.2s">
            <div class="chart-card-header">
                <div><h3 class="chart-card-title">{{ $lineTitle }}</h3></div>
                <div class="chart-card-icon bg-purple-50 text-purple-600"><i class="fa fa-chart-line"></i></div>
            </div>
            <div id="{{ $lineId }}_scrollOuter" class="chart-scroll-outer" style="overflow-x:auto;overflow-y:hidden;">
                <div id="{{ $lineId }}_scrollInner" style="min-width:100%;height:260px;position:relative;">
                    <canvas id="{{ $lineId }}" class="chart-canvas" style="height:260px;"></canvas>
                </div>
            </div>
        </div>

    @elseif($layout === 'bar-top')
    {{-- ── BAR-TOP LAYOUT: bar full-width atas, pie+line berdampingan bawah ── --}}

        {{-- BAR CHART — full width --}}
        <div class="chart-card chart-fade-in mb-4">
            <div class="chart-card-header">
                <div><h3 class="chart-card-title">{{ $barTitle }}</h3></div>
                <div class="chart-card-icon bg-green-50 text-green-600"><i class="fa fa-chart-bar"></i></div>
            </div>
            <div id="{{ $barId }}_scrollOuter" class="chart-scroll-outer" style="overflow-x:auto;overflow-y:hidden;">
                <div id="{{ $barId }}_scrollInner" style="min-width:100%;height:220px;position:relative;">
                    <canvas id="{{ $barId }}" class="chart-canvas" style="height:220px;"></canvas>
                </div>
            </div>
        </div>

        {{-- PIE + LINE — dua kolom --}}
        <div class="grid grid-cols-2 gap-4 mb-6">

            {{-- PIE/DONUT CHART --}}
            <div class="chart-card chart-fade-in" style="animation-delay:0.1s">
                <div class="chart-card-header">
                    <div><h3 class="chart-card-title">{{ $pieTitle }}</h3></div>
                    <div class="chart-card-icon bg-blue-50 text-blue-600"><i class="fa fa-chart-pie"></i></div>
                </div>
                <div class="chart-canvas-wrapper--pie" style="max-width:300px;margin:0 auto;">
                    <canvas id="{{ $pieId }}" class="chart-canvas"></canvas>
                </div>
            </div>

            {{-- LINE CHART --}}
            <div class="chart-card chart-fade-in" style="animation-delay:0.2s">
                <div class="chart-card-header">
                    <div><h3 class="chart-card-title">{{ $lineTitle }}</h3></div>
                    <div class="chart-card-icon bg-purple-50 text-purple-600"><i class="fa fa-chart-line"></i></div>
                </div>
                <div class="chart-canvas-wrapper">
                    <canvas id="{{ $lineId }}" class="chart-canvas"></canvas>
                </div>
            </div>

        </div>

    @else
    {{-- ── DEFAULT GRID LAYOUT: 3 kolom sejajar ── --}}
    <div class="chart-grid grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

        {{-- PIE/DONUT CHART --}}
        <div class="chart-card chart-fade-in">
            <div class="chart-card-header">
                <div><h3 class="chart-card-title">{{ $pieTitle }}</h3></div>
                <div class="chart-card-icon bg-blue-50 text-blue-600"><i class="fa fa-chart-pie"></i></div>
            </div>
            <div class="chart-canvas-wrapper--pie" style="max-width:300px;margin:0 auto;">
                <canvas id="{{ $pieId }}" class="chart-canvas"></canvas>
            </div>
        </div>

        {{-- BAR CHART --}}
        <div class="chart-card chart-fade-in" style="animation-delay:0.1s">
            <div class="chart-card-header">
                <div><h3 class="chart-card-title">{{ $barTitle }}</h3></div>
                <div class="chart-card-icon bg-green-50 text-green-600"><i class="fa fa-chart-bar"></i></div>
            </div>
            <div class="chart-canvas-wrapper">
                <canvas id="{{ $barId }}" class="chart-canvas"></canvas>
            </div>
        </div>

        {{-- LINE CHART --}}
        <div class="chart-card chart-fade-in" style="animation-delay:0.2s">
            <div class="chart-card-header">
                <div><h3 class="chart-card-title">{{ $lineTitle }}</h3></div>
                <div class="chart-card-icon bg-purple-50 text-purple-600"><i class="fa fa-chart-line"></i></div>
            </div>
            <div class="chart-canvas-wrapper">
                <canvas id="{{ $lineId }}" class="chart-canvas"></canvas>
            </div>
        </div>

    </div>
    @endif

    {{-- STATS CARDS SECTION (Below Charts) --}}
    @if($showStats && !empty($statsData))
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach($statsData as $stat)
        <div class="chart-stat-card bg-white rounded-xl border border-gray-100 p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <div class="flex-1">
                    <p class="text-xs text-gray-500 font-medium">{{ $stat['label'] ?? 'Metric' }}</p>
                    <p class="chart-stat-value text-2xl font-bold mt-1" style="color: {{ $stat['color'] ?? '#4f6ef7' }}">
                        {{ $stat['value'] ?? '0' }}
                    </p>
                </div>
                @if(isset($stat['icon']))
                <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                     style="background-color: {{ $stat['iconBg'] ?? '#eef1ff' }}; color: {{ $stat['color'] ?? '#4f6ef7' }}">
                    <i class="{{ $stat['icon'] }}"></i>
                </div>
                @endif
            </div>
            @if(isset($stat['trend']))
            <div class="flex items-center gap-2 mt-2">
                <span class="chart-trend {{ $stat['trend'] > 0 ? 'chart-trend-up' : ($stat['trend'] < 0 ? 'chart-trend-down' : 'chart-trend-neutral') }}">
                    @if($stat['trend'] > 0)
                        <i class="fa fa-arrow-up text-xs"></i> +{{ abs($stat['trend']) }}%
                    @elseif($stat['trend'] < 0)
                        <i class="fa fa-arrow-down text-xs"></i> {{ $stat['trend'] }}%
                    @else
                        <i class="fa fa-minus text-xs"></i> 0%
                    @endif
                </span>
                <span class="text-xs text-gray-500">vs bulan lalu</span>
            </div>
            @endif
            @if(isset($stat['badge']))
            <div class="mt-2">
                <span class="chart-badge chart-badge-{{ $stat['badgeType'] ?? 'info' }}">
                    {{ $stat['badge'] }}
                </span>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

</div>

{{-- Add slot for custom content if needed --}}
{{ $slot ?? '' }}
