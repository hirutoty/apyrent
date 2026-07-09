<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pelanggan;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

class PelangganController extends Controller
{
    public function index()
    {
        return view('admin.pelanggan.index', [
            'data' => Pelanggan::latest()->paginate(15)->withQueryString()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:255|unique:member,nama_pelanggan',
            'kontak_pelanggan' => 'nullable|string|max:50',
            'email_pelanggan' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'jenis_pelanggan' => 'required|in:perorangan,perusahaan',
        ], [
            'nama_pelanggan.required' => 'Nama pelanggan wajib diisi',
            'nama_pelanggan.unique' => 'Nama pelanggan sudah digunakan, tidak boleh sama',
            'jenis_pelanggan.required' => 'Jenis pelanggan wajib diisi',
        ]);

        Pelanggan::create([
            'nama_pelanggan' => $request->nama_pelanggan,
            'kontak_pelanggan' => $request->kontak_pelanggan,
            'email_pelanggan' => $request->email_pelanggan,
            'alamat' => $request->alamat,
            'jenis_pelanggan' => $request->jenis_pelanggan,
        ]);

        return back()->with('success', 'Pelanggan berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);

        $request->validate([
            'nama_pelanggan' => 'required|string|max:255|unique:member,nama_pelanggan,' . $id,
            'kontak_pelanggan' => 'string|max:50',
            'email_pelanggan' => 'nullable|max:50',
            'alamat' => 'nullable|string',
            'jenis_pelanggan' => 'required|in:perorangan,perusahaan',
        ], [
            'nama_pelanggan.required' => 'Nama pelanggan wajib diisi',
            'jenis_pelanggan.required' => 'Jenis pelanggan wajib diisi',
            'nama_pelanggan.unique' => 'Nama pelanggan sudah dipakai pelanggan lain',
        ]);

        $pelanggan->update([
            'nama_pelanggan' => $request->nama_pelanggan,
            'kontak_pelanggan' => $request->kontak_pelanggan,
            'email_pelanggan' => $request->email_pelanggan,
            'alamat' => $request->alamat,
            'jenis_pelanggan' => $request->jenis_pelanggan,
        ]);

        return back()->with('success', 'Pelanggan berhasil diupdate');
    }

    public function destroy($id)
    {
        Pelanggan::findOrFail($id)->delete();

        return back()->with('success', 'Pelanggan berhasil dihapus');
    }

    public function pdf(Request $request)
    {
        $query = Pelanggan::query();

        // ── FILTER SEARCH ──
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_pelanggan', 'like', '%' . $request->search . '%')
                    ->orWhere('kontak_pelanggan', 'like', '%' . $request->search . '%')
                    ->orWhere('email_pelanggan', 'like', '%' . $request->search . '%')
                    ->orWhere('alamat', 'like', '%' . $request->search . '%')
                    ->orWhere('jenis_pelanggan', 'like', '%' . $request->search . '%');
            });
        }

        $setting = Setting::first();
        // Base64 logo untuk DomPDF
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }
        $data = $query->latest()->get();

        $pdf = PDF::loadView('admin.pelanggan.pdf', compact('data', 'request', 'setting', 'logoSrc'));

        return $pdf->stream('data-pelanggan.pdf');
    }
}
