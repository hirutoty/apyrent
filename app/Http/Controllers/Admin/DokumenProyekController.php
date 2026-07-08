<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DokumenProyek;
use App\Models\IndukProyek;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DokumenProyekController extends Controller
{
    public function index()
    {
        $data       = DokumenProyek::latest()->get();
        $total      = $data->count();
        $totalValid  = $data->where('status', 'Valid')->count();
        $totalDraft  = $data->where('status', 'Draft')->count();
        $totalExpired= $data->where('status', 'Expired')->count();
        $proyeks     = IndukProyek::orderBy('kode')->pluck('nama_proyek', 'kode');

        return view('admin.project.dokumen.index', compact(
            'data', 'total', 'totalValid', 'totalDraft', 'totalExpired', 'proyeks'
        ));
    }

    public function show($id)
    {
        return response()->json(DokumenProyek::findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'proyek'        => 'required',
            'nama_dokumen'  => 'required',
            'status'        => 'required',
            'tanggal_upload'=> 'required|date',
        ]);

        $data = $request->except('file_upload');

        if ($request->hasFile('file_upload')) {
            $file = $request->file('file_upload');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/dokumen_proyek'), $filename);
            $data['file'] = 'uploads/dokumen_proyek/' . $filename;
            $data['tipe'] = strtoupper($file->getClientOriginalExtension());
        }

        DokumenProyek::create($data);

        return back()->with('success', 'Dokumen Proyek berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $item = DokumenProyek::findOrFail($id);

        $request->validate([
            'proyek'        => 'required',
            'nama_dokumen'  => 'required',
            'status'        => 'required',
            'tanggal_upload'=> 'required|date',
        ]);

        $data = $request->except('file_upload');

        if ($request->hasFile('file_upload')) {
            $file = $request->file('file_upload');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/dokumen_proyek'), $filename);
            $data['file'] = 'uploads/dokumen_proyek/' . $filename;
            $data['tipe'] = strtoupper($file->getClientOriginalExtension());
        }

        $item->update($data);

        return back()->with('success', 'Dokumen Proyek berhasil diperbarui.');
    }

    public function destroy($id)
    {
        DokumenProyek::findOrFail($id)->delete();
        return back()->with('success', 'Dokumen Proyek berhasil dihapus.');
    }

    public function pdf(Request $request)
    {
        $data    = DokumenProyek::latest()->get();
        $setting = Setting::first();
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.project.dokumen.pdf', compact('data', 'setting', 'logoSrc'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('dokumen-proyek.pdf');
    }
}
