<?php

namespace App\Http\Controllers\Admin;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bukubesar;
use App\Exports\BukuBesarExport;
use App\Models\Setting;
use Maatwebsite\Excel\Facades\Excel;

class BukubesarController extends Controller
{
    public function index(Request $request)
{
    $search = $request->search;

    $query = BukuBesar::query();

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('kode_jurnal', 'like', "%$search%")
              ->orWhere('transaksi', 'like', "%$search%")
              ->orWhere('kategori', 'like', "%$search%")
              ->orWhere('aktivitas', 'like', "%$search%");
        });
    }

    $data = $query->latest()->get();

    return view('admin.bukubesar.index', compact('data', 'search'));
}

    public function store(Request $request)
    {
        $request->validate([
            'kode_jurnal' => 'required',
            'transaksi' => 'required',
            'kategori' => 'required',
            'tanggal' => 'required',
        ]);

        Bukubesar::create([
            'kode_jurnal' => $request->kode_jurnal,
            'transaksi' => $request->transaksi,
            'kategori' => $request->kategori,
            'tanggal' => $request->tanggal,
            'debit' => $request->debit ?? 0,
            'kredit' => $request->kredit ?? 0,
            'saldo' => $request->saldo ?? 0,
            'aktivitas' => $request->aktivitas,
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('success', 'Data berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $data = Bukubesar::findOrFail($id);

        $data->update([
            'kode_jurnal' => $request->kode_jurnal,
            'transaksi' => $request->transaksi,
            'kategori' => $request->kategori,
            'tanggal' => $request->tanggal,
            'debit' => $request->debit ?? 0,
            'kredit' => $request->kredit ?? 0,
            'saldo' => $request->saldo ?? 0,
            'aktivitas' => $request->aktivitas,
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        Bukubesar::findOrFail($id)->delete();

        return back()->with('success', 'Data berhasil dihapus');
    }



public function pdf(Request $request)
{
    $search = $request->query('search');

    $query = BukuBesar::query();

    if (!empty($search)) {
        $query->where(function ($q) use ($search) {
            $q->where('kode_jurnal', 'like', "%$search%")
              ->orWhere('transaksi', 'like', "%$search%")
              ->orWhere('kategori', 'like', "%$search%")
              ->orWhere('aktivitas', 'like', "%$search%");
        });
    }

      $setting = Setting::first();
    $data = $query->orderBy('tanggal', 'desc')->get();

    $pdf = Pdf::loadView('admin.bukubesar.pdf', compact('data', 'search', 'setting'));

    return $pdf->stream('buku-besar.pdf');
}



public function exportExcel(Request $request)
{
    $filename = 'buku_besar_' . now()->format('Ymd_His') . '.xlsx';

    return Excel::download(
        new BukuBesarExport($request->search),
        $filename
    );
}
}