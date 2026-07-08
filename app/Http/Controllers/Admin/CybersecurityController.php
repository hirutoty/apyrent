<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cybersecurity;
use Illuminate\Http\Request;

class CybersecurityController extends Controller
{
    public function index()
    {
        $data           = Cybersecurity::latest()->paginate(15)->withQueryString();
        $totalTemuan    = Cybersecurity::count();
        $totalOpen      = Cybersecurity::where('status', 'Open')->count();
        $totalResolved  = Cybersecurity::where('status', 'Resolved')->count();
        $totalCritical  = Cybersecurity::where('level_risiko', 'Critical')->count();

        return view('admin.technology.cybers.index',
            compact('data','totalTemuan','totalOpen','totalResolved','totalCritical'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_audit'      => 'required|date',
            'area_diaudit'       => 'required|string|max:255',
            'temuan_risiko'      => 'required|string',
            'level_risiko'       => 'required|in:Low,Medium,High,Critical',
            'tindakan_perbaikan' => 'required|string',
            'status'             => 'required|in:Open,In Progress,Resolved,Closed',
        ]);

        Cybersecurity::create($request->only([
            'tanggal_audit','area_diaudit','temuan_risiko','level_risiko','tindakan_perbaikan','status'
        ]));

        return redirect()->route('cybers.index')->with('success', 'Data Cybersecurity berhasil ditambahkan.');
    }

    public function update(Request $request, Cybersecurity $cybers)
    {
        $request->validate([
            'tanggal_audit'      => 'required|date',
            'area_diaudit'       => 'required|string|max:255',
            'temuan_risiko'      => 'required|string',
            'level_risiko'       => 'required|in:Low,Medium,High,Critical',
            'tindakan_perbaikan' => 'required|string',
            'status'             => 'required|in:Open,In Progress,Resolved,Closed',
        ]);

        $cybers->update($request->only([
            'tanggal_audit','area_diaudit','temuan_risiko','level_risiko','tindakan_perbaikan','status'
        ]));

        return redirect()->route('cybers.index')->with('success', 'Data Cybersecurity berhasil diperbarui.');
    }

    public function destroy(Cybersecurity $cybers)
    {
        $cybers->delete();
        return redirect()->route('cybers.index')->with('success', 'Data Cybersecurity berhasil dihapus.');
    }
}
