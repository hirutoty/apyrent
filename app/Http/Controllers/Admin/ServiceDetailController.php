<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ServiceDetail;
use App\Models\Kendaraan;
use App\Models\Setting;

class ServiceDetailController extends Controller
{
    public function index()
    {
        $data = ServiceDetail::with('kendaraan')
            ->latest()
            ->paginate(15)->withQueryString();

        $kendaraan = Kendaraan::orderBy('merk')->get();

        return view(
            'admin.service.service_detail',
            compact('data', 'kendaraan')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'kendaraan_id'    => 'required|exists:kendaraan,id',
            'tanggal_service' => 'required|date',
            'kilometer'       => 'required|numeric',
            'status'          => 'required',
            'biaya'           => 'required|numeric',
            'bukti.*'         => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        $buktiList = $this->uploadBuktiFiles($request);

        ServiceDetail::create([
            'kendaraan_id'    => $request->kendaraan_id,
            'tanggal_service' => $request->tanggal_service,
            'kilometer'       => $request->kilometer,
            'status'          => $request->status,
            'biaya'           => $request->biaya,
            'keterangan'      => $request->keterangan,
            'bukti'           => !empty($buktiList) ? json_encode($buktiList) : null,
        ]);

        $kendaraan = Kendaraan::findOrFail($request->kendaraan_id);
        if ($request->status === 'Tidak Layak') {
            $kendaraan->update(['status_kendaraan' => 'bermasalah']);
        }

        return back()->with('success', 'Detail service berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $data = ServiceDetail::findOrFail($id);

        $request->validate([
            'biaya'   => 'required|numeric',
            'bukti.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        // File lama dipertahankan, file baru ditambahkan (append)
        $buktiLama = $data->bukti ?? [];

        $buktiFiles = $this->uploadBuktiFiles($request);

        // Gabungkan file lama + file baru
        $buktiList = array_merge($buktiLama, $buktiFiles);

        $data->update([
            'kendaraan_id'    => $request->kendaraan_id,
            'tanggal_service' => $request->tanggal_service,
            'kilometer'       => $request->kilometer,
            'status'          => $request->status,
            'biaya'           => $request->biaya,
            'keterangan'      => $request->keterangan,
            'bukti'           => !empty($buktiList) ? json_encode($buktiList) : null,
        ]);

        return back()->with('success', 'Data berhasil diupdate');
    }

    /**
     * Hapus satu file bukti dari service detail
     */
    public function deleteBukti(Request $request, $id)
    {
        $request->validate([
            'file_path' => 'required|string',
        ]);

        $data = ServiceDetail::findOrFail($id);
        $path = $request->file_path;

        $buktiList = $data->bukti ?? [];

        // Support both old format (plain string) and new format (object with 'path')
        $buktiList = array_values(array_filter($buktiList, function ($f) use ($path) {
            $p = is_array($f) ? ($f['path'] ?? '') : $f;
            return $p !== $path;
        }));

        // Hapus file fisik
        $fullPath = public_path($path);
        if ($path && file_exists($fullPath)) {
            unlink($fullPath);
        }

        $data->update([
            'bukti' => !empty($buktiList) ? json_encode($buktiList) : null,
        ]);

        return back()->with('success', 'File bukti berhasil dihapus');
    }

    public function destroy($id)
    {
        $data = ServiceDetail::findOrFail($id);

        // Hapus semua file bukti fisik (support old & new format)
        foreach ($data->bukti ?? [] as $f) {
            $path     = is_array($f) ? ($f['path'] ?? '') : $f;
            $fullPath = public_path($path);
            if ($path && file_exists($fullPath)) {
                unlink($fullPath);
            }
        }

        $data->delete();

        return back()->with('success', 'Data berhasil dihapus');
    }

    public function pdf(Request $request)
    {
        $query = ServiceDetail::with('kendaraan');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('keterangan', 'like', "%{$search}%")
                    ->orWhereHas('kendaraan', function ($k) use ($search) {
                        $k->where('merk', 'like', "%{$search}%")
                            ->orWhere('nopol', 'like', "%{$search}%");
                    });
            });
        }

        $status = $request->status;
        if ($request->filled('status')) {
            $query->where('status', $status);
        }

        $bulan      = $request->bulan;
        $bulanLabel = null;
        if ($request->filled('bulan')) {
            $query->whereYear('tanggal_service', substr($bulan, 0, 4))
                ->whereMonth('tanggal_service', substr($bulan, 5, 2));
            $bulanLabel = \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y');
        }

        $setting  = Setting::first();
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $data = $query->latest('tanggal_service')->get();

        $pdf = PDF::loadView(
            'admin.service.pdf_detail',
            compact('data', 'setting', 'status', 'bulanLabel', 'logoSrc')
        )->setPaper('A4', 'landscape');

        return $pdf->stream('service-detail.pdf');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Layak,Tidak Layak',
        ]);

        $data = ServiceDetail::findOrFail($id);
        $data->update(['status' => $request->status]);

        if ($request->status === 'Tidak Layak') {
            $data->kendaraan->update(['status_kendaraan' => 'bermasalah']);
        } elseif ($request->status === 'Layak') {
            $masihProses = \App\Models\ServiceHistory::where('kendaraan_id', $data->kendaraan_id)
                ->where('status', 'proses')
                ->exists();

            if (!$masihProses) {
                $data->kendaraan->update(['status_kendaraan' => 'tersedia']);
            }
        }

        return back()->with('success', 'Status berhasil diubah');
    }

    // ── HELPER ──────────────────────────────────────────────────────────────

    /**
     * Upload semua file dari request->file('bukti') ke public/service-detail
     * dan kembalikan array object [{path, name}].
     */
    private function uploadBuktiFiles(Request $request): array
    {
        $items = [];

        if (!$request->hasFile('bukti')) {
            return $items;
        }

        $destination = public_path('service-detail');
        if (!file_exists($destination)) {
            mkdir($destination, 0777, true);
        }

        foreach ($request->file('bukti') as $file) {
            if (!$file->isValid()) continue;

            $originalName = $file->getClientOriginalName();
            $filename     = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($destination, $filename);

            $items[] = [
                'path' => 'service-detail/' . $filename,
                'name' => $originalName,
            ];
        }

        return $items;
    }
}
