<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PenawaranSales;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PenawaranSalesController extends Controller
{
    public function index()
    {
        $data = PenawaranSales::latest()->get();
        $total        = $data->count();
        $totalDraft   = $data->where('status', 'Draft')->count();
        $totalTerkirim = $data->where('status', 'Terkirim')->count();
        $totalDisetujui = $data->where('status', 'Disetujui')->count();

        return view('admin.sales.penawaran.index', compact(
            'data', 'total', 'totalDraft', 'totalTerkirim', 'totalDisetujui'
        ));
    }

    public function show($id)
    {
        return response()->json(PenawaranSales::findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_quotation' => 'required|unique:penawarans,no_quotation',
            'tanggal'      => 'required|date',
            'pelanggan'    => 'required',
            'produk_jasa'  => 'required',
            'jumlah'       => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric|min:0',
            'total_harga'  => 'required|numeric|min:0',
            'status'       => 'required',
            'valid_sampai' => 'required|date|after_or_equal:tanggal',
        ]);

        PenawaranSales::create($request->all());

        return back()->with('success', 'Penawaran berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $item = PenawaranSales::findOrFail($id);

        $request->validate([
            'no_quotation' => 'required|unique:penawarans,no_quotation,' . $id,
            'tanggal'      => 'required|date',
            'pelanggan'    => 'required',
            'produk_jasa'  => 'required',
            'jumlah'       => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric|min:0',
            'total_harga'  => 'required|numeric|min:0',
            'status'       => 'required',
            'valid_sampai' => 'required|date|after_or_equal:tanggal',
        ]);

        $item->update($request->all());

        return back()->with('success', 'Penawaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        PenawaranSales::findOrFail($id)->delete();
        return back()->with('success', 'Penawaran berhasil dihapus.');
    }

    public function pdf(Request $request)
    {
        $query = PenawaranSales::query();

        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('no_quotation', 'like', "%$s%")
                  ->orWhere('pelanggan', 'like', "%$s%")
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

        $pdf = Pdf::loadView('admin.sales.penawaran.pdf', compact('data', 'setting', 'logoSrc'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('data-penawaran-sales.pdf');
    }
}
