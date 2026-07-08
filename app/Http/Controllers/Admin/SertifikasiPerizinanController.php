<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SertifikasiPerizinan;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SertifikasiPerizinanController extends Controller
{
    public function index(Request $request)
    {
        $query = SertifikasiPerizinan::latest();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sq) use ($q) {
                $sq->where('jenis', 'like', "%$q%")
                   ->orWhere('nomor', 'like', "%$q%")
                   ->orWhere('instansi', 'like', "%$q%");
            });
        }

        $data       = $query->paginate(15)->withQueryString();
        $total      = SertifikasiPerizinan::count();
        $totalAktif = SertifikasiPerizinan::where('status', 'Aktif')->count();
        $totalExp   = SertifikasiPerizinan::where('status', 'Kadaluarsa')->count();

        return view('admin.legal.sertifikasi_perizinan.index', compact('data', 'total', 'totalAktif', 'totalExp'));
    }

    public function show($id)
    {
        return response()->json(SertifikasiPerizinan::findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis'          => 'required',
            'nomor'          => 'required',
            'instansi'       => 'required',
            'berlaku_hingga' => 'required',
            'status'         => 'required',
        ]);
        SertifikasiPerizinan::create($request->all());
        return back()->with('success', 'Sertifikasi/Perizinan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $item = SertifikasiPerizinan::findOrFail($id);
        $request->validate([
            'jenis'          => 'required',
            'nomor'          => 'required',
            'instansi'       => 'required',
            'berlaku_hingga' => 'required',
            'status'         => 'required',
        ]);
        $item->update($request->all());
        return back()->with('success', 'Sertifikasi/Perizinan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        SertifikasiPerizinan::findOrFail($id)->delete();
        return back()->with('success', 'Sertifikasi/Perizinan berhasil dihapus.');
    }

    public function pdf(Request $request)
    {
        $data    = SertifikasiPerizinan::latest()->get();
        $setting = Setting::first();
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }
        $pdf = Pdf::loadView('admin.legal.sertifikasi_perizinan.pdf', compact('data', 'setting', 'logoSrc'))
            ->setPaper('a4', 'landscape');
        return $pdf->stream('sertifikasi-perizinan.pdf');
    }
}
