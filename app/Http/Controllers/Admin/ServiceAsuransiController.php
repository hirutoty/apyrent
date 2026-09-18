<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceAsuransi;
use App\Models\Kendaraan;
use App\Models\Asuransi;
use App\Models\JenisAsuransi;
use App\Models\PurchaseOrder;

class ServiceAsuransiController extends Controller
{
    public function index(Request $request)
    {
        // Tampilkan hanya yang sudah melewati PO (bukan Pending di PO)
        // Sama dengan logika GpsKendaraanController::index()
        $query = ServiceAsuransi::with(['kendaraan', 'jenisAsuransi', 'kejadians', 'pembayaran.approvals', 'pembayaran.latestApproval'])
            ->whereNotNull('persetujuan')
            ->where('persetujuan', '!=', 'Pending')
            ->where(function ($q) {
                $q->where('persetujuan', '!=', 'Ditolak')
                  ->orWhereNotNull('pembayaran_id'); // Ditolak di Pembayaran → tampilkan
            })
            ->latest();

        // Filter pencarian: nopol atau merk kendaraan
        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('kendaraan', function ($q) use ($s) {
                $q->where('nopol', 'like', "%{$s}%")
                  ->orWhere('merk', 'like', "%{$s}%");
            });
        }

        // Filter status
        if ($request->filled('status') && in_array($request->status, ['bermasalah', 'selesai', 'tidak_aktif'])) {
            $query->where('status', $request->status);
        }

        $data = $query->paginate(15)->withQueryString();

        $kendaraan     = Kendaraan::orderBy('merk')->get();
        $asuransi      = Asuransi::orderBy('nama_asuransi')->get();
        $jenisAsuransi = JenisAsuransi::orderBy('nama_jenis')->get();

        return view('admin.service.service_asuransi', compact('data', 'kendaraan', 'asuransi', 'jenisAsuransi'));
    }

    /**
     * AJAX: ambil kilometer_sekarang dari kendaraan
     */
    public function getKendaraanData($id)
    {
        $k = Kendaraan::findOrFail($id);
        return response()->json([
            'kilometer_sekarang' => $k->kilometer_sekarang ?? 0,
            'nopol'              => $k->nopol,
            'merk'               => $k->merk,
        ]);
    }

    public function store(Request $request, \App\Services\PengeluaranInterceptorService $interceptor)
    {
        $request->validate([
            'kendaraan_id'                  => 'required|exists:kendaraan,id',
            'nama_asuransi'                 => 'nullable|string|max:255',
            'jenis_asuransi_id'             => 'nullable|exists:jenis_asuransi,id',
            'tanggal_service'               => 'required|date',
            'periode_mulai'                 => 'nullable|date',
            'periode_selesai'               => 'nullable|date|after_or_equal:periode_mulai',
            'kilometer'                     => 'required|numeric',
            'biaya'                         => 'nullable|numeric',
            'kejadians'                     => 'required|array|min:1',
            'kejadians.*.nama_kejadian'     => 'required|string|max:255',
            'kejadians.*.biaya'             => 'nullable|integer|min:0',
            'kejadians.*.lampiran'          => 'required|array|min:1',
            'kejadians.*.lampiran.*'        => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        try {
            // Buat slug keterangan otomatis: servis asuransi-nopol
            $kendaraan = Kendaraan::findOrFail($request->kendaraan_id);
            $nopol     = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $kendaraan->nopol));
            $keterangan = 'servis asuransi-' . $kendaraan->nopol;

            // ── Check if this is a resubmit (from rejected PO) ──────────────
            if ($request->filled('edit_purchase_order')) {
                $poId            = $request->input('edit_purchase_order');
                $approvalService = app(\App\Services\PurchaseOrderApprovalService::class);
                $po = PurchaseOrder::findOrFail($poId);
                $po = $approvalService->resubmit($po, $request->all(), $request);

                return redirect()
                    ->route('service-asuransi.index')
                    ->with('success', 'Service asuransi berhasil diajukan ulang. Menunggu approval Superadmin.');
            }

            // ── Step 1: Intercept data ───────────────────────────────────────
            // Inject keterangan ke request sebelum intercept
            $request->merge(['keterangan' => $keterangan]);
            $interceptedData = $interceptor->intercept($request, 'service_asuransi');

            // ── Step 2: Save ke Purchase Order ──────────────────────────────
            $po = $interceptor->saveToPurchaseOrder($interceptedData, 'service_asuransi');

            // ── Step 3: Buat record service_asuransi dengan status Pending ──
            $biayaTotal = 0;
            foreach (($request->kejadians ?? []) as $kej) {
                $biayaTotal += (int) ($kej['biaya'] ?? 0);
            }

            $serviceAsuransi = ServiceAsuransi::create([
                'kendaraan_id'      => $request->kendaraan_id,
                'nama_asuransi'     => $request->nama_asuransi,
                'jenis_asuransi_id' => $request->jenis_asuransi_id ?: null,
                'tanggal_service'   => $request->tanggal_service,
                'periode_mulai'     => $request->periode_mulai,
                'periode_selesai'   => $request->periode_selesai,
                'kilometer'         => $request->kilometer,
                'biaya'             => $biayaTotal,
                'keterangan'        => $keterangan,
                'status'            => 'tidak_aktif',
                'persetujuan'       => 'Pending',
                'purchase_order_id' => $po->id,
            ]);

            // ── Step 4: Upload lampiran per-kejadian ke temp storage ─────────
            $kejadians     = $request->input('kejadians', []);
            $kejadiansMeta = [];
            foreach ($kejadians as $idx => $kej) {
                $lampiranPaths = [];
                $fileKey = "kejadians.{$idx}.lampiran";
                if ($request->hasFile($fileKey)) {
                    $files = $request->file($fileKey);
                    if (!is_array($files)) $files = [$files];
                    foreach ($files as $li => $file) {
                        if (!$file->isValid()) continue;
                        $ts         = time();
                        $storedName = "{$ts}_{$idx}_{$li}_{$file->getClientOriginalName()}";
                        $tempDir    = "purchase_order/temp/{$po->id}/kejadians/{$idx}";
                        $path       = $file->storeAs($tempDir, $storedName, 'public');
                        $lampiranPaths[] = [
                            'path'          => $path,
                            'original_name' => $file->getClientOriginalName(),
                            'size'          => $file->getSize(),
                            'extension'     => $file->getClientOriginalExtension(),
                        ];
                    }
                }
                $kejadiansMeta[] = [
                    'nama_kejadian' => $kej['nama_kejadian'] ?? '',
                    'biaya'         => (int) ($kej['biaya'] ?? 0),
                    'lampiran'      => $lampiranPaths,
                ];
            }

            // ── Step 5: Update source_data PO ────────────────────────────────
            $sourceData = $po->source_data;
            $sourceData['kejadians']             = $kejadiansMeta;
            $sourceData['service_asuransi_id']   = $serviceAsuransi->id;
            $sourceData['keterangan']            = $keterangan;
            $po->update(['source_data' => $sourceData]);

            return redirect()
                ->route('service-asuransi.index')
                ->with('success', 'Pengajuan service asuransi berhasil dikirim ke Purchase Order. Menunggu approval Superadmin.');

        } catch (\Exception $e) {
            \Log::error('Error service asuransi store: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat mengajukan. Silakan coba lagi.');
        }
    }

    public function update(Request $request, $id)
    {
        $data = ServiceAsuransi::findOrFail($id);

        $request->validate([
            'kendaraan_id'      => 'required|exists:kendaraan,id',
            'nama_asuransi'     => 'nullable|string|max:255',
            'jenis_asuransi_id' => 'nullable|exists:jenis_asuransi,id',
            'tanggal_service'   => 'required|date',
            'periode_mulai'     => 'nullable|date',
            'periode_selesai'   => 'nullable|date|after_or_equal:periode_mulai',
            'kilometer'         => 'required|numeric',
            'biaya'             => 'nullable|numeric',
        ]);

        $buktiLama      = $this->normalizeFileArray($data->getRawOriginal('bukti'));
        $attachmentLama = $this->normalizeFileArray($data->getRawOriginal('attachment'));

        $buktiFiles      = $this->uploadBuktiFiles($request);
        $attachmentFiles = $this->uploadAttachmentFiles($request);

        $buktiList      = array_merge($buktiLama,      $buktiFiles);
        $attachmentList = array_merge($attachmentLama, $attachmentFiles);

        // Regenerate keterangan slug jika kendaraan berubah
        $kendaraan  = Kendaraan::findOrFail($request->kendaraan_id);
        $keterangan = 'servis asuransi-' . $kendaraan->nopol;

        $data->update([
            'kendaraan_id'      => $request->kendaraan_id,
            'nama_asuransi'     => $request->nama_asuransi ?: null,
            'jenis_asuransi_id' => $request->jenis_asuransi_id ?: null,
            'tanggal_service'   => $request->tanggal_service,
            'periode_mulai'     => $request->periode_mulai,
            'periode_selesai'   => $request->periode_selesai,
            'kilometer'         => $request->kilometer,
            'biaya'             => $request->biaya,
            'keterangan'        => $keterangan,
            'bukti'             => !empty($buktiList)      ? $buktiList      : null,
            'attachment'        => !empty($attachmentList) ? $attachmentList : null,
        ]);

        return back()->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        $data = ServiceAsuransi::findOrFail($id);

        foreach ($this->normalizeFileArray($data->getRawOriginal('bukti')) as $f) {
            $path = is_array($f) ? ($f['path'] ?? '') : $f;
            if ($path && file_exists(public_path($path))) unlink(public_path($path));
        }

        foreach ($this->normalizeFileArray($data->getRawOriginal('attachment')) as $f) {
            $path = is_array($f) ? ($f['path'] ?? '') : $f;
            if ($path && file_exists(public_path($path))) unlink(public_path($path));
        }

        $data->delete();

        return back()->with('success', 'Data berhasil dihapus');
    }

    public function deleteBukti(Request $request, $id)
    {
        $request->validate(['file_path' => 'required|string']);

        $data = ServiceAsuransi::findOrFail($id);
        $path = $request->file_path;

        $buktiList = array_values(array_filter(
            $this->normalizeFileArray($data->getRawOriginal('bukti')),
            fn($f) => (is_array($f) ? ($f['path'] ?? '') : $f) !== $path
        ));

        if ($path && file_exists(public_path($path))) unlink(public_path($path));

        $data->update(['bukti' => !empty($buktiList) ? $buktiList : null]);

        return back()->with('success', 'File bukti berhasil dihapus');
    }

    public function deleteAttachment(Request $request, $id)
    {
        $request->validate(['file_path' => 'required|string']);

        $data = ServiceAsuransi::findOrFail($id);
        $path = $request->file_path;

        $attachmentList = array_values(array_filter(
            $this->normalizeFileArray($data->getRawOriginal('attachment')),
            fn($f) => (is_array($f) ? ($f['path'] ?? '') : $f) !== $path
        ));

        if ($path && file_exists(public_path($path))) unlink(public_path($path));

        $data->update(['attachment' => !empty($attachmentList) ? $attachmentList : null]);

        return back()->with('success', 'File attachment berhasil dihapus');
    }

    /**
     * AJAX: load data service asuransi untuk modal ajukan ulang (dari halaman Pembayaran)
     * GET: /admin/service-asuransi/{id}/ajukan-ulang (via POST untuk AJAX load)
     */
    public function ajukanUlang(Request $request, $id)
    {
        $data = ServiceAsuransi::with(['kendaraan', 'jenisAsuransi', 'kejadians'])->findOrFail($id);

        // Harus status Ditolak dan punya pembayaran
        if ($data->persetujuan !== 'Ditolak' || !$data->pembayaran_id) {
            return response()->json(['success' => false, 'message' => 'Record ini tidak dapat diajukan ulang.'], 422);
        }

        $pembayaran = \App\Models\Pembayaran::with('latestApproval')->find($data->pembayaran_id);
        $catatan    = $pembayaran?->latestApproval?->catatan ?? $pembayaran?->catatan ?? null;

        // Kumpulkan lampiran lama per kejadian dari source_data pembayaran
        $sourceData = $pembayaran ? ($pembayaran->source_data ?? []) : [];
        $tempFiles  = $sourceData['temp_files'] ?? [];

        // Helper: bangun daftar lampiran yang sudah ada untuk satu kejadian
        $buildLampiran = function (array $lampiranModel, array $tempKejFiles) {
            $lampiranExisting = [];
            $seenPaths = [];

            // Prioritaskan lampiran yang tersimpan di model kejadian
            foreach ($lampiranModel as $lf) {
                $path = $lf['path'] ?? '';
                if (!$path) continue;
                $url = \Illuminate\Support\Facades\Storage::disk('public')->url($path);
                if ($url && !in_array($path, $seenPaths)) {
                    $seenPaths[] = $path;
                    $lampiranExisting[] = [
                        'path'          => $path,
                        'original_name' => $lf['original_name'] ?? $lf['name'] ?? basename($path),
                        'extension'     => $lf['extension'] ?? pathinfo($path, PATHINFO_EXTENSION),
                        'url'           => $url,
                    ];
                }
            }

            // Fallback: ambil dari temp_files source_data jika belum ada
            foreach ($tempKejFiles as $tf) {
                $path = $tf['path'] ?? '';
                if (!$path || in_array($path, $seenPaths)) continue;
                $url = \Illuminate\Support\Facades\Storage::disk('public')->url($path);
                if ($url) {
                    $seenPaths[] = $path;
                    $lampiranExisting[] = [
                        'path'          => $path,
                        'original_name' => $tf['original_name'] ?? basename($path),
                        'extension'     => $tf['extension'] ?? pathinfo($path, PATHINFO_EXTENSION),
                        'url'           => $url,
                    ];
                }
            }

            return $lampiranExisting;
        };

        // Bangun daftar kejadian dari relasi Eloquent
        $kejadians = $data->kejadians->map(function ($kej, $idx) use ($tempFiles, $buildLampiran) {
            $tempKejFiles = $tempFiles['kejadians'][$idx] ?? [];
            return [
                'id'                => $kej->id,
                'nama_kejadian'     => $kej->nama_kejadian,
                'biaya'             => $kej->biaya,
                'lampiran_existing' => $buildLampiran($kej->lampiran ?? [], $tempKejFiles),
            ];
        })->values();

        // Fallback: jika kejadian di DB kosong, coba bangun dari source_data pembayaran
        if ($kejadians->isEmpty() && !empty($sourceData['kejadians'])) {
            $kejadians = collect($sourceData['kejadians'])->map(function ($kej, $idx) use ($tempFiles, $buildLampiran) {
                $tempKejFiles = $tempFiles['kejadians'][$idx] ?? [];
                $lampiranModel = $kej['lampiran'] ?? [];
                return [
                    'id'                => null,
                    'nama_kejadian'     => $kej['nama_kejadian'] ?? '',
                    'biaya'             => (int)($kej['biaya'] ?? 0),
                    'lampiran_existing' => $buildLampiran($lampiranModel, $tempKejFiles),
                ];
            })->values();
        }

        return response()->json([
            'success'           => true,
            'id'                => $data->id,
            'pembayaran_id'     => $data->pembayaran_id,
            'kendaraan_id'      => $data->kendaraan_id,
            'kendaraan'         => ($data->kendaraan->nopol ?? '-') . ' — ' . ($data->kendaraan->merk ?? ''),
            'nama_asuransi'     => $data->nama_asuransi ?? '-',
            'tanggal_service'   => $data->tanggal_service,
            'periode_mulai'     => $data->periode_mulai,
            'periode_selesai'   => $data->periode_selesai,
            'kilometer'         => $data->kilometer,
            'catatan_penolakan' => $catatan,
            'kejadians'         => $kejadians,
        ]);
    }

    /**
     * POST: submit ajukan ulang service asuransi (dari halaman Pembayaran)
     */
    public function ajukanUlangSubmit(Request $request, $id)
    {
        $data = ServiceAsuransi::with('kejadians')->findOrFail($id);

        if ($data->persetujuan !== 'Ditolak' || !$data->pembayaran_id) {
            return response()->json(['success' => false, 'message' => 'Record ini tidak dapat diajukan ulang.'], 422);
        }

        $request->validate([
            'kejadians'                 => 'required|array|min:1',
            'kejadians.*.nama_kejadian' => 'required|string|max:255',
            'kejadians.*.biaya'         => 'nullable|integer|min:0',
        ]);

        try {
            $pembayaran = \App\Models\Pembayaran::findOrFail($data->pembayaran_id);
            $sourceData = $pembayaran->source_data ?? [];
            $tempFiles  = $sourceData['temp_files'] ?? [];

            $kejadians    = $request->input('kejadians', []);
            $biayaTotal   = collect($kejadians)->sum(fn($k) => (int)($k['biaya'] ?? 0));

            // Upload lampiran baru per kejadian
            foreach ($kejadians as $idx => $kej) {
                $fileKey = "kejadians.{$idx}.lampiran";
                if ($request->hasFile($fileKey)) {
                    $files = $request->file($fileKey);
                    if (!is_array($files)) $files = [$files];
                    foreach ($files as $li => $file) {
                        if (!$file->isValid()) continue;
                        $storedName = time() . "_{$idx}_{$li}_" . $file->getClientOriginalName();
                        $tempDir    = "purchase_order/temp/" . ($pembayaran->po_id ?? $pembayaran->id) . "/kejadians/{$idx}";
                        $path       = $file->storeAs($tempDir, $storedName, 'public');
                        $tempFiles['kejadians'][$idx][] = [
                            'path'          => $path,
                            'original_name' => $file->getClientOriginalName(),
                            'size'          => $file->getSize(),
                            'extension'     => $file->getClientOriginalExtension(),
                        ];
                    }
                }
            }

            // Update source_data pembayaran
            $newKejadians = array_map(function ($kej, $idx) use ($tempFiles) {
                // Lampiran lama dikirim dari form sebagai hidden inputs
                $lampiranLama = array_values(array_filter(
                    array_map(function ($lf) {
                        $path = $lf['path'] ?? '';
                        if (!$path) return null;
                        return [
                            'path'          => $path,
                            'original_name' => $lf['original_name'] ?? basename($path),
                            'extension'     => $lf['extension'] ?? pathinfo($path, PATHINFO_EXTENSION),
                            'size'          => (int)($lf['size'] ?? 0),
                        ];
                    }, $kej['lampiran_lama'] ?? [])
                ));
                // Gabungkan lampiran lama + file baru yang di-upload
                $newLampiran = $tempFiles['kejadians'][$idx] ?? [];
                return [
                    'nama_kejadian' => $kej['nama_kejadian'] ?? '',
                    'biaya'         => (int)($kej['biaya'] ?? 0),
                    'lampiran'      => array_merge($lampiranLama, $newLampiran),
                ];
            }, $kejadians, array_keys($kejadians));

            $newSourceData = array_merge($sourceData, [
                'kejadians' => $newKejadians,
                'temp_files'=> $tempFiles,
            ]);

            // Reset pembayaran ke Diajukan
            $pembayaran->update([
                'source_data'       => $newSourceData,
                'nominal'           => $biayaTotal,
                'status'            => 'Diajukan',
                'can_edit'          => false,
                'catatan'           => null,
                'terakhir_diajukan' => now(),
            ]);

            // Reset service_asuransi ke Diajukan ke Pembayaran
            $data->update([
                'biaya'        => $biayaTotal,
                'persetujuan'  => 'Diajukan ke Pembayaran',
            ]);

            // Update kejadian
            \App\Models\ServiceAsuransiKejadian::where('service_asuransi_id', $id)->delete();
            foreach ($newKejadians as $kej) {
                \App\Models\ServiceAsuransiKejadian::create([
                    'service_asuransi_id' => $id,
                    'nama_kejadian'       => $kej['nama_kejadian'],
                    'biaya'               => (int)($kej['biaya'] ?? 0),
                    'lampiran'            => !empty($kej['lampiran']) ? $kej['lampiran'] : null,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Service asuransi berhasil diajukan ulang. Menunggu approval.',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => implode(' ', array_merge(...array_values($e->errors())))], 422);
        } catch (\Exception $e) {
            \Log::error('Error ajukanUlang service asuransi: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $data = ServiceAsuransi::with('kendaraan')->findOrFail($id);

        $request->validate([
            'status' => 'required|in:bermasalah,selesai',
        ]);

        $updateData = ['status' => $request->status];

        if ($request->status === 'selesai') {
            $updateData['periode_selesai'] = now()->toDateString();
        }

        $data->update($updateData);

        if ($data->kendaraan) {
            if ($request->status === 'selesai') {
                $masihBermasalah = ServiceAsuransi::where('kendaraan_id', $data->kendaraan_id)
                    ->where('id', '!=', $id)
                    ->where('status', 'bermasalah')
                    ->exists();

                if (!$masihBermasalah) {
                    $data->kendaraan->update(['status_kendaraan' => 'tersedia']);
                }
            } else {
                $data->kendaraan->update(['status_kendaraan' => 'bermasalah']);
            }
        }

        return back()->with('success', 'Status berhasil diperbarui menjadi ' . ($request->status === 'selesai' ? 'Selesai' : 'Bermasalah'));
    }

    // ── HELPERS ─────────────────────────────────────────────────────────────

    private function normalizeFileArray(mixed $value): array
    {
        if (empty($value)) return [];
        if (is_array($value)) return $value;
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function uploadBuktiFiles(Request $request): array
    {
        $items = [];
        if (!$request->hasFile('bukti')) return $items;

        $destination = public_path('service-asuransi');
        if (!file_exists($destination)) mkdir($destination, 0777, true);

        foreach ($request->file('bukti') as $file) {
            if (!$file->isValid()) continue;
            $originalName = $file->getClientOriginalName();
            $filename     = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($destination, $filename);
            $items[] = ['path' => 'service-asuransi/' . $filename, 'name' => $originalName];
        }

        return $items;
    }

    private function uploadAttachmentFiles(Request $request): array
    {
        $items = [];
        if (!$request->hasFile('attachment')) return $items;

        $destination = public_path('service-asuransi-attachment');
        if (!file_exists($destination)) mkdir($destination, 0777, true);

        foreach ($request->file('attachment') as $file) {
            if (!$file->isValid()) continue;
            $originalName = $file->getClientOriginalName();
            $filename     = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($destination, $filename);
            $items[] = ['path' => 'service-asuransi-attachment/' . $filename, 'name' => $originalName];
        }

        return $items;
    }
}
