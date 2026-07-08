<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SoftwareLicense;
use Illuminate\Http\Request;

class SoftwareLicenseController extends Controller
{
    public function index()
    {
        $data           = SoftwareLicense::latest()->paginate(15)->withQueryString();
        $totalLisensi   = SoftwareLicense::count();
        $totalAktif     = SoftwareLicense::where('status', 'Aktif')->count();
        $totalExpired   = SoftwareLicense::where('status', 'Expired')->count();
        $totalUnit      = SoftwareLicense::sum('jumlah_lisensi');

        return view('admin.technology.softwarel.index',
            compact('data','totalLisensi','totalAktif','totalExpired','totalUnit'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_software'          => 'required|string|max:255',
            'jenis_lisensi'          => 'required|string|max:100',
            'jumlah_lisensi'         => 'required|integer|min:1',
            'provider'               => 'required|string|max:255',
            'masa_berlaku'           => 'required|date',
            'status'                 => 'required|string|max:50',
            'tanggal_perpanjangan'   => 'nullable|date',
        ]);

        SoftwareLicense::create($request->only([
            'nama_software','jenis_lisensi','jumlah_lisensi','provider',
            'masa_berlaku','status','tanggal_perpanjangan'
        ]));

        return redirect()->route('softwarel.index')->with('success', 'Software License berhasil ditambahkan.');
    }

    public function update(Request $request, SoftwareLicense $softwarel)
    {
        $request->validate([
            'nama_software'          => 'required|string|max:255',
            'jenis_lisensi'          => 'required|string|max:100',
            'jumlah_lisensi'         => 'required|integer|min:1',
            'provider'               => 'required|string|max:255',
            'masa_berlaku'           => 'required|date',
            'status'                 => 'required|string|max:50',
            'tanggal_perpanjangan'   => 'nullable|date',
        ]);

        $softwarel->update($request->only([
            'nama_software','jenis_lisensi','jumlah_lisensi','provider',
            'masa_berlaku','status','tanggal_perpanjangan'
        ]));

        return redirect()->route('softwarel.index')->with('success', 'Software License berhasil diperbarui.');
    }

    public function destroy(SoftwareLicense $softwarel)
    {
        $softwarel->delete();
        return redirect()->route('softwarel.index')->with('success', 'Software License berhasil dihapus.');
    }
}
