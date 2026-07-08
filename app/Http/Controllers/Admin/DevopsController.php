<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Devops;
use Illuminate\Http\Request;

class DevopsController extends Controller
{
    public function index()
    {
        $data           = Devops::latest()->paginate(15)->withQueryString();
        $totalPipeline  = Devops::count();
        $totalAktif     = Devops::where('status', 'Aktif')->count();
        $totalNonaktif  = Devops::where('status', 'Nonaktif')->count();
        $totalOtomatis  = Devops::where('deployment_otomatis', 'Ya')->count();

        return view('admin.technology.devops.index',
            compact('data','totalPipeline','totalAktif','totalNonaktif','totalOtomatis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'aplikasi'              => 'required|string|max:255',
            'tools'                 => 'required|string|max:255',
            'deployment_otomatis'   => 'required|in:Ya,Tidak',
            'jadwal_build'          => 'required|string|max:100',
            'status'                => 'required|in:Aktif,Nonaktif',
        ]);

        Devops::create($request->only([
            'aplikasi','tools','deployment_otomatis','jadwal_build','status'
        ]));

        return redirect()->route('devops.index')->with('success', 'Data DevOps berhasil ditambahkan.');
    }

    public function update(Request $request, Devops $devops)
    {
        $request->validate([
            'aplikasi'              => 'required|string|max:255',
            'tools'                 => 'required|string|max:255',
            'deployment_otomatis'   => 'required|in:Ya,Tidak',
            'jadwal_build'          => 'required|string|max:100',
            'status'                => 'required|in:Aktif,Nonaktif',
        ]);

        $devops->update($request->only([
            'aplikasi','tools','deployment_otomatis','jadwal_build','status'
        ]));

        return redirect()->route('devops.index')->with('success', 'Data DevOps berhasil diperbarui.');
    }

    public function destroy(Devops $devops)
    {
        $devops->delete();
        return redirect()->route('devops.index')->with('success', 'Data DevOps berhasil dihapus.');
    }
}
