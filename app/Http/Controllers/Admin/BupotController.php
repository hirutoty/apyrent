<?php

namespace App\Http\Controllers\Admin;


use App\Exports\BupotExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bupot;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

class BupotController extends Controller
{
    public function index()
    {
        $data = Bupot::latest()->get();

        return view('admin.bupot.index', compact('data'));
    }

    public function store(Request $request)
{
    $request->validate([
        'tanggal_bukti' => 'required',
        'tipe' => 'required',
        'npwp_pemotong' => 'required',
        'nama_pemotong' => 'required',
        'npwp_dipotong' => 'required',
        'nama_dipotong' => 'required',
        'jumlah_bruto' => 'required|numeric',
        'tarif_pajak' => 'required|numeric',
        'status' => 'required',
    ]);

    $jumlahPotong =
        ($request->jumlah_bruto * $request->tarif_pajak) / 100;

    $file = null;

if ($request->hasFile('file_bupot')) {

    $fileUpload = $request->file('file_bupot');

    $filename = time() . '_' . $fileUpload->getClientOriginalName();
    $destination = public_path('bupot');

    if (!file_exists($destination)) {
        mkdir($destination, 0777, true);
    }

    $fileUpload->move($destination, $filename);

    $file = 'bupot/' . $filename;
}

    // simpan dulu untuk mendapatkan ID
    $bupot = Bupot::create([
        'tanggal_bukti' => $request->tanggal_bukti,
        'tipe' => $request->tipe,
        'npwp_pemotong' => $request->npwp_pemotong,
        'nama_pemotong' => $request->nama_pemotong,
        'npwp_dipotong' => $request->npwp_dipotong,
        'nama_dipotong' => $request->nama_dipotong,
        'jumlah_bruto' => $request->jumlah_bruto,
        'tarif_pajak' => $request->tarif_pajak,
        'jumlah_potong' => $jumlahPotong,
        'status' => $request->status,
        'file_bupot' => $file,
    ]);

    // generate nomor bukti
    $tahun = date('Y');

    $nomorBukti = 'BUPOST-' . $tahun . '-' . $bupot->id;

    $bupot->update([
        'nomor_bukti' => $nomorBukti
    ]);

    return back()->with('success', 'Data berhasil ditambahkan');
}

    public function update(Request $request, $id)
    {
        $data = Bupot::findOrFail($id);

        $jumlahPotong =
            ($request->jumlah_bruto * $request->tarif_pajak) / 100;

        $file = $data->file_bupot;

if ($request->hasFile('file_bupot')) {

    // hapus file lama
    if ($data->file_bupot && file_exists(public_path($data->file_bupot))) {
        unlink(public_path($data->file_bupot));
    }

    $fileUpload = $request->file('file_bupot');

    $filename = time() . '_' . $fileUpload->getClientOriginalName();
    $destination = public_path('bupot');

    if (!file_exists($destination)) {
        mkdir($destination, 0777, true);
    }

    $fileUpload->move($destination, $filename);

    $file = 'bupot/' . $filename;
}

        $data->update([
            'nomor_bukti' => $request->nomor_bukti,
            'tanggal_bukti' => $request->tanggal_bukti,
            'tipe' => $request->tipe,
            'npwp_pemotong' => $request->npwp_pemotong,
            'nama_pemotong' => $request->nama_pemotong,
            'npwp_dipotong' => $request->npwp_dipotong,
            'nama_dipotong' => $request->nama_dipotong,
            'jumlah_bruto' => $request->jumlah_bruto,
            'tarif_pajak' => $request->tarif_pajak,
            'jumlah_potong' => $jumlahPotong,
            'status' => $request->status,
            'file_bupot' => $file,
        ]);

        return back()->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
{
    $data = Bupot::findOrFail($id);

    if ($data->file_bupot && file_exists(public_path($data->file_bupot))) {
        unlink(public_path($data->file_bupot));
    }

    $data->delete();

    return back()->with('success', 'Data berhasil dihapus');
}

  
public function pdf(Request $request)
{
    $query = Bupot::query();

    // FILTER SEARCH (sama seperti tabel)
    if ($request->search) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('nomor_bukti', 'like', "%$search%")
              ->orWhere('nama_pemotong', 'like', "%$search%")
              ->orWhere('nama_dipotong', 'like', "%$search%")
              ->orWhere('tipe', 'like', "%$search%")
              ->orWhere('status', 'like', "%$search%");
        });
    }

    $setting = Setting::first();
    $data = $query->get();

    $pdf = PDF::loadView('admin.bupot.pdf', compact('data', 'setting'));

    return $pdf->stream('bupot.pdf');
}

public function exportExcel(Request $request)
{
    $filename = 'bupot_' . now()->format('Ymd_His') . '.xlsx';

    return Excel::download(
        new BupotExport($request->search),
        $filename
    );
}
}