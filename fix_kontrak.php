<?php
$file = __DIR__ . '/resources/views/admin/kontrak/index.blade.php';
$content = file_get_contents($file);

// ========== Task 3: Tambah kolom Kendaraan di header tabel ==========
$content = preg_replace(
    '/(<th data-col="col-penawaran"[^>]+>Penawaran<\/th>)\s*(<th data-col="col-tanggal")/s',
    '$1' . "\n" . '                            <th data-col="col-kendaraan"       class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-3">Kendaraan</th>' . "\n" . '                            $2',
    $content
);

// ========== Task 3: Tambah kolom toggle kendaraan di dropdown ==========
$content = str_replace(
    "'col-penawaran'       => 'Penawaran',",
    "'col-penawaran'       => 'Penawaran',\n            'col-kendaraan'       => 'Kendaraan',",
    $content
);

// ========== Task 3: Tambah cell Kendaraan setelah cell Penawaran ==========
// Temukan cell penawaran di tbody
$old_cell = <<<'OLD'
                                <td class="px-4 py-3.5 text-sm text-gray-600" data-col="col-penawaran">
                                    {{ $k->penawaran->no_penawaran ?? '
OLD;
// Pendekatan berbeda: pakai marker unik
$content = preg_replace(
    '/(<td class="px-4 py-3.5 text-sm text-gray-600" data-col="col-penawaran">.*?<\/td>)\s*(<td class="px-4 py-3.5 text-sm text-gray-600" data-col="col-tanggal">)/s',
    '$1' . "\n" . <<<'NEW'

                                {{-- Task 3: Kolom Kendaraan --}}
                                <td class="px-4 py-3.5" data-col="col-kendaraan">
                                    @if ($k->penawaran && $k->penawaran->items->isNotEmpty())
                                        <div class="flex flex-col gap-1">
                                            @foreach ($k->penawaran->items as $item)
                                                @if ($item->kendaraan)
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="text-xs font-semibold text-gray-800">{{ $item->kendaraan->merk }}</span>
                                                        <span class="font-mono text-xs text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded">{{ $item->kendaraan->nopol }}</span>
                                                        @php
                                                            $stKend = $item->kendaraan->status_kendaraan ?? '';
                                                            $stColor = match($stKend) {
                                                                'tersedia' => 'bg-green-100 text-green-700',
                                                                'disewa'   => 'bg-blue-100 text-blue-700',
                                                                default    => 'bg-gray-100 text-gray-600',
                                                            };
                                                        @endphp
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-semibold {{ $stColor }}">
                                                            {{ ucfirst($stKend) ?: '-' }}
                                                        </span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>

NEW
    . '                                $2',
    $content
);

// ========== Task 3: Perbaiki badge reminder di kolom perjanjian ==========
// Logika lama salah: showReminder sudah termasuk isExpired
// Ganti kondisi @if ($k->showReminder) menjadi logika yang benar
$content = str_replace(
    '@if ($k->showReminder)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-100 text-red-700 text-xs font-semibold w-fit">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                                Terlambat {{ abs($k->sisaHari) }} hari
                                            </span>
                                        @elseif ($k->isExpired)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700 text-xs font-semibold w-fit animate-pulse">
                                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>
                                                @if ($k->sisaHari == 0) Jatuh Tempo Hari Ini
                                                @elseif ($k->sisaHari == 1) Jatuh Tempo Besok
                                                @else Jatuh Tempo {{ $k->sisaHari }} hari lagi
                                                @endif
                                            </span>
                                        @endif',
    '@if ($k->showReminder && $k->isExpired)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-100 text-red-700 text-xs font-semibold w-fit">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                                Terlambat {{ abs($k->sisaHari) }} hari
                                            </span>
                                        @elseif ($k->showReminder && $k->isSoon)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700 text-xs font-semibold w-fit animate-pulse">
                                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>
                                                @if ($k->sisaHari == 0) Jatuh Tempo Hari Ini
                                                @elseif ($k->sisaHari == 1) Jatuh Tempo Besok
                                                @else Berakhir {{ $k->sisaHari }} hari lagi
                                                @endif
                                            </span>
                                        @endif',
    $content
);

// ========== Task 3: Fix colspan di empty state ==========
$content = str_replace(
    'colspan="11" class="text-center py-12 text-gray-400 text-sm"',
    'colspan="12" class="text-center py-12 text-gray-400 text-sm"',
    $content
);

file_put_contents($file, $content);

echo "Checks:\n";
echo "  col-kendaraan header: " . (strpos($content, 'col-kendaraan') !== false ? "OK" : "FAIL") . "\n";
echo "  Badge showReminder&&isExpired: " . (strpos($content, 'showReminder && $k->isExpired') !== false ? "OK" : "FAIL") . "\n";
echo "  Badge showReminder&&isSoon: " . (strpos($content, 'showReminder && $k->isSoon') !== false ? "OK" : "FAIL") . "\n";
echo "Done!\n";
