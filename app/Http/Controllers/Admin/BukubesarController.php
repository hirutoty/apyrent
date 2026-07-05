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

    $query = Bukubesar::query();

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

    $query = Bukubesar::query();

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

public function exportCsv(Request $request)
{
    $search = $request->search;

    $query = Bukubesar::query();
    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('kode_jurnal', 'like', "%$search%")
              ->orWhere('transaksi',  'like', "%$search%")
              ->orWhere('kategori',   'like', "%$search%")
              ->orWhere('aktivitas',  'like', "%$search%");
        });
    }
    $data = $query->orderBy('tanggal')->get();

    $filename = 'buku_besar_' . now()->format('Ymd_His') . '.csv';

    $headers = [
        'Content-Type'        => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"$filename\"",
    ];

    $callback = function () use ($data) {
        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['No','Kode Jurnal','Transaksi','Kategori','Tanggal','Debit (Rp)','Kredit (Rp)','Saldo (Rp)','Aktivitas','Keterangan']);
        foreach ($data as $i => $row) {
            fputcsv($handle, [
                $i + 1,
                $row->kode_jurnal,
                $row->transaksi,
                $row->kategori,
                $row->tanggal,
                $row->debit,
                $row->kredit,
                $row->saldo,
                $row->aktivitas,
                $row->keterangan,
            ]);
        }
        fclose($handle);
    };

    return response()->stream($callback, 200, $headers);
}

public function exportCsvLabaRugi(Request $request)
{
    $data = Bukubesar::all();

    $pendapatan = $data->where('kategori', 'Pendapatan')->sum('kredit');
    $bebanPokok = $data->filter(fn($i) =>
        $i->kategori == 'Beban' &&
        str_contains(strtolower($i->transaksi . ' ' . $i->keterangan), 'pokok')
    )->sum('debit');
    $totalBeban = $data->where('kategori', 'Beban')->sum('debit');
    $labaKotor  = $pendapatan - $bebanPokok;
    $labaBersih = $pendapatan - $totalBeban;

    $filename = 'laba_rugi_' . now()->format('Ymd_His') . '.csv';

    $headers = [
        'Content-Type'        => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"$filename\"",
    ];

    $callback = function () use ($totalBeban, $pendapatan, $labaKotor, $labaBersih) {
        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['No','Total Beban (Rp)','Total Pendapatan (Rp)','Laba Kotor (Rp)','Laba Bersih (Rp)']);
        fputcsv($handle, [1, $totalBeban, $pendapatan, $labaKotor, $labaBersih]);
        fclose($handle);
    };

    return response()->stream($callback, 200, $headers);
}

public function exportExcelLabaRugi(Request $request)
{
    $data = Bukubesar::all();

    $pendapatan = $data->where('kategori', 'Pendapatan')->sum('kredit');
    $bebanPokok = $data->filter(fn($i) =>
        $i->kategori == 'Beban' &&
        str_contains(strtolower($i->transaksi . ' ' . $i->keterangan), 'pokok')
    )->sum('debit');
    $totalBeban = $data->where('kategori', 'Beban')->sum('debit');
    $labaKotor  = $pendapatan - $bebanPokok;
    $labaBersih = $pendapatan - $totalBeban;

    $filename = 'laba_rugi_' . now()->format('Ymd_His') . '.xlsx';

    return Excel::download(new \App\Exports\LabaRugiExport($totalBeban, $pendapatan, $labaKotor, $labaBersih), $filename);
}

public function pdfLabaRugi(Request $request)
{
    $data = Bukubesar::all();

    $pendapatan = $data->where('kategori', 'Pendapatan')->sum('kredit');
    $bebanPokok = $data->filter(fn($i) =>
        $i->kategori == 'Beban' &&
        str_contains(strtolower($i->transaksi . ' ' . $i->keterangan), 'pokok')
    )->sum('debit');
    $totalBeban = $data->where('kategori', 'Beban')->sum('debit');
    $labaKotor  = $pendapatan - $bebanPokok;
    $labaBersih = $pendapatan - $totalBeban;
    $setting    = Setting::first();

    $pdf = Pdf::loadView('admin.bukubesar.laba_rugi_pdf', compact(
        'totalBeban', 'pendapatan', 'labaKotor', 'labaBersih', 'setting'
    ));

    return $pdf->stream('laba-rugi.pdf');
}
}