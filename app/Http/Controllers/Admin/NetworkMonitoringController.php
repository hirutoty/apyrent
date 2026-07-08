<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NetworkMonitoring;
use Illuminate\Http\Request;

class NetworkMonitoringController extends Controller
{
    public function index()
    {
        $data           = NetworkMonitoring::latest()->paginate(15)->withQueryString();
        $totalPerangkat = NetworkMonitoring::count();
        $totalOnline    = NetworkMonitoring::where('status_koneksi', 'Online')->count();
        $totalOffline   = NetworkMonitoring::where('status_koneksi', 'Offline')->count();
        $totalWarning   = NetworkMonitoring::where('status_koneksi', 'Warning')->count();

        return view('admin.technology.networkm.index',
            compact('data','totalPerangkat','totalOnline','totalOffline','totalWarning'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lokasi'         => 'required|string|max:255',
            'ip_public'      => 'required|string|max:50',
            'status_koneksi' => 'required|in:Online,Offline,Warning,Maintenance',
            'bandwidth'      => 'required|string|max:50',
            'downtime'       => 'required|string|max:50',
            'catatan'        => 'nullable|string',
        ]);

        NetworkMonitoring::create($request->only([
            'lokasi','ip_public','status_koneksi','bandwidth','downtime','catatan'
        ]));

        return redirect()->route('networkm.index')->with('success', 'Network Monitoring berhasil ditambahkan.');
    }

    public function update(Request $request, NetworkMonitoring $networkm)
    {
        $request->validate([
            'lokasi'         => 'required|string|max:255',
            'ip_public'      => 'required|string|max:50',
            'status_koneksi' => 'required|in:Online,Offline,Warning,Maintenance',
            'bandwidth'      => 'required|string|max:50',
            'downtime'       => 'required|string|max:50',
            'catatan'        => 'nullable|string',
        ]);

        $networkm->update($request->only([
            'lokasi','ip_public','status_koneksi','bandwidth','downtime','catatan'
        ]));

        return redirect()->route('networkm.index')->with('success', 'Network Monitoring berhasil diperbarui.');
    }

    public function destroy(NetworkMonitoring $networkm)
    {
        $networkm->delete();
        return redirect()->route('networkm.index')->with('success', 'Network Monitoring berhasil dihapus.');
    }
}
