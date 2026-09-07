<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceAsuransi;
use App\Models\Kendaraan;
use App\Models\Asuransi;
use App\Models\JenisAsuransi;

class ServiceAsuransiController extends Controller
{
    public function index(Request $request)
    {
        // Auto-selesaikan record yang periode_selesai-nya sudah lewat
        $expired = ServiceAsuransi::with('kendaraan')
            ->where('status', 'bermasalah')
            ->whereNotNull('periode_selesai')
            ->where('periode_selesai', '<', now()->toDateString())
            ->get();

        foreach ($expired as $item) {
            $item->update(['status' => 'selesai']);

            if ($item->kendaraan) {
                $masihBermasalah = ServiceAsuransi::where('kendaraan_id', $item->kendaraan_id)
                    ->where('id', '!=', $item->id)
                    ->where('status', 'bermasalah')
                    ->exists();

                if (!$masihBermasalah) {
                    $item->kendaraan->update(['status_kendaraan' => 'tersedia']);
                }
            }
        }

        $query = ServiceAsuransi::with(['kendaraan', 'jenisAsuransi'])->latest();

        // Filter pencarian: nopol atau merk kendaraan
        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('kendaraan', function ($q) use ($s) {
                $q->where('nopol', 'like', "%{$s}%")
                  ->orWhere('merk', 'like', "%{$s}%");
            });
        }

        // Filter status
        if ($request->filled('status') && in_array($request->status, ['bermasalah', 'selesai'])) {
            $query->where('status', $request->status);
        }

        $data = $query->paginate(15)->withQueryString();

        $kendaraan     = Kendaraan::orderBy('merk')->get();
        $asuransi      = Asuransi::orderBy('nama_asuransi')->get();
        $jenisAsuransi = JenisAsuransi::orderBy('nama_jenis')->get();

        return view('admin.service.service_asuransi', compact('data', 'kendaraan', 'asuransi', 'jenisAsuransi'));
    }

    public function store(Request $request, \App\Services\PengeluaranInterceptorService $interceptor)
    {
        $request->validate([
            'kendaraan_id'      => 'required|exists:kendaraan,id',
            'nama_asuransi'     => 'nullable|string|max:255',
            'jenis_asuransi_id' => 'nullable|exists:jenis_asuransi,id',
            'tanggal_service'   => 'required|date',
            'periode_mulai'     => 'nullable|date',
            'periode_selesai'   => 'nullable|date|after_or_equal:periode_mulai',
            'kilometer'         => 'required|numeric',
            'biaya'             => 'nullable|numeric',
            'bukti.*'           => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',  // Changed to nullable - upload saat approval
            'attachment.*'      => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        // Cek duplikat
        $exists = ServiceAsuransi::where('kendaraan_id', $request->kendaraan_id)->exists();
        if ($exists) {
            return back()->withInput()->with('error', 'Kendaraan ini sudah memiliki data service asuransi. Gunakan fitur Edit untuk memperbarui data.');
        }

        // ===========================================================================
        // APPROVAL WORKFLOW: Intercept dan kirim ke Pembayaran
        // ===========================================================================
        
        try {
            // Check if this is a resubmit (from rejected pembayaran)
            if ($request->filled('edit_pembayaran')) {
                $pembayaranId = $request->input('edit_pembayaran');
                
                // Resubmit: Update existing pembayaran
                $pembayaran = $interceptor->resubmitToPembayaran($pembayaranId, $request, 'service_asuransi');
                
                return redirect()
                    ->route('pembayaran.index', ['tab' => 'Pending'])
                    ->with('success', 'Pengajuan service asuransi berhasil diajukan ulang. Menunggu approval dari Superadmin.');
            }
            
            // Step 1: Intercept data dari form
            $interceptedData = $interceptor->intercept($request, 'service_asuransi');
            
            // Step 2: Save ke Pembayaran
            $pembayaran = $interceptor->saveToPembayaran($interceptedData, 'service_asuransi');
            
            // Step 3: Upload temporary files
            $uploadedFiles = $interceptor->uploadTemporaryFiles($request, $pembayaran->id);
            
            // Step 4: Update source_data dengan file info
            $sourceData = $pembayaran->source_data;
            $sourceData['temp_files'] = $uploadedFiles;
            $pembayaran->update(['source_data' => $sourceData]);
            
            return redirect()
                ->route('pembayaran.index', ['tab' => 'Pending'])
                ->with('success', 'Pengajuan pengeluaran service asuransi berhasil dikirim. Menunggu approval dari Superadmin.');
                
        } catch (\Exception $e) {
            \Log::error('Error intercepting service asuransi submission: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mengajukan pengeluaran. Silakan coba lagi.');
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
            'bukti.*'           => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
            'attachment.*'      => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        // Cek duplikat kendaraan di record lain
        $exists = ServiceAsuransi::where('kendaraan_id', $request->kendaraan_id)
            ->where('id', '!=', $id)
            ->exists();
        if ($exists) {
            return back()->withInput()->with('error', 'Kendaraan ini sudah memiliki data service asuransi di record lain.');
        }

        // Ambil file lama — pastikan selalu array (hindari foreach error jika tersimpan sebagai string)
        $buktiLama      = $this->normalizeFileArray($data->getRawOriginal('bukti'));
        $attachmentLama = $this->normalizeFileArray($data->getRawOriginal('attachment'));

        $buktiFiles      = $this->uploadBuktiFiles($request);
        $attachmentFiles = $this->uploadAttachmentFiles($request);

        $buktiList      = array_merge($buktiLama,      $buktiFiles);
        $attachmentList = array_merge($attachmentLama, $attachmentFiles);

        $data->update([
            'kendaraan_id'      => $request->kendaraan_id,
            'nama_asuransi'     => $request->nama_asuransi ?: null,
            'jenis_asuransi_id' => $request->jenis_asuransi_id ?: null,
            'tanggal_service'   => $request->tanggal_service,
            'periode_mulai'     => $request->periode_mulai,
            'periode_selesai'   => $request->periode_selesai,
            'kilometer'         => $request->kilometer,
            'biaya'             => $request->biaya,
            'keterangan'        => $request->keterangan,
            'bukti'             => !empty($buktiList)      ? $buktiList      : null,
            'attachment'        => !empty($attachmentList) ? $attachmentList : null,
        ]);

        return back()->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        $data = ServiceAsuransi::findOrFail($id);

        // Normalise raw JSON string → array sebelum foreach
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

    /**
     * Normalise nilai kolom bukti/attachment ke array.
     * Menangani: null, string JSON, atau array yang sudah di-cast.
     */
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
