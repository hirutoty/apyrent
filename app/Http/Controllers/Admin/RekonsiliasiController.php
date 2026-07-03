<?php

namespace App\Http\Controllers\Admin;

use App\Exports\RekonsiliasiExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RekonsiliasiBank;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

class RekonsiliasiController extends Controller
{
    public function index()
    {
        $data = RekonsiliasiBank::latest()->get();

        return view('admin.rekonsiliasi.index', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required',
            'deskripsi' => 'required',
            'reference_no' => 'required',
            'amount' => 'required|numeric',
        ]);

        RekonsiliasiBank::create([
            'tanggal' => $request->tanggal,
            'deskripsi' => $request->deskripsi,
            'reference_no' => $request->reference_no,
            'amount' => $request->amount,
            'currency' => $request->currency,
            'status_rekonsiliasi' => $request->status_rekonsiliasi,
            'invoice_id' => $request->invoice_id,
        ]);

        return back()->with('success', 'Data berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $data = RekonsiliasiBank::findOrFail($id);

        $data->update([
            'tanggal' => $request->tanggal,
            'deskripsi' => $request->deskripsi,
            'reference_no' => $request->reference_no,
            'amount' => $request->amount,
            'currency' => $request->currency,
            'status_rekonsiliasi' => $request->status_rekonsiliasi,
            'invoice_id' => $request->invoice_id,
        ]);

        return back()->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        RekonsiliasiBank::findOrFail($id)->delete();

        return back()->with('success', 'Data berhasil dihapus');
    }

   
public function pdf(Request $request)
{
    $query = RekonsiliasiBank::query();

    // 🔎 FILTER SEARCH (SAMA SEPERTI TABLE)
    if ($request->search) {
        $query->where(function ($q) use ($request) {
            $q->where('deskripsi', 'like', "%{$request->search}%")
              ->orWhere('reference_no', 'like', "%{$request->search}%")
              ->orWhere('currency', 'like', "%{$request->search}%")
              ->orWhere('status_rekonsiliasi', 'like', "%{$request->search}%");
        });
    }

    $setting = Setting::first();
    $data = $query->orderBy('tanggal', 'desc')->get();

    $pdf = PDF::loadView('admin.rekonsiliasi.pdf', compact('data', 'setting'));

    return $pdf->stream('rekonsiliasi-bank.pdf');
}



public function exportExcel(Request $request)
{
    $filename = 'rekonsiliasi_' . now()->format('Ymd_His') . '.xlsx';

    return Excel::download(
        new RekonsiliasiExport($request->search),
        $filename
    );
}
}