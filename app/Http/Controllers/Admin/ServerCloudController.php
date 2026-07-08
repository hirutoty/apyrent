<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServerCloud;
use Illuminate\Http\Request;

class ServerCloudController extends Controller
{
    public function index()
    {
        $data         = ServerCloud::latest()->paginate(15)->withQueryString();
        $totalServer  = ServerCloud::count();
        $totalAktif   = ServerCloud::where('status', 'Aktif')->count();
        $totalDown    = ServerCloud::where('status', 'Nonaktif')->count();
        $totalBackup  = ServerCloud::where('backup_aktif', true)->count();

        return view('admin.technology.serverc.index',
            compact('data','totalServer','totalAktif','totalDown','totalBackup'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_server'    => 'required|string|max:255',
            'jenis_server'   => 'required|string|max:100',
            'lokasi'         => 'required|string|max:255',
            'os'             => 'required|string|max:100',
            'provider_cloud' => 'nullable|string|max:100',
            'status'         => 'required|in:Aktif,Nonaktif,Maintenance',
            'backup_aktif'   => 'required|in:0,1',
        ]);

        ServerCloud::create($request->only([
            'nama_server','jenis_server','lokasi','os','provider_cloud','status','backup_aktif'
        ]));

        return redirect()->route('serverc.index')->with('success', 'Server & Cloud berhasil ditambahkan.');
    }

    public function update(Request $request, ServerCloud $serverc)
    {
        $request->validate([
            'nama_server'    => 'required|string|max:255',
            'jenis_server'   => 'required|string|max:100',
            'lokasi'         => 'required|string|max:255',
            'os'             => 'required|string|max:100',
            'provider_cloud' => 'nullable|string|max:100',
            'status'         => 'required|in:Aktif,Nonaktif,Maintenance',
            'backup_aktif'   => 'required|in:0,1',
        ]);

        $serverc->update($request->only([
            'nama_server','jenis_server','lokasi','os','provider_cloud','status','backup_aktif'
        ]));

        return redirect()->route('serverc.index')->with('success', 'Server & Cloud berhasil diperbarui.');
    }

    public function destroy(ServerCloud $serverc)
    {
        $serverc->delete();
        return redirect()->route('serverc.index')->with('success', 'Server & Cloud berhasil dihapus.');
    }
}
