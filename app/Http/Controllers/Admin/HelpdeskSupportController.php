<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HelpdeskSupport;
use Illuminate\Http\Request;

class HelpdeskSupportController extends Controller
{
    public function index()
    {
        $data           = HelpdeskSupport::latest()->paginate(15)->withQueryString();
        $totalTiket     = HelpdeskSupport::count();
        $totalOpen      = HelpdeskSupport::where('status', 'Open')->count();
        $totalProgress  = HelpdeskSupport::where('status', 'In Progress')->count();
        $totalClosed    = HelpdeskSupport::where('status', 'Closed')->count();

        return view('admin.technology.helpdesk.index',
            compact('data','totalTiket','totalOpen','totalProgress','totalClosed'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_tiket'     => 'required|string|max:50',
            'tanggal'      => 'required|date',
            'departemen'   => 'required|string|max:100',
            'masalah'      => 'required|string',
            'prioritas'    => 'required|in:Low,Medium,High,Critical',
            'status'       => 'required|in:Open,In Progress,Resolved,Closed',
            'teknisi'      => 'required|string|max:255',
            'waktu_respon' => 'required|string|max:50',
        ]);

        HelpdeskSupport::create($request->only([
            'no_tiket','tanggal','departemen','masalah','prioritas','status','teknisi','waktu_respon'
        ]));

        return redirect()->route('helpdesk.index')->with('success', 'Tiket Helpdesk berhasil ditambahkan.');
    }

    public function update(Request $request, HelpdeskSupport $helpdesk)
    {
        $request->validate([
            'no_tiket'     => 'required|string|max:50',
            'tanggal'      => 'required|date',
            'departemen'   => 'required|string|max:100',
            'masalah'      => 'required|string',
            'prioritas'    => 'required|in:Low,Medium,High,Critical',
            'status'       => 'required|in:Open,In Progress,Resolved,Closed',
            'teknisi'      => 'required|string|max:255',
            'waktu_respon' => 'required|string|max:50',
        ]);

        $helpdesk->update($request->only([
            'no_tiket','tanggal','departemen','masalah','prioritas','status','teknisi','waktu_respon'
        ]));

        return redirect()->route('helpdesk.index')->with('success', 'Tiket Helpdesk berhasil diperbarui.');
    }

    public function destroy(HelpdeskSupport $helpdesk)
    {
        $helpdesk->delete();
        return redirect()->route('helpdesk.index')->with('success', 'Tiket Helpdesk berhasil dihapus.');
    }
}
