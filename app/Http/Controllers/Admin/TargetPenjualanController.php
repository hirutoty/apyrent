<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TargetPenjualan;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class TargetPenjualanController extends Controller
{
    public function index(Request $request)
    {
        $query = TargetPenjualan::latest();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sq) use ($q) {
                $sq->where('nama_sales', 'like', "%$q%")
                   ->orWhere('bulan', 'like', "%$q%");
            });
        }

        $data            = $query->paginate(15)->withQueryString();
        $total           = TargetPenjualan::count();
        $totalTarget     = TargetPenjualan::sum('target_penjualan');
        $totalPencapaian = TargetPenjualan::sum('pencapaian');
        $persentase      = $totalTarget > 0 ? round(($totalPencapaian / $totalTarget) * 100, 1) : 0;

        return view('admin.sales.target_penjualan.index', compact(
            'data', 'total', 'totalTarget', 'totalPencapaian', 'persentase'
        ));
    }

    public function show($id)
    {
        return response()->json(TargetPenjualan::findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_sales'      => 'required',
            'bulan'           => 'required',
            'target_penjualan'=> 'required|numeric|min:0',
            'pencapaian'      => 'required|numeric|min:0',
        ]);

        TargetPenjualan::create($request->all());

        return back()->with('success', 'Target Penjualan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $item = TargetPenjualan::findOrFail($id);

        $request->validate([
            'nama_sales'      => 'required',
            'bulan'           => 'required',
            'target_penjualan'=> 'required|numeric|min:0',
            'pencapaian'      => 'required|numeric|min:0',
        ]);

        $item->update($request->all());

        return back()->with('success', 'Target Penjualan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        TargetPenjualan::findOrFail($id)->delete();
        return back()->with('success', 'Target Penjualan berhasil dihapus.');
    }

    public function pdf(Request $request)
    {
        $query = TargetPenjualan::query();

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

        $pdf = Pdf::loadView('admin.sales.target_penjualan.pdf', compact('data', 'setting', 'logoSrc'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('data-target-penjualan.pdf');
    }
}
