<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LegalDocument;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LegalDocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = LegalDocument::latest();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sq) use ($q) {
                $sq->where('kode', 'like', "%$q%")
                   ->orWhere('nama_dokumen', 'like', "%$q%")
                   ->orWhere('pihak_terkait', 'like', "%$q%");
            });
        }

        $data       = $query->paginate(15)->withQueryString();
        $total      = LegalDocument::count();
        $totalAktif = LegalDocument::where('status', 'Aktif')->count();
        $totalExp   = LegalDocument::where('status', 'Kadaluarsa')->count();

        return view('admin.legal.legal_document.index', compact('data', 'total', 'totalAktif', 'totalExp'));
    }

    public function show($id)
    {
        return response()->json(LegalDocument::findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode'         => 'required|unique:legal_documents,kode',
            'nama_dokumen' => 'required',
            'jenis'        => 'required',
            'pihak_terkait'=> 'required',
            'tgl_terbit'   => 'required|date',
            'status'       => 'required',
            'format'       => 'required',
        ]);

        LegalDocument::create($request->all());

        return back()->with('success', 'Dokumen Legal berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $item = LegalDocument::findOrFail($id);

        $request->validate([
            'kode'         => 'required|unique:legal_documents,kode,' . $id,
            'nama_dokumen' => 'required',
            'jenis'        => 'required',
            'pihak_terkait'=> 'required',
            'tgl_terbit'   => 'required|date',
            'status'       => 'required',
            'format'       => 'required',
        ]);

        $item->update($request->all());

        return back()->with('success', 'Dokumen Legal berhasil diperbarui.');
    }

    public function destroy($id)
    {
        LegalDocument::findOrFail($id)->delete();
        return back()->with('success', 'Dokumen Legal berhasil dihapus.');
    }

    public function pdf(Request $request)
    {
        $query = LegalDocument::query();
        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama_dokumen', 'like', "%$s%")
                  ->orWhere('kode', 'like', "%$s%")
                  ->orWhere('jenis', 'like', "%$s%")
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
        $pdf = Pdf::loadView('admin.legal.legal_document.pdf', compact('data', 'setting', 'logoSrc'))
            ->setPaper('a4', 'landscape');
        return $pdf->stream('dokumen-legal.pdf');
    }
}
