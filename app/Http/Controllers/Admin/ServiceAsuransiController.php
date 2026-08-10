<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceAsuransi;
use App\Models\Kendaraan;

class ServiceAsuransiController extends Controller
{
    public function index()
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

        $data = ServiceAsuransi::with('kendaraan')
            ->latest()
            ->paginate(15)->withQueryString();

        $kendaraan = Kendaraan::orderBy('merk')->get();

        return view('admin.service.service_asuransi', compact('data', 'kendaraan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kendaraan_id'    => 'required|exists:kendaraan,id',
            'tanggal_service' => 'required|date',
            'periode_mulai'   => 'nullable|date',
            'periode_selesai' => 'nullable|date|after_or_equal:periode_mulai',
            'kilometer'       => 'required|numeric',
            'biaya'           => 'nullable|numeric',
            'bukti.*'         => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
            'attachment.*'    => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        $buktiList      = $this->uploadBuktiFiles($request);
        $attachmentList = $this->uploadAttachmentFiles($request);

        // Cek duplikat: kendaraan yang sama tidak boleh ditambah lagi
        $exists = ServiceAsuransi::where('kendaraan_id', $request->kendaraan_id)->exists();
        if ($exists) {
            return back()->withInput()->with('error', 'Kendaraan ini sudah memiliki data service asuransi. Gunakan fitur Edit untuk memperbarui data.');
        }

        ServiceAsuransi::create([
            'kendaraan_id'    => $request->kendaraan_id,
            'tanggal_service' => $request->tanggal_service,
            'periode_mulai'   => $request->periode_mulai,
            'periode_selesai' => $request->periode_selesai,
            'kilometer'       => $request->kilometer,
            'biaya'           => $request->biaya,
            'keterangan'      => $request->keterangan,
            'bukti'           => !empty($buktiList)      ? json_encode($buktiList)      : null,
            'attachment'      => !empty($attachmentList) ? json_encode($attachmentList) : null,
            'status'          => 'bermasalah',
        ]);

        // Auto-set kendaraan status to 'bermasalah'
        $kendaraan = Kendaraan::findOrFail($request->kendaraan_id);
        $kendaraan->update(['status_kendaraan' => 'bermasalah']);

        return back()->with('success', 'Data service asuransi berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $data = ServiceAsuransi::findOrFail($id);

        $request->validate([
            'kendaraan_id'    => 'required|exists:kendaraan,id',
            'tanggal_service' => 'required|date',
            'periode_mulai'   => 'nullable|date',
            'periode_selesai' => 'nullable|date|after_or_equal:periode_mulai',
            'kilometer'       => 'required|numeric',
            'biaya'           => 'nullable|numeric',
            'bukti.*'         => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
            'attachment.*'    => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        // Cek duplikat: kendaraan yang sama tidak boleh dipakai oleh record lain
        $exists = ServiceAsuransi::where('kendaraan_id', $request->kendaraan_id)
            ->where('id', '!=', $id)
            ->exists();
        if ($exists) {
            return back()->withInput()->with('error', 'Kendaraan ini sudah memiliki data service asuransi di record lain.');
        }

        // File lama dipertahankan, file baru di-append
        $buktiLama      = $data->bukti      ?? [];
        $attachmentLama = $data->attachment ?? [];

        $buktiFiles      = $this->uploadBuktiFiles($request);
        $attachmentFiles = $this->uploadAttachmentFiles($request);

        $buktiList      = array_merge($buktiLama,      $buktiFiles);
        $attachmentList = array_merge($attachmentLama, $attachmentFiles);

        $data->update([
            'kendaraan_id'    => $request->kendaraan_id,
            'tanggal_service' => $request->tanggal_service,
            'periode_mulai'   => $request->periode_mulai,
            'periode_selesai' => $request->periode_selesai,
            'kilometer'       => $request->kilometer,
            'biaya'           => $request->biaya,
            'keterangan'      => $request->keterangan,
            'bukti'           => !empty($buktiList)      ? json_encode($buktiList)      : null,
            'attachment'      => !empty($attachmentList) ? json_encode($attachmentList) : null,
        ]);

        return back()->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        $data = ServiceAsuransi::findOrFail($id);

        foreach ($data->bukti ?? [] as $f) {
            $path     = is_array($f) ? ($f['path'] ?? '') : $f;
            $fullPath = public_path($path);
            if ($path && file_exists($fullPath)) unlink($fullPath);
        }

        foreach ($data->attachment ?? [] as $f) {
            $path     = is_array($f) ? ($f['path'] ?? '') : $f;
            $fullPath = public_path($path);
            if ($path && file_exists($fullPath)) unlink($fullPath);
        }

        $data->delete();

        return back()->with('success', 'Data berhasil dihapus');
    }

    public function deleteBukti(Request $request, $id)
    {
        $request->validate(['file_path' => 'required|string']);

        $data = ServiceAsuransi::findOrFail($id);
        $path = $request->file_path;

        $buktiList = array_values(array_filter($data->bukti ?? [], function ($f) use ($path) {
            $p = is_array($f) ? ($f['path'] ?? '') : $f;
            return $p !== $path;
        }));

        $fullPath = public_path($path);
        if ($path && file_exists($fullPath)) unlink($fullPath);

        $data->update(['bukti' => !empty($buktiList) ? json_encode($buktiList) : null]);

        return back()->with('success', 'File bukti berhasil dihapus');
    }

    public function deleteAttachment(Request $request, $id)
    {
        $request->validate(['file_path' => 'required|string']);

        $data = ServiceAsuransi::findOrFail($id);
        $path = $request->file_path;

        $attachmentList = array_values(array_filter($data->attachment ?? [], function ($f) use ($path) {
            $p = is_array($f) ? ($f['path'] ?? '') : $f;
            return $p !== $path;
        }));

        $fullPath = public_path($path);
        if ($path && file_exists($fullPath)) unlink($fullPath);

        $data->update(['attachment' => !empty($attachmentList) ? json_encode($attachmentList) : null]);

        return back()->with('success', 'File attachment berhasil dihapus');
    }

    public function updateStatus(Request $request, $id)
    {
        $data = ServiceAsuransi::with('kendaraan')->findOrFail($id);

        $request->validate([
            'status' => 'required|in:bermasalah,selesai',
        ]);

        $updateData = ['status' => $request->status];

        // Jika diubah ke selesai, set periode_selesai ke hari ini
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
