<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PricelistDiskon;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PricelistDiskonController extends Controller
{
    public function index(Request $request)
    {
        $query = PricelistDiskon::latest();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sq) use ($q) {
                $sq->where('id_harga', 'like', "%$q%")
                   ->orWhere('nama_produk', 'like', "%$q%")
                   ->orWhere('level_pelanggan', 'like', "%$q%");
            });
        }

        $data      = $query->paginate(15)->withQueryString();
        $total     = PricelistDiskon::count();
        $avgDiskon = PricelistDiskon::avg('diskon') ?? 0;

        return view('admin.sales.pricelist_diskon.index', compact('data', 'total', 'avgDiskon'));
    }

    public function show($id)
    {
        return response()->json(PricelistDiskon::findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_harga'       => 'required|unique:pricelist_diskons,id_harga',
            'nama_produk'    => 'required',
            'level_pelanggan'=> 'required',
            'harga_normal'   => 'required|numeric|min:0',
            'diskon'         => 'required|numeric|min:0|max:100',
            'harga_diskon'   => 'required|numeric|min:0',
            'periode_mulai'  => 'required|date',
            'periode_selesai'=> 'required|date|after_or_equal:periode_mulai',
        ]);

        PricelistDiskon::create($request->all());

        return back()->with('success', 'Pricelist & Diskon berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $item = PricelistDiskon::findOrFail($id);

        $request->validate([
            'id_harga'       => 'required|unique:pricelist_diskons,id_harga,' . $id,
            'nama_produk'    => 'required',
            'level_pelanggan'=> 'required',
            'harga_normal'   => 'required|numeric|min:0',
            'diskon'         => 'required|numeric|min:0|max:100',
            'harga_diskon'   => 'required|numeric|min:0',
            'periode_mulai'  => 'required|date',
            'periode_selesai'=> 'required|date|after_or_equal:periode_mulai',
        ]);

        $item->update($request->all());

        return back()->with('success', 'Pricelist & Diskon berhasil diperbarui.');
    }

    public function destroy($id)
    {
        PricelistDiskon::findOrFail($id)->delete();
        return back()->with('success', 'Pricelist & Diskon berhasil dihapus.');
    }

    public function pdf(Request $request)
    {
        $query = PricelistDiskon::query();

        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama_produk', 'like', "%$s%")
                  ->orWhere('id_harga', 'like', "%$s%")
                  ->orWhere('level_pelanggan', 'like', "%$s%");
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

        $pdf = Pdf::loadView('admin.sales.pricelist_diskon.pdf', compact('data', 'setting', 'logoSrc'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('data-pricelist-diskon.pdf');
    }
}
