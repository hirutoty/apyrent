<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Procuremento;
use Illuminate\Http\Request;

class ProcurementoController extends Controller
{
    public function index()
    {
        $data = Procuremento::latest()->paginate(15)->withQueryString();

        $statusStats = Procuremento::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
        $picStats    = Procuremento::selectRaw('pic, count(*) as total')->groupBy('pic')->pluck('total', 'pic');

        $totalWorkflow = Procuremento::count();
        $totalAktif    = Procuremento::where('status', 'Aktif')->count();
        $totalNonaktif = Procuremento::where('status', 'Nonaktif')->count();

        return view('admin.procuremento.index', compact(
            'data', 'statusStats', 'picStats',
            'totalWorkflow', 'totalAktif', 'totalNonaktif'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_workflow'   => 'required|string|max:255',
            'trigger_event'   => 'required|string|max:255',
            'syarat_tambahan' => 'required|string|max:255',
            'aksi_dilakukan'  => 'required|string|max:255',
            'delay_aksi'      => 'required|string|max:255',
            'status'          => 'required|in:Aktif,Nonaktif',
            'pic'             => 'required|string|max:255',
            'catatan'         => 'nullable|string',
        ]);

        // workflow_id otomatis di-generate lewat Model::boot()
        Procuremento::create($validated);

        return redirect()->route('procuremento.index')
            ->with('success', 'Workflow berhasil ditambahkan.');
    }

    public function update(Request $request, Procuremento $procuremento)
    {
        $validated = $request->validate([
            'nama_workflow'   => 'required|string|max:255',
            'trigger_event'   => 'required|string|max:255',
            'syarat_tambahan' => 'required|string|max:255',
            'aksi_dilakukan'  => 'required|string|max:255',
            'delay_aksi'      => 'required|string|max:255',
            'status'          => 'required|in:Aktif,Nonaktif',
            'pic'             => 'required|string|max:255',
            'catatan'         => 'nullable|string',
        ]);

        // workflow_id sengaja tidak diubah saat update
        $procuremento->update($validated);

        return redirect()->route('procuremento.index')
            ->with('success', 'Workflow berhasil diperbarui.');
    }

    public function destroy(Procuremento $procuremento)
    {
        $procuremento->delete();

        return redirect()->route('procuremento.index')
            ->with('success', 'Workflow berhasil dihapus.');
    }
}