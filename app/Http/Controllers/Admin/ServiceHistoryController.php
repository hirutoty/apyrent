<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceHistory;
use App\Models\ServicePart;
use App\Models\ServiceCategory;
use App\Models\ServiceDetail;
use App\Models\Kendaraan;
use App\Models\Keuangan;
use App\Models\Bukubesar;
use App\Models\Setting;
use App\Models\Attachment;
use App\Models\ReminderService;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ServiceHistoryController extends Controller
{
    public function index(Request $request)
    {
        $bulan        = $request->bulan ?? now()->format('Y-m');
        $search       = $request->search;
        $kendaraanId  = $request->kendaraan_id;

        $data = ServiceHistory::with([
                'kendaraan.jenis',
                'attachments',
                'parts.category',
            ])
            ->when($bulan, fn($q) => $q->whereRaw("DATE_FORMAT(tanggal_service,'%Y-%m') = ?", [$bulan]))
            ->when($kendaraanId, fn($q) => $q->where('kendaraan_id', $kendaraanId))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('keluhan', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhere('kilometer', 'like', "%{$search}%")
                        ->orWhereHas('kendaraan', fn($k) =>
                            $k->where('merk', 'like', "%{$search}%")
                              ->orWhere('nopol', 'like', "%{$search}%")
                        )
                        ->orWhereHas('parts', fn($p) =>
                            $p->where('nama_part', 'like', "%{$search}%")
                              ->orWhere('part_number', 'like', "%{$search}%")
                              ->orWhere('serial_number', 'like', "%{$search}%")
                              ->orWhereHas('category', fn($c) =>
                                  $c->where('nama', 'like', "%{$search}%")
                              )
                        );
                });
            })
            ->latest()
            ->paginate(15)->withQueryString();

        $kendaraan  = Kendaraan::whereNotIn('status_kendaraan', ['disewa'])
            ->orderBy('merk')
            ->get();

        $categories = ServiceCategory::orderBy('nama')->get();

        // Summary: status limit per kendaraan (untuk cards)
        $allKendaraan = Kendaraan::all();
        $aman = 0; $hampir = 0; $habis = 0;
        foreach ($allKendaraan as $k) {
            $limit = $k->limit_biaya_bulanan_service ?? 0;
            if ($limit <= 0) continue;
            $total = ServiceHistory::where('kendaraan_id', $k->id)
                ->whereRaw("DATE_FORMAT(tanggal_service, '%Y-%m') = ?", [$bulan])
                ->sum('total_biaya');
            $persen = ($total / $limit) * 100;
            if ($persen >= 100) $habis++;
            elseif ($persen >= 70) $hampir++;
            else $aman++;
        }

        return view('admin.service.service_history', [
            'data'       => $data,
            'kendaraan'  => $kendaraan,
            'categories' => $categories,
            'bulan'      => $bulan,
            'aman'       => $aman,
            'hampir'     => $hampir,
            'habis'      => $habis,
        ]);
    }

    /**
     * Show create form — pre-filled dari reminder jika ada ?from_reminder=ID
     */
    public function create(Request $request)
    {
        $kendaraan  = Kendaraan::whereNotIn('status_kendaraan', ['disewa'])->orderBy('merk')->get();
        $categories = ServiceCategory::orderBy('nama')->get();
        $prefill    = null;

        if ($request->from_reminder) {
            $reminder = ReminderService::with(['servicePart.category', 'kendaraan'])->find($request->from_reminder);
            if ($reminder && $reminder->servicePart) {
                $part = $reminder->servicePart;
                $prefill = [
                    'reminder_id'    => $reminder->id,
                    'kendaraan_id'   => $reminder->kendaraan_id,
                    'kendaraan'      => $reminder->kendaraan,
                    'part'           => [
                        'service_part_id' => $part->id,
                        'nama_part'       => $part->nama_part,
                        'part_number'     => $part->part_number,
                        'posisi'          => $part->posisi,
                        'category_id'     => $part->category_id,
                        'category_nama'   => $part->category?->nama,
                        'interval_nilai'  => $part->interval_nilai,
                        'interval_satuan' => $part->interval_satuan,
                        'biaya'           => $part->biaya,
                        'kondisi'         => $part->kondisi,
                    ],
                ];
            }
        }

        return view('admin.service.service_history_create', compact('kendaraan', 'categories', 'prefill'));
    }

    /**
     * Tambah kategori baru secara inline dari form
     */
    public function storeCategory(Request $request)
    {
        $request->validate(['nama' => 'required|string|max:100|unique:service_categories,nama']);
        $cat = ServiceCategory::create(['nama' => $request->nama]);
        return response()->json(['id' => $cat->id, 'nama' => $cat->nama]);
    }

    /**
     * Ambil data kendaraan untuk auto-fill KM
     */
    public function getKendaraanData($id)
    {
        $k = Kendaraan::with('jenis')->findOrFail($id);
        return response()->json([
            'kilometer_sekarang' => $k->kilometer_sekarang ?? 0,
            'merk'               => $k->merk,
            'nopol'              => $k->nopol,
            'jenis'              => $k->jenis?->nama ?? '-',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kendaraan_id'                 => 'required|exists:kendaraan,id',
            'tanggal_service'              => 'required|date',
            'kilometer'                    => 'required|integer|min:0',
            'status'                       => 'required|in:proses,selesai',
            'keluhan'                      => 'nullable|string',
            'total_biaya_override'         => 'nullable|numeric|min:0',
            'bukti_pembayaran'             => 'nullable|file|max:5120',
            'bukti_attachment'             => 'nullable|array',
            'bukti_attachment.*'           => 'file|max:5120',
            // Parts
            'parts'                        => 'nullable|array',
            'parts.*.nama_part'            => 'required_with:parts|string|max:255',
            'parts.*.category_id'          => 'nullable',
            'parts.*.nama_category_baru'   => 'nullable|string|max:100',
            'parts.*.part_number'          => 'nullable|string|max:100',
            'parts.*.serial_number'        => 'nullable|string|max:100',
            'parts.*.posisi'               => 'nullable|string|max:100',
            'parts.*.tgl_pasang'           => 'required_with:parts|date',
            'parts.*.kilometer_pasang'     => 'nullable|integer|min:0',
            'parts.*.kondisi'              => 'nullable|in:Baik,Rusak,Perlu Ganti',
            'parts.*.interval_nilai'       => 'required_with:parts|integer|min:1',
            'parts.*.interval_satuan'      => 'required_with:parts|in:hari,minggu,bulan,tahun',
            'parts.*.biaya'                => 'nullable|numeric|min:0',
        ]);

        $kendaraan = Kendaraan::findOrFail($request->kendaraan_id);

        if ($kendaraan->status_kendaraan === 'disewa') {
            return back()->withErrors(['kendaraan_id' => 'Kendaraan sedang disewa.'])->withInput();
        }

        $serviceAktif = ServiceHistory::where('kendaraan_id', $request->kendaraan_id)
            ->where('status', 'proses')->exists();
        if ($serviceAktif) {
            return back()->withErrors(['kendaraan_id' => 'Kendaraan masih punya service proses aktif.'])->withInput();
        }

        // Resolusi kategori per part (buat baru jika inline)
        $resolvedParts = $this->resolvePartsCategory($request->parts ?? []);

        // Total biaya: auto-sum dari parts, atau override jika diisi
        $sumBiayaParts = collect($resolvedParts)->sum(fn($p) => (int)($p['biaya'] ?? 0));
        $totalBiaya    = filled($request->total_biaya_override) && (int)$request->total_biaya_override > 0
            ? (int)$request->total_biaya_override
            : $sumBiayaParts;

        // Kalkulasi limit bulanan/tahunan
        [$sisaLimit, $maksBulanan, $biayaTahunan, $statusPengeluaran] = $this->hitungLimitStatus(
            $kendaraan, $request->tanggal_service, $totalBiaya
        );

        // Siapkan metadata file
        $buktiBayarMeta  = $this->prepBuktiBayar($request);
        $attachmentsMeta = $this->prepAttachments($request);
        $movedFiles      = [];

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use (
                $request, $kendaraan, $totalBiaya, $sisaLimit, $maksBulanan,
                $biayaTahunan, $statusPengeluaran, $buktiBayarMeta, $attachmentsMeta,
                $resolvedParts, &$movedFiles
            ) {
                $buktiBayar = null;
                if ($buktiBayarMeta) {
                    if (!file_exists($buktiBayarMeta['destination'])) mkdir($buktiBayarMeta['destination'], 0777, true);
                    $buktiBayarMeta['file']->move($buktiBayarMeta['destination'], $buktiBayarMeta['filename']);
                    $buktiBayar   = $buktiBayarMeta['path'];
                    $movedFiles[] = public_path($buktiBayar);
                }

                $service = ServiceHistory::create([
                    'kendaraan_id'       => $request->kendaraan_id,
                    'keluhan'            => $request->keluhan,
                    'kilometer'          => $request->kilometer,
                    'total_biaya'        => $totalBiaya,
                    'status'             => $request->status,
                    'tanggal_service'    => $request->tanggal_service,
                    'sisa_limit'         => $sisaLimit,
                    'maks_bulanan'       => $maksBulanan,
                    'biaya_tahunan'      => $biayaTahunan,
                    'status_pengeluaran' => $statusPengeluaran,
                    'bukti_pembayaran'   => $buktiBayar,
                ]);

                // Simpan parts
                $tglTerakhirPasang = null;
                foreach ($resolvedParts as $partData) {
                    $tglPasang     = Carbon::parse($partData['tgl_pasang']);
                    $tanggalLimit  = $this->hitungTanggalLimitPart(
                        $tglPasang,
                        (int)$partData['interval_nilai'],
                        $partData['interval_satuan']
                    );

                    $part = ServicePart::create([
                        'service_history_id' => $service->id,
                        'kendaraan_id'       => $request->kendaraan_id,
                        'category_id'        => $partData['category_id'] ?? null,
                        'nama_part'          => $partData['nama_part'],
                        'part_number'        => $partData['part_number'] ?? null,
                        'serial_number'      => $partData['serial_number'] ?? null,
                        'posisi'             => $partData['posisi'] ?? null,
                        'tgl_pasang'         => $tglPasang->toDateString(),
                        'kilometer_pasang'   => $partData['kilometer_pasang'] ?? $request->kilometer,
                        'kondisi'            => $partData['kondisi'] ?? 'Baik',
                        'status'             => 'Terpasang',
                        'interval_nilai'     => (int)$partData['interval_nilai'],
                        'interval_satuan'    => $partData['interval_satuan'],
                        'tanggal_limit'      => $tanggalLimit->toDateString(),
                        'biaya'              => (int)($partData['biaya'] ?? 0),
                    ]);

                    // Auto-close reminder aktif untuk part yang sama (kendaraan + posisi + nama)
                    $this->autoCloseReminderPart($kendaraan->id, $part);

                    // Track tanggal pasang terbaru untuk update kendaraan
                    if (!$tglTerakhirPasang || $tglPasang->gt($tglTerakhirPasang)) {
                        $tglTerakhirPasang = $tglPasang;
                    }
                }

                // Attachments
                if (!empty($attachmentsMeta)) {
                    if (!file_exists($attachmentsMeta[0]['destination'])) mkdir($attachmentsMeta[0]['destination'], 0777, true);
                    foreach ($attachmentsMeta as $att) {
                        $att['file']->move($att['destination'], $att['filename']);
                        $movedFiles[] = public_path($att['file_path']);
                        Attachment::create([
                            'relation_type' => 'service',
                            'relation_id'   => $service->id,
                            'file_name'     => $att['file_name'],
                            'file_path'     => $att['file_path'],
                            'file_type'     => $att['file_type'],
                            'file_size'     => $att['file_size'],
                        ]);
                    }
                }

                // Update kendaraan
                $updateKendaraan = [
                    'status_kendaraan' => $request->status === 'proses' ? 'service' : 'tersedia',
                ];
                if ($request->status === 'selesai') {
                    $updateKendaraan['km_terakhir_service']      = $request->kilometer;
                    $updateKendaraan['kilometer_sekarang']       = $request->kilometer;
                }
                // Update tanggal terakhir service dari tgl_pasang part terbaru
                if ($tglTerakhirPasang) {
                    $updateKendaraan['tanggal_terakhir_service'] = $tglTerakhirPasang->toDateString();
                }
                $kendaraan->update($updateKendaraan);

                // Jurnal keuangan
                $this->catatKeuangan($service, $kendaraan, $totalBiaya, $request->tanggal_service, false);
            });
        } catch (\Throwable $e) {
            foreach ($movedFiles as $f) { if (file_exists($f)) unlink($f); }
            throw $e;
        }

        return redirect()->route('service-history.index')
            ->with('success', 'Data service berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kendaraan_id'                 => 'required|exists:kendaraan,id',
            'tanggal_service'              => 'required|date',
            'kilometer'                    => 'required|integer|min:0',
            'status'                       => 'required|in:proses,selesai',
            'keluhan'                      => 'nullable|string',
            'total_biaya_override'         => 'nullable|numeric|min:0',
            'bukti_pembayaran'             => 'nullable|file|max:5120',
            'bukti_attachment'             => 'nullable|array',
            'bukti_attachment.*'           => 'file|max:5120',
            // Parts
            'parts'                        => 'nullable|array',
            'parts.*.nama_part'            => 'required_with:parts|string|max:255',
            'parts.*.category_id'          => 'nullable',
            'parts.*.nama_category_baru'   => 'nullable|string|max:100',
            'parts.*.part_number'          => 'nullable|string|max:100',
            'parts.*.serial_number'        => 'nullable|string|max:100',
            'parts.*.posisi'               => 'nullable|string|max:100',
            'parts.*.tgl_pasang'           => 'required_with:parts|date',
            'parts.*.kilometer_pasang'     => 'nullable|integer|min:0',
            'parts.*.kondisi'              => 'nullable|in:Baik,Rusak,Perlu Ganti',
            'parts.*.interval_nilai'       => 'required_with:parts|integer|min:1',
            'parts.*.interval_satuan'      => 'required_with:parts|in:hari,minggu,bulan,tahun',
            'parts.*.biaya'                => 'nullable|numeric|min:0',
        ]);

        $service   = ServiceHistory::findOrFail($id);
        $kendaraan = Kendaraan::findOrFail($request->kendaraan_id);

        $resolvedParts = $this->resolvePartsCategory($request->parts ?? []);
        $sumBiayaParts = collect($resolvedParts)->sum(fn($p) => (int)($p['biaya'] ?? 0));
        $totalBiaya    = filled($request->total_biaya_override) && (int)$request->total_biaya_override > 0
            ? (int)$request->total_biaya_override
            : $sumBiayaParts;

        [$sisaLimit, $maksBulanan, $biayaTahunan, $statusPengeluaran] = $this->hitungLimitStatus(
            $kendaraan, $request->tanggal_service, $totalBiaya, $id
        );

        $buktiBayarMeta  = $this->prepBuktiBayar($request);
        $attachmentsMeta = $this->prepAttachments($request);
        $movedFiles      = [];
        $deletedFiles    = [];

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use (
                $request, $id, $service, $kendaraan, $totalBiaya, $sisaLimit, $maksBulanan,
                $biayaTahunan, $statusPengeluaran, $buktiBayarMeta, $attachmentsMeta,
                $resolvedParts, &$movedFiles, &$deletedFiles
            ) {
                $buktiBayar = $service->bukti_pembayaran;
                if ($buktiBayarMeta) {
                    $oldPath = public_path($buktiBayarMeta['old_path'] ?? '');
                    if (!empty($buktiBayarMeta['old_path']) && file_exists($oldPath)) {
                        $deletedFiles[$oldPath] = file_get_contents($oldPath);
                        unlink($oldPath);
                    }
                    if (!file_exists($buktiBayarMeta['destination'])) mkdir($buktiBayarMeta['destination'], 0777, true);
                    $buktiBayarMeta['file']->move($buktiBayarMeta['destination'], $buktiBayarMeta['filename']);
                    $buktiBayar   = $buktiBayarMeta['path'];
                    $movedFiles[] = public_path($buktiBayar);
                }

                $service->update([
                    'kendaraan_id'       => $request->kendaraan_id,
                    'keluhan'            => $request->keluhan,
                    'kilometer'          => $request->kilometer,
                    'total_biaya'        => $totalBiaya,
                    'status'             => $request->status,
                    'tanggal_service'    => $request->tanggal_service,
                    'sisa_limit'         => $sisaLimit,
                    'maks_bulanan'       => $maksBulanan,
                    'biaya_tahunan'      => $biayaTahunan,
                    'status_pengeluaran' => $statusPengeluaran,
                    'bukti_pembayaran'   => $buktiBayar,
                ]);

                // Replace semua parts lama → hapus, buat ulang
                $service->parts()->delete();

                $tglTerakhirPasang = null;
                foreach ($resolvedParts as $partData) {
                    $tglPasang    = Carbon::parse($partData['tgl_pasang']);
                    $tanggalLimit = $this->hitungTanggalLimitPart(
                        $tglPasang, (int)$partData['interval_nilai'], $partData['interval_satuan']
                    );

                    $part = ServicePart::create([
                        'service_history_id' => $service->id,
                        'kendaraan_id'       => $request->kendaraan_id,
                        'category_id'        => $partData['category_id'] ?? null,
                        'nama_part'          => $partData['nama_part'],
                        'part_number'        => $partData['part_number'] ?? null,
                        'serial_number'      => $partData['serial_number'] ?? null,
                        'posisi'             => $partData['posisi'] ?? null,
                        'tgl_pasang'         => $tglPasang->toDateString(),
                        'kilometer_pasang'   => $partData['kilometer_pasang'] ?? $request->kilometer,
                        'kondisi'            => $partData['kondisi'] ?? 'Baik',
                        'status'             => 'Terpasang',
                        'interval_nilai'     => (int)$partData['interval_nilai'],
                        'interval_satuan'    => $partData['interval_satuan'],
                        'tanggal_limit'      => $tanggalLimit->toDateString(),
                        'biaya'              => (int)($partData['biaya'] ?? 0),
                    ]);

                    $this->autoCloseReminderPart($kendaraan->id, $part);

                    if (!$tglTerakhirPasang || $tglPasang->gt($tglTerakhirPasang)) {
                        $tglTerakhirPasang = $tglPasang;
                    }
                }

                // Attachments baru
                if (!empty($attachmentsMeta)) {
                    if (!file_exists($attachmentsMeta[0]['destination'])) mkdir($attachmentsMeta[0]['destination'], 0777, true);
                    foreach ($attachmentsMeta as $att) {
                        $att['file']->move($att['destination'], $att['filename']);
                        $movedFiles[] = public_path($att['file_path']);
                        Attachment::create([
                            'relation_type' => 'service',
                            'relation_id'   => $service->id,
                            'file_name'     => $att['file_name'],
                            'file_path'     => $att['file_path'],
                            'file_type'     => $att['file_type'],
                            'file_size'     => $att['file_size'],
                        ]);
                    }
                }

                $updateKendaraan = [
                    'status_kendaraan' => $request->status === 'proses' ? 'service' : 'tersedia',
                ];
                if ($request->status === 'selesai') {
                    $updateKendaraan['km_terakhir_service'] = $request->kilometer;
                    $updateKendaraan['kilometer_sekarang']  = $request->kilometer;
                }
                if ($tglTerakhirPasang) {
                    $updateKendaraan['tanggal_terakhir_service'] = $tglTerakhirPasang->toDateString();
                }
                $kendaraan->update($updateKendaraan);

                $this->catatKeuangan($service, $kendaraan, $totalBiaya, $request->tanggal_service, true);
            });
        } catch (\Throwable $e) {
            foreach ($movedFiles as $f) { if (file_exists($f)) unlink($f); }
            foreach ($deletedFiles as $p => $c) { file_put_contents($p, $c); }
            throw $e;
        }

        return redirect()->route('service-history.index')
            ->with('success', 'Data service berhasil diupdate.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:proses,selesai']);

        $service   = ServiceHistory::findOrFail($id);
        $kendaraan = $service->kendaraan;

        $service->update(['status' => $request->status]);

        $tanggalSelesai  = Carbon::parse($service->tanggal_service)->toDateString();
        $updateKendaraan = [
            'status_kendaraan' => $request->status === 'proses' ? 'service' : 'tersedia',
        ];
        if ($request->status === 'selesai' && $service->kilometer > 0) {
            $updateKendaraan['km_terakhir_service'] = $service->kilometer;
            $updateKendaraan['kilometer_sekarang']  = $service->kilometer;
            $updateKendaraan['tanggal_terakhir_service'] = $tanggalSelesai;
        }

        $kendaraan->update($updateKendaraan);

        return back()->with('success', 'Status berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $service   = ServiceHistory::findOrFail($id);
        $kendaraan = $service->kendaraan;

        foreach ($service->attachments as $att) {
            if (file_exists(public_path($att->file_path))) unlink(public_path($att->file_path));
            $att->delete();
        }

        // Parts akan terhapus cascade (FK cascadeOnDelete)
        $service->delete();

        if ($kendaraan) {
            $masihProses = ServiceHistory::where('kendaraan_id', $kendaraan->id)
                ->where('status', 'proses')->exists();
            if (!$masihProses) $kendaraan->update(['status_kendaraan' => 'tersedia']);
        }

        return back()->with('success', 'Data berhasil dihapus.');
    }

    public function destroyAttachment($id)
    {
        $attachment = Attachment::where('relation_type', 'service')->findOrFail($id);
        if (file_exists(public_path($attachment->file_path))) unlink(public_path($attachment->file_path));
        $attachment->delete();
        return back()->with('success', 'Lampiran berhasil dihapus');
    }

    public function pdf(Request $request)
    {
        $search = $request->search;
        $bulan  = $request->bulan;

        $data = ServiceHistory::with(['kendaraan.jenis', 'attachments', 'parts.category'])
            ->when($bulan, fn($q) => $q->whereRaw("DATE_FORMAT(tanggal_service, '%Y-%m') = ?", [$bulan]))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('keluhan', 'like', "%$search%")
                        ->orWhere('status', 'like', "%$search%")
                        ->orWhereHas('kendaraan', fn($k) =>
                            $k->where('merk', 'like', "%$search%")
                              ->orWhere('nopol', 'like', "%$search%")
                        );
                });
            })
            ->latest()->get();

        $setting = Setting::first();
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.service.pdf_history', compact('data', 'search', 'bulan', 'setting', 'logoSrc'));
        return $pdf->stream('service-history.pdf');
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Resolve category per part: buat category baru jika nama_category_baru diisi
     */
    private function resolvePartsCategory(array $parts): array
    {
        return array_map(function ($part) {
            if (!empty($part['nama_category_baru'])) {
                $cat = ServiceCategory::firstOrCreate(['nama' => trim($part['nama_category_baru'])]);
                $part['category_id'] = $cat->id;
            }
            return $part;
        }, $parts);
    }

    /**
     * Hitung tanggal limit part dari tgl_pasang + interval
     */
    private function hitungTanggalLimitPart(Carbon $tglPasang, int $nilai, string $satuan): Carbon
    {
        return match ($satuan) {
            'hari'   => (clone $tglPasang)->addDays($nilai),
            'minggu' => (clone $tglPasang)->addWeeks($nilai),
            'tahun'  => (clone $tglPasang)->addYears($nilai),
            default  => (clone $tglPasang)->addMonths($nilai),
        };
    }

    /**
     * Auto-close reminder aktif untuk part yang sama (kendaraan + posisi + nama_part)
     */
    private function autoCloseReminderPart(int $kendaraanId, ServicePart $newPart): void
    {
        ReminderService::where('kendaraan_id', $kendaraanId)
            ->whereIn('status', ['aktif', 'jatuh_tempo'])
            ->whereHas('servicePart', fn($q) =>
                $q->where('nama_part', $newPart->nama_part)
                  ->where('posisi', $newPart->posisi)
            )
            ->update(['status' => 'selesai']);
    }

    /**
     * Kalkulasi sisa limit, biaya tahunan, dan status pengeluaran
     */
    private function hitungLimitStatus(Kendaraan $kendaraan, string $tanggal, int $totalBiaya, ?int $excludeId = null): array
    {
        $limitBulanan = $kendaraan->limit_biaya_bulanan_service ?? 0;
        $limitTahunan = $kendaraan->limit_biaya_tahunan_service ?? 0;
        $bulan        = date('Y-m', strtotime($tanggal));
        $tahun        = date('Y', strtotime($tanggal));

        $qBulan = ServiceHistory::where('kendaraan_id', $kendaraan->id)
            ->whereRaw("DATE_FORMAT(tanggal_service, '%Y-%m') = ?", [$bulan]);
        $qTahun = ServiceHistory::where('kendaraan_id', $kendaraan->id)
            ->whereYear('tanggal_service', $tahun);

        if ($excludeId) {
            $qBulan->where('id', '!=', $excludeId);
            $qTahun->where('id', '!=', $excludeId);
        }

        $totalBulanIni = $qBulan->sum('total_biaya');
        $totalTahunIni = $qTahun->sum('total_biaya');
        $sisaLimit     = $limitBulanan - ($totalBulanIni + $totalBiaya);
        $biayaTahunan  = $totalTahunIni + $totalBiaya;
        $overBulanan   = $limitBulanan > 0 && ($totalBulanIni + $totalBiaya) > $limitBulanan;
        $overTahunan   = $limitTahunan > 0 && $biayaTahunan > $limitTahunan;

        return [$sisaLimit, $limitBulanan, $biayaTahunan, ($overBulanan || $overTahunan) ? 'overservice' : 'stabil'];
    }

    /**
     * Persiapkan metadata bukti pembayaran untuk di-move
     */
    private function prepBuktiBayar(Request $request, ?ServiceHistory $existing = null): ?array
    {
        if (!$request->hasFile('bukti_pembayaran')) return null;
        $file     = $request->file('bukti_pembayaran');
        $filename = time() . '_' . $file->getClientOriginalName();
        return [
            'file'        => $file,
            'filename'    => $filename,
            'destination' => public_path('bukti_pembayaran'),
            'path'        => 'bukti_pembayaran/' . $filename,
            'old_path'    => $existing?->bukti_pembayaran,
        ];
    }

    /**
     * Persiapkan metadata attachments untuk di-move
     */
    private function prepAttachments(Request $request): array
    {
        if (!$request->hasFile('bukti_attachment')) return [];
        $pathDir = public_path('service/attachments');
        $result  = [];
        foreach ($request->file('bukti_attachment') as $file) {
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $result[] = [
                'file'        => $file,
                'filename'    => $filename,
                'destination' => $pathDir,
                'file_name'   => $file->getClientOriginalName(),
                'file_path'   => 'service/attachments/' . $filename,
                'file_type'   => $file->getClientOriginalExtension(),
                'file_size'   => $file->getSize(),
            ];
        }
        return $result;
    }

    /**
     * Catat / update jurnal keuangan dan buku besar
     */
    private function catatKeuangan(ServiceHistory $service, Kendaraan $kendaraan, int $totalBiaya, string $tanggal, bool $isUpdate): void
    {
        $kodeJurnal = 'SRV-' . $service->id;

        $lastSaldo = (float) (\Illuminate\Support\Facades\DB::table('keuangans')
            ->lockForUpdate()->orderBy('id', 'desc')->value('saldo') ?? 0);

        $keuangan = Keuangan::where('reference', $kodeJurnal)->first();

        if ($isUpdate && $keuangan) {
            $selisih = $totalBiaya - $keuangan->pengeluaran;
            $keuangan->update([
                'tanggal'     => $tanggal,
                'pengeluaran' => $totalBiaya,
                'saldo'       => $lastSaldo - $selisih,
                'keterangan'  => 'Service Kendaraan',
            ]);
        } elseif (!$keuangan) {
            Keuangan::create([
                'tanggal'     => $tanggal,
                'reference'   => $kodeJurnal,
                'user_id'     => auth()->id(),
                'kategori'    => 'Pengeluaran',
                'metode'      => 'Cash',
                'keterangan'  => 'Service Kendaraan',
                'pemasukan'   => 0,
                'pengeluaran' => $totalBiaya,
                'saldo'       => $lastSaldo - $totalBiaya,
                'source_type' => 'service_history',
                'source_id'   => $service->id,
                'sumber'      => 'auto',
            ]);
        }

        $saldoBB = (float) (\Illuminate\Support\Facades\DB::table('bukubesars')
            ->lockForUpdate()->orderBy('id', 'desc')->value('saldo') ?? 0);

        $bukubesar = Bukubesar::where('kode_jurnal', $kodeJurnal)->first();

        if ($isUpdate && $bukubesar) {
            $selisihBB = $totalBiaya - $bukubesar->debit;
            $bukubesar->update([
                'tanggal'   => $tanggal,
                'debit'     => $totalBiaya,
                'saldo'     => $saldoBB - $selisihBB,
                'transaksi' => 'Beban Service - ' . ($kendaraan->merk ?? '-') . ' ' . ($kendaraan->nopol ?? '-'),
            ]);
        } elseif (!$bukubesar) {
            Bukubesar::create([
                'kode_jurnal' => $kodeJurnal,
                'transaksi'   => 'Beban Service - ' . ($kendaraan->merk ?? '-') . ' ' . ($kendaraan->nopol ?? '-'),
                'kategori'    => 'Beban',
                'tanggal'     => $tanggal,
                'debit'       => $totalBiaya,
                'kredit'      => 0,
                'saldo'       => $saldoBB - $totalBiaya,
                'aktivitas'   => 'Operasi',
                'keterangan'  => 'Auto-posting: Service kendaraan ' . ($kendaraan->nopol ?? '-'),
            ]);
        }
    }
}
