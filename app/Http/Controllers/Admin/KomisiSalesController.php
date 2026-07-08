<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KomisiSales;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class KomisiSalesController extends Controller
{
    public function index(Request $request)
    {
        $query = KomisiSales::latest();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sq) use ($q) {
                $sq->where('nama_sales', 'like', "%$q%")
                   ->orWhere('bulan', 'like', "%$q%");
            });
        }

        $data           = $query->paginate(15)->withQueryString();
        $total          = KomisiSales::count();
        $totalKomisi    = KomisiSales::sum('total_komisi');
        $totalPenjualan = KomisiSales::sum('total_penjualan');

        return view('admin.sales.komisi_sales.index', compact(
            'data', 'total', 'totalKomisi', 'totalPenjualan'
        ));
    }

    public function show($id)
    {
        return response()->json(KomisiSales::findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_sales'     => 'required',
            'bulan'          => 'required',
            'total_penjualan'=> 'required|numeric|min:0',
            'persen_komisi'  => 'required|numeric|min:0|max:100',
            'total_komisi'   => 'required|numeric|min:0',
        ]);

        KomisiSales::create($request->all());

        return back()->with('success', 'Komisi Sales berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $item = KomisiSales::findOrFail($id);

        $request->validate([
            'nama_sales'     => 'required',
            'bulan'          => 'required',
            'total_penjualan'=> 'required|numeric|min:0',
            'persen_komisi'  => 'required|numeric|min:0|max:100',
            'total_komisi'   => 'required|numeric|min:0',
        ]);

        $item->update($request->all());

        return back()->with('success', 'Komisi Sales berhasil diperbarui.');
    }

    public function destroy($id)
    {
        KomisiSales::findOrFail($id)->delete();
        return back()->with('success', 'Komisi Sales berhasil dihapus.');
    }

    public function pdf(Request $request)
    {
        $query = KomisiSales::query();

        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama_sales', 'like', "%$s%")
                  ->orWhere('bulan', 'like', "%$s%");
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

        $pdf = Pdf::loadView('admin.sales.komisi_sales.pdf', compact('data', 'setting', 'logoSrc'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('data-komisi-sales.pdf');
    }
}
