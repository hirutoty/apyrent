{{--
    Partial: _lampiran_files.blade.php
    Menampilkan daftar file lampiran dari source_data['temp_files']

    Cara pemakaian:
    @include('admin.partials._lampiran_files', [
        'tempFiles' => $pembayaran->source_data['temp_files'] ?? [],
        'parts'     => $pembayaran->source_data['parts'] ?? [],      // opsional, untuk mode per-item
        'gpsItems'  => $pembayaran->source_data['gps_items'] ?? [],  // opsional, untuk mode per-item GPS
        'label'     => 'Lampiran',   // opsional, default 'Lampiran'
        'compact'   => false,        // opsional, mode compact untuk index table
        'perItem'   => false,        // opsional, tampilkan lampiran dikelompokkan per item/part
    ])
--}}
@php
    $tempFiles  = $tempFiles ?? [];
    $label      = $label ?? 'Lampiran';
    $compact    = $compact ?? false;
    $perItem    = $perItem ?? false;
    $parts      = $parts ?? [];       // source_data['parts']
    $gpsItems   = $gpsItems ?? [];    // source_data['gps_items']

    // Kumpulkan semua file (untuk mode compact & mode flat)
    $allFiles = array_merge(
        $tempFiles['bukti'] ?? [],
        $tempFiles['attachments'] ?? []
    );
    if (!empty($tempFiles['parts'])) {
        foreach ($tempFiles['parts'] as $partFiles) {
            $allFiles = array_merge($allFiles, $partFiles['bukti'] ?? []);
        }
    }
    if (!empty($tempFiles['gps_items'])) {
        foreach ($tempFiles['gps_items'] as $gpsItem) {
            $allFiles = array_merge($allFiles, $gpsItem['lampiran'] ?? []);
        }
    }
    $totalFiles = count($allFiles);
@endphp

@if($compact)
    {{-- Mode compact: hanya badge jumlah file --}}
    @if($totalFiles > 0)
        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-semibold bg-blue-50 text-blue-600 border border-blue-200 rounded-full">
            <i class="fa fa-paperclip text-[9px]"></i>
            {{ $totalFiles }}
        </span>
    @else
        <span class="text-gray-300 text-xs">—</span>
    @endif

@elseif($perItem)
    {{-- Mode per-item: kelompokkan lampiran berdasarkan part/item --}}
    @php
        $tempParts   = $tempFiles['parts'] ?? [];
        $tempGps     = $tempFiles['gps_items'] ?? [];
        $globalBukti = array_merge($tempFiles['bukti'] ?? [], $tempFiles['attachments'] ?? []);

        // Cek apakah ada file sama sekali
        $hasAnyPerItemFile = false;
        foreach ($tempParts as $pf) {
            if (!empty($pf['bukti'])) { $hasAnyPerItemFile = true; break; }
        }
        if (!$hasAnyPerItemFile) {
            foreach ($tempGps as $gf) {
                if (!empty($gf['lampiran'])) { $hasAnyPerItemFile = true; break; }
            }
        }
        if (!$hasAnyPerItemFile && !empty($globalBukti)) $hasAnyPerItemFile = true;
    @endphp

    @if($hasAnyPerItemFile)
        <div class="space-y-3">
            {{-- Lampiran global (bukti + attachments bukan per-part) --}}
            @if(!empty($globalBukti))
            <div class="border border-gray-100 rounded-xl overflow-hidden">
                <div class="bg-gray-50 px-3 py-2 border-b border-gray-100">
                    <span class="text-xs font-semibold text-gray-500">
                        <i class="fa fa-paperclip text-gray-400 mr-1 text-[10px]"></i>
                        Lampiran Global
                    </span>
                </div>
                <div class="p-3 flex flex-wrap gap-1.5">
                    @foreach($globalBukti as $file)
                        @include('admin.partials._lampiran_file_link', ['file' => $file])
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Lampiran per-part (service part / service incident) --}}
            @foreach($tempParts as $idx => $partFiles)
                @if(!empty($partFiles['bukti']))
                @php
                    $partInfo     = $parts[(int)$idx] ?? null;
                    $partName     = $partInfo['nama_part'] ?? ('Part #' . ($idx + 1));
                    $partCategory = $partInfo['category_nama'] ?? ($partInfo['nama_category_baru'] ?? null);
                    $partBiaya    = isset($partInfo['biaya']) ? 'Rp ' . number_format($partInfo['biaya'], 0, ',', '.') : null;
                @endphp
                <div class="border border-orange-100 rounded-xl overflow-hidden">
                    <div class="bg-orange-50 px-3 py-2 border-b border-orange-100 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5 min-w-0">
                            <i class="bi bi-tools text-orange-500 text-[10px] flex-shrink-0"></i>
                            <span class="text-xs font-semibold text-gray-700 truncate">{{ $partName }}</span>
                            @if($partCategory)
                                <span class="text-[10px] font-semibold bg-orange-100 text-orange-600 px-1.5 py-0.5 rounded flex-shrink-0">{{ $partCategory }}</span>
                            @endif
                        </div>
                        @if($partBiaya)
                            <span class="text-[11px] font-bold text-emerald-600 flex-shrink-0">{{ $partBiaya }}</span>
                        @endif
                    </div>
                    <div class="p-3 flex flex-wrap gap-1.5">
                        @foreach($partFiles['bukti'] as $file)
                            @php
                                $ext      = strtolower($file['extension'] ?? pathinfo($file['original_name'] ?? $file['stored_name'] ?? '', PATHINFO_EXTENSION));
                                $name     = $file['original_name'] ?? $file['stored_name'] ?? 'file';
                                $path     = $file['path'] ?? null;
                                $url      = $path ? \Illuminate\Support\Facades\Storage::disk('public')->url($path) : null;
                                $isImage  = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                $isPdf    = $ext === 'pdf';
                                $isVideo  = in_array($ext, ['mp4', 'mov', 'avi']);
                                $iconClass = $isImage ? 'fa fa-image text-blue-400' : ($isPdf ? 'fa fa-file-pdf text-red-400' : ($isVideo ? 'fa fa-file-video text-purple-400' : 'fa fa-paperclip text-gray-400'));
                                $size     = isset($file['size']) ? round($file['size'] / 1024, 1) . ' KB' : null;
                            @endphp
                            @if($url)
                                <a href="{{ $url }}" target="_blank"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-600 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700 transition-colors max-w-xs"
                                    title="{{ $name }}">
                                    <i class="{{ $iconClass }} text-[11px] flex-shrink-0"></i>
                                    <span class="truncate">{{ $name }}</span>
                                    @if($size)<span class="text-gray-400 flex-shrink-0 text-[10px]">{{ $size }}</span>@endif
                                </a>
                            @else
                                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-500 max-w-xs" title="{{ $name }}">
                                    <i class="{{ $iconClass }} text-[11px] flex-shrink-0"></i>
                                    <span class="truncate">{{ $name }}</span>
                                </span>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif
            @endforeach

            {{-- Lampiran per-item GPS --}}
            @foreach($tempGps as $idx => $gpsItemFiles)
                @if(!empty($gpsItemFiles['lampiran']))
                @php
                    $gpsInfo = $gpsItems[(int)$idx] ?? null;
                    $gpsName = $gpsInfo['nama'] ?? ($gpsItemFiles['nama'] ?? ('Item GPS #' . ((int)$idx + 1)));
                @endphp
                <div class="border border-green-100 rounded-xl overflow-hidden">
                    <div class="bg-green-50 px-3 py-2 border-b border-green-100 flex items-center gap-1.5">
                        <i class="fa fa-satellite-dish text-green-500 text-[10px] flex-shrink-0"></i>
                        <span class="text-xs font-semibold text-gray-700">{{ $gpsName }}</span>
                    </div>
                    <div class="p-3 flex flex-wrap gap-1.5">
                        @foreach($gpsItemFiles['lampiran'] as $file)
                            @php
                                $ext      = strtolower($file['extension'] ?? pathinfo($file['original_name'] ?? $file['stored_name'] ?? '', PATHINFO_EXTENSION));
                                $name     = $file['original_name'] ?? $file['stored_name'] ?? 'file';
                                $path     = $file['path'] ?? null;
                                $url      = $path ? \Illuminate\Support\Facades\Storage::disk('public')->url($path) : null;
                                $isImage  = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                $isPdf    = $ext === 'pdf';
                                $isVideo  = in_array($ext, ['mp4', 'mov', 'avi']);
                                $iconClass = $isImage ? 'fa fa-image text-blue-400' : ($isPdf ? 'fa fa-file-pdf text-red-400' : ($isVideo ? 'fa fa-file-video text-purple-400' : 'fa fa-paperclip text-gray-400'));
                                $size     = isset($file['size']) ? round($file['size'] / 1024, 1) . ' KB' : null;
                            @endphp
                            @if($url)
                                <a href="{{ $url }}" target="_blank"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-600 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700 transition-colors max-w-xs"
                                    title="{{ $name }}">
                                    <i class="{{ $iconClass }} text-[11px] flex-shrink-0"></i>
                                    <span class="truncate">{{ $name }}</span>
                                    @if($size)<span class="text-gray-400 flex-shrink-0 text-[10px]">{{ $size }}</span>@endif
                                </a>
                            @else
                                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-500 max-w-xs" title="{{ $name }}">
                                    <i class="{{ $iconClass }} text-[11px] flex-shrink-0"></i>
                                    <span class="truncate">{{ $name }}</span>
                                </span>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    @else
        <p class="text-xs text-gray-400 italic">Tidak ada lampiran</p>
    @endif

@else
    {{-- Mode flat/default: tampilkan semua file dalam satu daftar --}}
    @if($totalFiles > 0)
        <div class="flex flex-wrap gap-1.5">
            @foreach($allFiles as $file)
                @php
                    $ext      = strtolower($file['extension'] ?? pathinfo($file['original_name'] ?? $file['stored_name'] ?? '', PATHINFO_EXTENSION));
                    $name     = $file['original_name'] ?? $file['stored_name'] ?? 'file';
                    $path     = $file['path'] ?? null;
                    $url      = $path ? \Illuminate\Support\Facades\Storage::disk('public')->url($path) : null;
                    $isImage  = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                    $isPdf    = $ext === 'pdf';
                    $isVideo  = in_array($ext, ['mp4', 'mov', 'avi']);
                    $iconClass = $isImage ? 'fa fa-image text-blue-400' : ($isPdf ? 'fa fa-file-pdf text-red-400' : ($isVideo ? 'fa fa-file-video text-purple-400' : 'fa fa-paperclip text-gray-400'));
                    $size     = isset($file['size']) ? round($file['size'] / 1024, 1) . ' KB' : null;
                @endphp
                @if($url)
                    <a href="{{ $url }}" target="_blank"
                        class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-600 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700 transition-colors max-w-xs"
                        title="{{ $name }}">
                        <i class="{{ $iconClass }} text-[11px] flex-shrink-0"></i>
                        <span class="truncate">{{ $name }}</span>
                        @if($size)<span class="text-gray-400 flex-shrink-0 text-[10px]">{{ $size }}</span>@endif
                    </a>
                @else
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-500 max-w-xs" title="{{ $name }}">
                        <i class="{{ $iconClass }} text-[11px] flex-shrink-0"></i>
                        <span class="truncate">{{ $name }}</span>
                    </span>
                @endif
            @endforeach
        </div>
    @else
        <p class="text-xs text-gray-400 italic">Tidak ada lampiran</p>
    @endif
@endif
