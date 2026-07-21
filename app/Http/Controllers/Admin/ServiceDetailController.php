<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ServiceDetail;
use App\Models\Kendaraan;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

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
            'bukti'           => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $bukti = null;

        if ($request->hasFile('bukti')) {

            $file = $request->file('bukti');

            $filename = time() . '_' . $file->getClientOriginalName();
            $destination = public_path('service-detail');

            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }

            $file->move($destination, $filename);

            $bukti = 'service-detail/' . $filename;
        }

        ServiceDetail::create([
            'kendaraan_id' => $request->kendaraan_id,
            'tanggal_service' => $request->tanggal_service,
            'kilometer' => $request->kilometer,
            'status' => $request->status,
            'biaya' => $request->biaya,
            'keterangan' => $request->keterangan,
            'bukti' => $bukti,
        ]);

        return back()->with('success', 'Detail service berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $data = ServiceDetail::findOrFail($id);

        $request->validate([
            'biaya' => 'required|numeric',
            'bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // default pakai data lama
        $bukti = $data->bukti;

        if ($request->hasFile('bukti')) {

            // hapus file lama
            if ($data->bukti && file_exists(public_path($data->bukti))) {
                unlink(public_path($data->bukti));
            }

            $file = $request->file('bukti');
            $filename = time() . '_' . $file->getClientOriginalName();
            $destination = public_path('service-detail');

            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }

            $file->move($destination, $filename);

            $bukti = 'service-detail/' . $filename;
        }

        $data->update([
            'kendaraan_id' => $request->kendaraan_id,
            'tanggal_service' => $request->tanggal_service,
            'kilometer' => $request->kilometer,
            'status' => $request->status,
            'biaya' => $request->biaya,
            'keterangan' => $request->keterangan,
            'bukti' => $bukti, // ✅ sekarang pasti ada
        ]);

        return back()->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        $data = ServiceDetail::findOrFail($id);

        if ($data->bukti && file_exists(public_path($data->bukti))) {
            unlink(public_path($data->bukti));
        }

        $data->delete();

        return back()->with('success', 'Data berhasil dihapus');
    }

    public function pdf(Request $request)
    {
        $query = ServiceDetail::with('kendaraan');

        // 🔍 FILTER SEARCH (keterangan, merk, atau nopol)
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

        // ✅ FILTER STATUS (Layak / Tidak Layak)
        $status = $request->status;

        if ($request->filled('status')) {
            $query->where('status', $status);
        }

        // ✅ FILTER BULAN (format dikirim dari frontend: YYYY-MM)
        $bulan = $request->bulan;
        $bulanLabel = null;

        if ($request->filled('bulan')) {
            $query->whereYear('tanggal_service', substr($bulan, 0, 4))
                ->whereMonth('tanggal_service', substr($bulan, 5, 2));

            $bulanLabel = \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y');
        }

        $setting = Setting::first();
        // Base64 logo untuk DomPDF
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

        $data->update([
            'status' => $request->status,
        ]);

        if ($request->status === 'Tidak Layak') {
            $data->kendaraan->update([
                'status_kendaraan' => 'bermasalah',
            ]);
        } elseif ($request->status === 'Layak') {
            // Hanya set tersedia jika tidak sedang dalam proses service
            $masihProses = \App\Models\ServiceHistory::where('kendaraan_id', $data->kendaraan_id)
                ->where('status', 'proses')
                ->exists();

            if (!$masihProses) {
                $data->kendaraan->update([
                    'status_kendaraan' => 'tersedia',
                ]);
            }
        }

        return back()->with('success', 'Status berhasil diubah');
    }
}