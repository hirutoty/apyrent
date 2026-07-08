<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrmProspek;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CrmProspekController extends Controller
{
    public function index(Request $request)
    {
        $query = CrmProspek::latest();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sq) use ($q) {
                $sq->where('kode_prospek', 'like', "%$q%")
                   ->orWhere('nama_kontak', 'like', "%$q%")
                   ->orWhere('perusahaan', 'like', "%$q%")
                   ->orWhere('sales', 'like', "%$q%");
            });
        }

        $data  = $query->paginate(15)->withQueryString();
        $total          = CrmProspek::count();
        $totalProspek   = CrmProspek::where('tahapan', 'Prospek')->count();
        $totalNegotiasi = CrmProspek::where('tahapan', 'Negosiasi')->count();
        $totalClosing   = CrmProspek::where('tahapan', 'Closing')->count();

        return view('admin.sales.crm_prospek.index', compact(
            'data', 'total', 'totalProspek', 'totalNegotiasi', 'totalClosing'
        ));
    }

    public function show($id)
    {
        $item = CrmProspek::findOrFail($id);
        return response()->json($item);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_prospek'  => 'required|unique:crm_prospeks,kode_prospek',
            'nama_kontak'   => 'required',
            'perusahaan'    => 'required',
            'tahapan'       => 'required',
            'status'        => 'required',
            'sales'         => 'required',
            'tanggal_masuk' => 'required|date',
        ]);

        CrmProspek::create($request->all());

        return back()->with('success', 'CRM Prospek berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $item = CrmProspek::findOrFail($id);

        $request->validate([
            'kode_prospek'  => 'required|unique:crm_prospeks,kode_prospek,' . $id,
            'nama_kontak'   => 'required',
            'perusahaan'    => 'required',
            'tahapan'       => 'required',
            'status'        => 'required',
            'sales'         => 'required',
            'tanggal_masuk' => 'required|date',
        ]);

        $item->update($request->all());

        return back()->with('success', 'CRM Prospek berhasil diperbarui.');
    }

    public function destroy($id)
    {
        CrmProspek::findOrFail($id)->delete();
        return back()->with('success', 'CRM Prospek berhasil dihapus.');
    }

    public function pdf(Request $request)
    {
        $query = CrmProspek::query();

        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama_kontak', 'like', "%$s%")
                  ->orWhere('kode_prospek', 'like', "%$s%")
                  ->orWhere('perusahaan', 'like', "%$s%")
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

        $pdf = Pdf::loadView('admin.sales.crm_prospek.pdf', compact('data', 'setting', 'logoSrc'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('data-crm-prospek.pdf');
    }
}
