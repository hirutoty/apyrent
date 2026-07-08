<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReturPenjualan;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReturPenjualanController extends Controller
{
    public function index(Request $request)
    {
        $query = ReturPenjualan::latest();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sq) use ($q) {
                $sq->where('no_retur', 'like', "%$q%")
                   ->orWhere('pelanggan', 'like', "%$q%")
                   ->orWhere('no_order', 'like', "%$q%")
                   ->orWhere('produk', 'like', "%$q%");
            });
        }

        $data          = $query->paginate(15)->withQueryString();
        $total         = ReturPenjualan::count();
        $totalMenunggu = ReturPenjualan::where('status', 'Menunggu')->count();
        $totalDiproses = ReturPenjualan::where('status', 'Diproses')->count();
        $totalSelesai  = ReturPenjualan::where('status', 'Selesai')->count();

        return view('admin.sales.retur_penjualan.index', compact(
            'data', 'total', 'totalMenunggu', 'totalDiproses', 'totalSelesai'
        ));
    }

    public function show($id)
    {
        return response()->json(ReturPenjualan::findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_retur'   => 'required|unique:retur_penjualans,no_retur',
            'tanggal'    => 'required|date',
            'no_order'   => 'required',
            'pelanggan'  => 'required',
            'produk'     => 'required',
            'qty'        => 'required|integer|min:1',
            'alasan'     => 'required',
            'nilai_retur'=> 'required|numeric|min:0',
            'status'     => 'required',
        ]);

        ReturPenjualan::create($request->all());

        return back()->with('success', 'Retur Penjualan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $item = ReturPenjualan::findOrFail($id);

        $request->validate([
            'no_retur'   => 'required|unique:retur_penjualans,no_retur,' . $id,
            'tanggal'    => 'required|date',
            'no_order'   => 'required',
            'pelanggan'  => 'required',
            'produk'     => 'required',
            'qty'        => 'required|integer|min:1',
            'alasan'     => 'required',
            'nilai_retur'=> 'required|numeric|min:0',
            'status'     => 'required',
        ]);

        $item->update($request->all());

        return back()->with('success', 'Retur Penjualan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        ReturPenjualan::findOrFail($id)->delete();
        return back()->with('success', 'Retur Penjualan berhasil dihapus.');
    }

    public function pdf(Request $request)
    {
        $query = ReturPenjualan::query();

        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('no_retur', 'like', "%$s%")
                  ->orWhere('pelanggan', 'like', "%$s%")
                  ->orWhere('no_order', 'like', "%$s%")
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

        $pdf = Pdf::loadView('admin.sales.retur_penjualan.pdf', compact('data', 'setting', 'logoSrc'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('data-retur-penjualan.pdf');
    }
}
