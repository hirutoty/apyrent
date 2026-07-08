<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PembelianProyek;
use App\Models\IndukProyek;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PembelianProyekController extends Controller
{
    public function index(Request $request)
    {
        $query = PembelianProyek::latest();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sq) use ($q) {
                $sq->where('pr_no', 'like', "%$q%")
                   ->orWhere('proyek', 'like', "%$q%")
                   ->orWhere('item_diminta', 'like', "%$q%");
            });
        }

        $data           = $query->paginate(15)->withQueryString();
        $total          = PembelianProyek::count();
        $totalDisetujui = PembelianProyek::where('status', 'Disetujui')->count();
        $totalPending   = PembelianProyek::where('status', 'Pending')->count();
        $totalDitolak   = PembelianProyek::where('status', 'Ditolak')->count();
        $proyeks        = IndukProyek::orderBy('kode')->pluck('nama_proyek', 'kode');

        return view('admin.project.pembelian.index', compact(
            'data', 'total', 'totalDisetujui', 'totalPending', 'totalDitolak', 'proyeks'
        ));
    }

    public function show($id)
    {
        return response()->json(PembelianProyek::findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pr_no'          => 'required|unique:pembelian_proyeks,pr_no',
            'proyek'         => 'required',
            'item_diminta'   => 'required',
            'qty'            => 'required|integer|min:1',
            'estimasi_harga' => 'required|numeric',
            'status'         => 'required',
            'tgl_permintaan' => 'required|date',
        ]);

        PembelianProyek::create($request->all());

        return back()->with('success', 'Pembelian Proyek berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $item = PembelianProyek::findOrFail($id);

        $request->validate([
            'pr_no'          => 'required|unique:pembelian_proyeks,pr_no,' . $id,
            'proyek'         => 'required',
            'item_diminta'   => 'required',
            'qty'            => 'required|integer|min:1',
            'estimasi_harga' => 'required|numeric',
            'status'         => 'required',
            'tgl_permintaan' => 'required|date',
        ]);

        $item->update($request->all());

        return back()->with('success', 'Pembelian Proyek berhasil diperbarui.');
    }

    public function destroy($id)
    {
        PembelianProyek::findOrFail($id)->delete();
        return back()->with('success', 'Pembelian Proyek berhasil dihapus.');
    }

    public function pdf(Request $request)
    {
        $data    = PembelianProyek::latest()->get();
        $setting = Setting::first();
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.project.pembelian.pdf', compact('data', 'setting', 'logoSrc'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('pembelian-proyek.pdf');
    }
}
