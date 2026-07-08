<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Otomatisasi;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class OtomatisasiController extends Controller
{
    public function index(Request $request)
    {
        $query = Otomatisasi::latest();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sq) use ($q) {
                $sq->where('workflow_id', 'like', "%$q%")
                   ->orWhere('nama_workflow', 'like', "%$q%")
                   ->orWhere('trigger_event', 'like', "%$q%");
            });
        }

        $data          = $query->paginate(15)->withQueryString();
        $total         = Otomatisasi::count();
        $totalAktif    = Otomatisasi::where('status', 'Aktif')->count();
        $totalNonaktif = Otomatisasi::where('status', 'Nonaktif')->count();

        return view('admin.marketing.otomatisasi.index', compact(
            'data', 'total', 'totalAktif', 'totalNonaktif'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'workflow_id'       => 'required|unique:otomatisasis,workflow_id',
            'nama_workflow'     => 'required',
            'trigger_event'     => 'required',
            'aksi'              => 'required',
            'status'            => 'required',
        ]);

        Otomatisasi::create($request->all());

        return back()->with('success', 'Otomatisasi berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $otomatisasi = Otomatisasi::findOrFail($id);

        $request->validate([
            'workflow_id'       => 'required|unique:otomatisasis,workflow_id,' . $id,
            'nama_workflow'     => 'required',
            'trigger_event'     => 'required',
            'aksi'              => 'required',
            'status'            => 'required',
        ]);

        $otomatisasi->update($request->all());

        return back()->with('success', 'Otomatisasi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Otomatisasi::findOrFail($id)->delete();

        return back()->with('success', 'Otomatisasi berhasil dihapus.');
    }

    public function pdf(Request $request)
    {
        $query = Otomatisasi::query();

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_workflow', 'like', "%$search%")
                  ->orWhere('workflow_id', 'like', "%$search%")
                  ->orWhere('trigger_event', 'like', "%$search%")
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

        $pdf = Pdf::loadView('admin.marketing.otomatisasi.pdf', compact('data', 'setting', 'logoSrc'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('data-otomatisasi.pdf');
    }
}
