<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Afiliasi;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AfiliasiController extends Controller
{
    public function index()
    {
        $data = Afiliasi::latest()->get();
        $total = $data->count();
        $totalAktif = $data->where('status', 'Aktif')->count();
        $totalNonaktif = $data->where('status', 'Nonaktif')->count();

        return view('admin.marketing.afiliasi.index', compact(
            'data', 'total', 'totalAktif', 'totalNonaktif'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_program'       => 'required|unique:afiliasis,id_program',
            'nama_program'     => 'required',
            'kode_referral'    => 'required|unique:afiliasis,kode_referral',
            'diskon_referral'  => 'required|numeric|min:0',
            'bonus_pengajak'   => 'required',
            'batas_waktu'      => 'required|date',
            'status'           => 'required',
        ]);

        Afiliasi::create($request->all());

        return back()->with('success', 'Program afiliasi berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $afiliasi = Afiliasi::findOrFail($id);

        $request->validate([
            'id_program'       => 'required|unique:afiliasis,id_program,' . $id,
            'nama_program'     => 'required',
            'kode_referral'    => 'required|unique:afiliasis,kode_referral,' . $id,
            'diskon_referral'  => 'required|numeric|min:0',
            'bonus_pengajak'   => 'required',
            'batas_waktu'      => 'required|date',
            'status'           => 'required',
        ]);

        $afiliasi->update($request->all());

        return back()->with('success', 'Program afiliasi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Afiliasi::findOrFail($id)->delete();

        return back()->with('success', 'Program afiliasi berhasil dihapus.');
    }

    public function pdf(Request $request)
    {
        $query = Afiliasi::query();

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_program', 'like', "%$search%")
                  ->orWhere('id_program', 'like', "%$search%")
                  ->orWhere('kode_referral', 'like', "%$search%")
                  ->orWhere('status', 'like', "%$search%");
            });
        }

        $data = $query->latest()->get();
        $setting = Setting::first();

        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.marketing.afiliasi.pdf', compact('data', 'setting', 'logoSrc'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('data-afiliasi.pdf');
    }
}
