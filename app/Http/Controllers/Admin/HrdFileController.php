<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HrdFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HrdFileController extends Controller
{
    public function index()
    {
        $data = HrdFile::latest()->paginate(15)->withQueryString();

        $totalFile     = HrdFile::count();
        $totalPegawai  = HrdFile::distinct('nama_pegawai')->count('nama_pegawai');
        $jenisStats    = HrdFile::selectRaw('jenis_dokumen, count(*) as total')->groupBy('jenis_dokumen')->pluck('total', 'jenis_dokumen');
        $totalJenisDok = HrdFile::distinct('jenis_dokumen')->count('jenis_dokumen');

        return view('admin.hrd.file.index', compact(
            'data', 'totalFile', 'totalPegawai', 'totalJenisDok', 'jenisStats'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pegawai'  => 'required|string|max:255',
            'nama_file'     => 'required|string|max:255',
            'jenis_dokumen' => 'required|string|max:255',
            'file'          => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            'keterangan'    => 'nullable|string',
        ]);

        $path = $request->file('file')->store('hrd/files', 'public');

        HrdFile::create([
            'nama_pegawai'  => $validated['nama_pegawai'],
            'nama_file'     => $validated['nama_file'],
            'jenis_dokumen' => $validated['jenis_dokumen'],
            'file_path'     => $path,
            'keterangan'    => $validated['keterangan'] ?? null,
        ]);

        return redirect()->route('hrd-file.index')
            ->with('success', 'File berhasil diupload.');
    }

    public function update(Request $request, HrdFile $hrdFile)
    {
        $validated = $request->validate([
            'nama_pegawai'  => 'required|string|max:255',
            'nama_file'     => 'required|string|max:255',
            'jenis_dokumen' => 'required|string|max:255',
            'file'          => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            'keterangan'    => 'nullable|string',
        ]);

        if ($request->hasFile('file')) {
            // Hapus file lama jika ada
            if ($hrdFile->file_path) {
                Storage::disk('public')->delete($hrdFile->file_path);
            }
            $validated['file_path'] = $request->file('file')->store('hrd/files', 'public');
        }

        $hrdFile->update([
            'nama_pegawai'  => $validated['nama_pegawai'],
            'nama_file'     => $validated['nama_file'],
            'jenis_dokumen' => $validated['jenis_dokumen'],
            'file_path'     => $validated['file_path'] ?? $hrdFile->file_path,
            'keterangan'    => $validated['keterangan'] ?? null,
        ]);

        return redirect()->route('hrd-file.index')
            ->with('success', 'File berhasil diperbarui.');
    }

    public function destroy(HrdFile $hrdFile)
    {
        if ($hrdFile->file_path) {
            Storage::disk('public')->delete($hrdFile->file_path);
        }

        $hrdFile->delete();

        return redirect()->route('hrd-file.index')
            ->with('success', 'File berhasil dihapus.');
    }
}
