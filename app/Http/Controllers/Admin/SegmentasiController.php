<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Segmentasi;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SegmentasiController extends Controller
{
    public function index()
    {
        $data = Segmentasi::latest()->get();
        $total = $data->count();
        $totalAktif = $data->where('status', 'Aktif')->count();
        $totalNonaktif = $data->where('status', 'Tidak Aktif')->count();
        $totalCustomer = $data->sum('customer_count');

        return view('admin.marketing.segmentasi.index', compact(
            'data', 'total', 'totalAktif', 'totalNonaktif', 'totalCustomer'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'segment_code'           => 'required|unique:segmentasis,segment_code',
            'segment_name'           => 'required',
            'segmentation_criteria'  => 'required',
            'customer_count'         => 'required|integer|min:0',
            'status'                 => 'required',
        ]);

        Segmentasi::create($request->all());

        return back()->with('success', 'Segmentasi berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $segmentasi = Segmentasi::findOrFail($id);

        $request->validate([
            'segment_code'           => 'required|unique:segmentasis,segment_code,' . $id,
            'segment_name'           => 'required',
            'segmentation_criteria'  => 'required',
            'customer_count'         => 'required|integer|min:0',
            'status'                 => 'required',
        ]);

        $segmentasi->update($request->all());

        return back()->with('success', 'Segmentasi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Segmentasi::findOrFail($id)->delete();

        return back()->with('success', 'Segmentasi berhasil dihapus.');
    }

    public function pdf(Request $request)
    {
        $query = Segmentasi::query();

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('segment_name', 'like', "%$search%")
                  ->orWhere('segment_code', 'like', "%$search%")
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

        $pdf = Pdf::loadView('admin.marketing.segmentasi.pdf', compact('data', 'setting', 'logoSrc'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('data-segmentasi.pdf');
    }
}
