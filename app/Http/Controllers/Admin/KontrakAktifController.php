<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KontrakAktif;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class KontrakAktifController extends Controller
{
    public function index(Request $request)
    {
        $query = KontrakAktif::latest();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sq) use ($q) {
                $sq->where('kode_kontrak', 'like', "%$q%")
                   ->orWhere('mitra', 'like', "%$q%")
                   ->orWhere('pic', 'like', "%$q%");
            });
        }

        $data         = $query->paginate(15)->withQueryString();
        $total        = KontrakAktif::count();
        $totalAktif   = KontrakAktif::where('status', 'Aktif')->count();
        $totalSelesai = KontrakAktif::where('status', 'Selesai')->count();
        $totalDraft   = KontrakAktif::where('status', 'Draft')->count();

        return view('admin.legal.kontrak_aktif.index', compact('data', 'total', 'totalAktif', 'totalSelesai', 'totalDraft'));
    }

    public function show($id)
    {
        return response()->json(KontrakAktif::findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_kontrak' => 'required',
            'mitra'        => 'required',
            'nilai'        => 'required|numeric',
            'tgl_mulai'    => 'required|date',
            'tgl_selesai'  => 'required|date|after_or_equal:tgl_mulai',
            'pic'          => 'required',
            'status'       => 'required',
        ]);

        $data = $request->all();
        $data['perpanjangan'] = $request->has('perpanjangan') ? 1 : 0;
        KontrakAktif::create($data);

        return back()->with('success', 'Kontrak berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $item = KontrakAktif::findOrFail($id);

        $request->validate([
            'kode_kontrak' => 'required',
            'mitra'        => 'required',
            'nilai'        => 'required|numeric',
            'tgl_mulai'    => 'required|date',
            'tgl_selesai'  => 'required|date|after_or_equal:tgl_mulai',
            'pic'          => 'required',
            'status'       => 'required',
        ]);

        $data = $request->all();
        $data['perpanjangan'] = $request->has('perpanjangan') ? 1 : 0;
        $item->update($data);

        return back()->with('success', 'Kontrak berhasil diperbarui.');
    }

    public function destroy($id)
    {
        KontrakAktif::findOrFail($id)->delete();
        return back()->with('success', 'Kontrak berhasil dihapus.');
    }

    public function pdf(Request $request)
    {
        $query = KontrakAktif::query();
        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('kode_kontrak', 'like', "%$s%")
                  ->orWhere('mitra', 'like', "%$s%")
                  ->orWhere('status', 'like', "%$s%");
            });
        }
        $data    = $query->latest()->get();
        $setting = Setting::first();
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }
        $pdf = Pdf::loadView('admin.legal.kontrak_aktif.pdf', compact('data', 'setting', 'logoSrc'))
            ->setPaper('a4', 'landscape');
        return $pdf->stream('kontrak-aktif.pdf');
    }
}
