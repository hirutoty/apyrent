<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kampanye;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class KampanyeController extends Controller
{
    public function index(Request $request)
    {
        $query = Kampanye::latest();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sq) use ($q) {
                $sq->where('id_kampanye', 'like', "%$q%")
                   ->orWhere('nama_kampanye', 'like', "%$q%")
                   ->orWhere('channel', 'like', "%$q%");
            });
        }

        $data             = $query->paginate(15)->withQueryString();
        $total            = Kampanye::count();
        $totalAktif       = Kampanye::where('status', 'Aktif')->count();
        $totalSelesai     = Kampanye::where('status', 'Selesai')->count();
        $totalDijadwalkan = Kampanye::where('status', 'Dijadwalkan')->count();

        return view('admin.marketing.kampanye.index', compact(
            'data', 'total', 'totalAktif', 'totalSelesai', 'totalDijadwalkan'
        ));
    }

    public function show($id)
    {
        $kampanye = Kampanye::findOrFail($id);
        return response()->json($kampanye);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_kampanye'       => 'required|unique:kampanyes,id_kampanye',
            'nama_kampanye'     => 'required',
            'tipe_kampanye'     => 'required',
            'channel'           => 'required',
            'target_segment'    => 'required',
            'tanggal_mulai'     => 'required|date',
            'tanggal_akhir'     => 'required|date|after_or_equal:tanggal_mulai',
            'subjek_pesan'      => 'required',
            'isi_pesan_ringkas' => 'required',
            'status'            => 'required',
        ]);

        Kampanye::create($request->all());

        return back()->with('success', 'Kampanye berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $kampanye = Kampanye::findOrFail($id);

        $request->validate([
            'id_kampanye'       => 'required|unique:kampanyes,id_kampanye,' . $id,
            'nama_kampanye'     => 'required',
            'tipe_kampanye'     => 'required',
            'channel'           => 'required',
            'target_segment'    => 'required',
            'tanggal_mulai'     => 'required|date',
            'tanggal_akhir'     => 'required|date|after_or_equal:tanggal_mulai',
            'subjek_pesan'      => 'required',
            'isi_pesan_ringkas' => 'required',
            'status'            => 'required',
        ]);

        $kampanye->update($request->all());

        return back()->with('success', 'Kampanye berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Kampanye::findOrFail($id)->delete();

        return back()->with('success', 'Kampanye berhasil dihapus.');
    }

    public function pdf(Request $request)
    {
        $query = Kampanye::query();

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_kampanye', 'like', "%$search%")
                  ->orWhere('id_kampanye', 'like', "%$search%")
                  ->orWhere('channel', 'like', "%$search%")
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

        $pdf = Pdf::loadView('admin.marketing.kampanye.pdf', compact('data', 'setting', 'logoSrc'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('data-kampanye.pdf');
    }
}
