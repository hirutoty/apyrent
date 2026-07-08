<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loyalty;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LoyaltyController extends Controller
{
    public function index()
    {
        $data = Loyalty::latest()->get();
        $total = $data->count();
        $totalAktif = $data->where('status', 'Aktif')->count();
        $totalNonaktif = $data->where('status', 'Nonaktif')->count();

        return view('admin.marketing.loyalty.index', compact(
            'data', 'total', 'totalAktif', 'totalNonaktif'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_program'      => 'required|unique:loyalties,id_program',
            'nama_program'    => 'required',
            'jenis_reward'    => 'required',
            'akumulasi_poin'  => 'required',
            'konversi_poin'   => 'required',
            'periode_mulai'   => 'required|date',
            'periode_akhir'   => 'required|date|after_or_equal:periode_mulai',
            'status'          => 'required',
        ]);

        Loyalty::create($request->all());

        return back()->with('success', 'Program loyalty berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $loyalty = Loyalty::findOrFail($id);

        $request->validate([
            'id_program'      => 'required|unique:loyalties,id_program,' . $id,
            'nama_program'    => 'required',
            'jenis_reward'    => 'required',
            'akumulasi_poin'  => 'required',
            'konversi_poin'   => 'required',
            'periode_mulai'   => 'required|date',
            'periode_akhir'   => 'required|date|after_or_equal:periode_mulai',
            'status'          => 'required',
        ]);

        $loyalty->update($request->all());

        return back()->with('success', 'Program loyalty berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Loyalty::findOrFail($id)->delete();

        return back()->with('success', 'Program loyalty berhasil dihapus.');
    }

    public function pdf(Request $request)
    {
        $query = Loyalty::query();

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_program', 'like', "%$search%")
                  ->orWhere('id_program', 'like', "%$search%")
                  ->orWhere('jenis_reward', 'like', "%$search%")
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

        $pdf = Pdf::loadView('admin.marketing.loyalty.pdf', compact('data', 'setting', 'logoSrc'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('data-loyalty.pdf');
    }
}
