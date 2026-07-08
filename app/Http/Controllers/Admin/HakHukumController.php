<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HakHukum;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class HakHukumController extends Controller
{
    public function index(Request $request)
    {
        $query = HakHukum::latest();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sq) use ($q) {
                $sq->where('jenis_akses', 'like', "%$q%")
                   ->orWhere('kategori_dokumen', 'like', "%$q%")
                   ->orWhere('penerima_akses', 'like', "%$q%");
            });
        }

        $data          = $query->paginate(15)->withQueryString();
        $total         = HakHukum::count();
        $totalAktif    = HakHukum::where('status', 'Aktif')->count();
        $totalNonAktif = HakHukum::where('status', 'Non-Aktif')->count();

        return view('admin.legal.hak_hukum.index', compact('data', 'total', 'totalAktif', 'totalNonAktif'));
    }

    public function show($id)
    {
        return response()->json(HakHukum::findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_akses'      => 'required',
            'kategori_dokumen' => 'required',
            'penerima_akses'   => 'required',
            'level_hak'        => 'required',
            'tanggal_akses'    => 'required|date',
            'status'           => 'required',
        ]);
        HakHukum::create($request->all());
        return back()->with('success', 'Hak Hukum berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $item = HakHukum::findOrFail($id);
        $request->validate([
            'jenis_akses'      => 'required',
            'kategori_dokumen' => 'required',
            'penerima_akses'   => 'required',
            'level_hak'        => 'required',
            'tanggal_akses'    => 'required|date',
            'status'           => 'required',
        ]);
        $item->update($request->all());
        return back()->with('success', 'Hak Hukum berhasil diperbarui.');
    }

    public function destroy($id)
    {
        HakHukum::findOrFail($id)->delete();
        return back()->with('success', 'Hak Hukum berhasil dihapus.');
    }

    public function pdf(Request $request)
    {
        $data    = HakHukum::latest()->get();
        $setting = Setting::first();
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }
        $pdf = Pdf::loadView('admin.legal.hak_hukum.pdf', compact('data', 'setting', 'logoSrc'))
            ->setPaper('a4', 'landscape');
        return $pdf->stream('hak-hukum.pdf');
    }
}
