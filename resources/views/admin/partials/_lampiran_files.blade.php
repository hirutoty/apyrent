{{--
    Partial: _lampiran_files.blade.php
    Menampilkan daftar file lampiran dari source_data['temp_files']

    Cara pemakaian:
    @include('admin.partials._lampiran_files', [
        'tempFiles' => $pembayaran->source_data['temp_files'] ?? [],
        'label'     => 'Lampiran',   // opsional, default 'Lampiran'
        'compact'   => false,        // opsional, mode compact untuk index table
    ])
--}}
@php
    $tempFiles  = $tempFiles ?? [];
    $label      = $label ?? 'Lampiran';
    $compact    = $compact ?? false;

    // Kumpulkan semua file dari bukti + attachments
    $allFiles = array_merge(
        $tempFiles['bukti'] ?? [],
        $tempFiles['attachments'] ?? []
    );

    // Jika ada parts (service_part), ambil bukti per part
    if (!empty($tempFiles['parts'])) {
        foreach ($tempFiles['parts'] as $partFiles) {
            $allFiles = array_merge($allFiles, $partFiles['bukti'] ?? []);
        }
    }

    // Jika ada gps_items, ambil lampiran per item
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
@else
    {{-- Mode full: tampilkan daftar file --}}
    @if($totalFiles > 0)
        <div class="space-y-1.5">
            @foreach($allFiles as $file)
                @php
                    $ext      = strtolower($file['extension'] ?? pathinfo($file['original_name'] ?? $file['stored_name'] ?? '', PATHINFO_EXTENSION));
                    $name     = $file['original_name'] ?? $file['stored_name'] ?? 'file';
                    $path     = $file['path'] ?? null;
                    $url      = $path ? \Illuminate\Support\Facades\Storage::disk('public')->url($path) : null;

                    $isImage  = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                    $isPdf    = $ext === 'pdf';
                    $isVideo  = in_array($ext, ['mp4', 'mov', 'avi']);

                    $iconClass = match(true) {
                        $isImage => 'fa fa-image text-blue-400',
                        $isPdf   => 'fa fa-file-pdf text-red-400',
                        $isVideo => 'fa fa-file-video text-purple-400',
                        default  => 'fa fa-paperclip text-gray-400',
                    };

                    $size = isset($file['size']) ? round($file['size'] / 1024, 1) . ' KB' : null;
                @endphp
                @if($url)
                    <a href="{{ $url }}" target="_blank"
                        class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-600 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700 transition-colors max-w-xs"
                        title="{{ $name }}">
                        <i class="{{ $iconClass }} text-[11px] flex-shrink-0"></i>
                        <span class="truncate">{{ $name }}</span>
                        @if($size)
                            <span class="text-gray-400 flex-shrink-0 text-[10px]">{{ $size }}</span>
                        @endif
                    </a>
                @else
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-500 max-w-xs"
                        title="{{ $name }}">
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
