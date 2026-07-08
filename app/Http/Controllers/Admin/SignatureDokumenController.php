<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SignatureDokumen;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SignatureDokumenController extends Controller
{
    public function index(Request $request)
    {
        $query = SignatureDokumen::latest();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sq) use ($q) {
                $sq->where('document_id', 'like', "%$q%")
                   ->orWhere('jenis_dokumen', 'like', "%$q%")
                   ->orWhere('pihak_terlibat', 'like', "%$q%");
            });
        }

        $data                = $query->paginate(15)->withQueryString();
        $total               = SignatureDokumen::count();
        $totalMenunggu       = SignatureDokumen::where('status_ttd', 'Menunggu')->count();
        $totalDitandatangani = SignatureDokumen::where('status_ttd', 'Ditandatangani')->count();
        $totalDitolak        = SignatureDokumen::where('status_ttd', 'Ditolak')->count();

        return view('admin.sales.signature_dokumen.index', compact(
            'data', 'total', 'totalMenunggu', 'totalDitandatangani', 'totalDitolak'
        ));
    }

    public function show($id)
    {
        return response()->json(SignatureDokumen::findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'document_id'      => 'required|unique:signature_dokumens,document_id',
            'jenis_dokumen'    => 'required',
            'tanggal'          => 'required|date',
            'pihak_terlibat'   => 'required',
            'status_ttd'       => 'required',
            'platform_digisign'=> 'required',
        ]);

        SignatureDokumen::create($request->all());

        return back()->with('success', 'Signature Dokumen berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $item = SignatureDokumen::findOrFail($id);

        $request->validate([
            'document_id'      => 'required|unique:signature_dokumens,document_id,' . $id,
            'jenis_dokumen'    => 'required',
            'tanggal'          => 'required|date',
            'pihak_terlibat'   => 'required',
            'status_ttd'       => 'required',
            'platform_digisign'=> 'required',
        ]);

        $item->update($request->all());

        return back()->with('success', 'Signature Dokumen berhasil diperbarui.');
    }

    public function destroy($id)
    {
        SignatureDokumen::findOrFail($id)->delete();
        return back()->with('success', 'Signature Dokumen berhasil dihapus.');
    }

    public function pdf(Request $request)
    {
        $query = SignatureDokumen::query();

        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('document_id', 'like', "%$s%")
                  ->orWhere('jenis_dokumen', 'like', "%$s%")
                  ->orWhere('pihak_terlibat', 'like', "%$s%")
                  ->orWhere('status_ttd', 'like', "%$s%");
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

        $pdf = Pdf::loadView('admin.sales.signature_dokumen.pdf', compact('data', 'setting', 'logoSrc'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('data-signature-dokumen.pdf');
    }
}
