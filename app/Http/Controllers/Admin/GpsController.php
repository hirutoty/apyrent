<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GpsKendaraan;
use App\Models\GpsKendaraanHistory;
use App\Models\Gps;
use App\Models\Kendaraan;
use App\Models\Setting;
use App\Models\Keuangan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class GpsController extends Controller
{
    public function index()
    {
        $data = Gps::with('user')->latest()->paginate(15)->withQueryString();
        $setting = Setting::first();

        $reminder = match ($setting->satuan_reminder) {
            'hari'    => $setting->batas_reminder,
            'minggu'  => $setting->batas_reminder * 7,
            'bulan'   => $setting->batas_reminder * 30,
            'tahun'   => $setting->batas_reminder * 365,
            default   => $setting->batas_reminder,
        };

        return view('admin.gps.index', [
            'data' => $data,
            'setting' => $reminder,
            'users' => User::all()
        ]);
    }

    public function store(Request $request)
{
    $request->validate([
        'nama_gps' => 'required|unique:gps,nama_gps',
        'alamat' => 'nullable',
        'nama_marketing' => 'nullable',
        'kontak_marketing' => 'nullable',
        'nama_bengkel' => 'nullable',
        'kontak_bengkel' => 'nullable',
    ], [
        'nama_gps.required' => 'Nama GPS wajib diisi',
        'nama_gps.unique' => 'Nama GPS sudah digunakan',
    ]);

    Gps::create([
        'user_id' => auth()->id(), // 🔥 AUTO USER LOGIN
        'nama_gps' => $request->nama_gps,
        'alamat' => $request->alamat,
        'nama_marketing' => $request->nama_marketing,
        'kontak_marketing' => $request->kontak_marketing,
        'nama_bengkel' => $request->nama_bengkel,
        'kontak_bengkel' => $request->kontak_bengkel,
    ]);

    return back()->with('success', 'Data GPS berhasil ditambahkan');
}

    public function update(Request $request, $id)
{
    $gps = Gps::findOrFail($id);

    $request->validate([
        'nama_gps' => 'required|unique:gps,nama_gps,' . $id,
    ]);

    $gps->update([
        'user_id' => auth()->id(), // tetap otomatis update user terakhir edit
        'nama_gps' => $request->nama_gps,
        'alamat' => $request->alamat,
        'nama_marketing' => $request->nama_marketing,
        'kontak_marketing' => $request->kontak_marketing,
        'nama_bengkel' => $request->nama_bengkel,
        'kontak_bengkel' => $request->kontak_bengkel,
    ]);

    return back()->with('success', 'Data GPS berhasil diupdate');
}
    public function destroy($id)
    {
        Gps::findOrFail($id)->delete();

        return back()->with('success', 'Data GPS berhasil dihapus');
    }

    
}