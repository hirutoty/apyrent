<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stnk;
use App\Models\StnkHistory;
use App\Models\Kendaraan;
use App\Models\Keuangan;
use App\Models\Bukubesar;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

class StnkController extends Controller
{
    public function index()
    {
        $data = Stnk::with('kendaraan')->latest()->paginate(15)->withQueryString();
        $kendaraan = Kendaraan::all();

        return view('admin.stnk.index', compact('data', 'kendaraan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kendaraan_id'   => 'required|exists:kendaraan,id',
            'nama_pemilik'   => 'required',
            'jenis_model'    => 'required',
            'masa_berlaku'   => 'required|date',
            'biaya'          => 'required|numeric',
            'bukti'          => 'nullable|file|max:5120',
        ]);

        $kendaraan = Kendaraan::findOrFail($request->kendaraan_id);

        // CEK DUPLIKAT NOPOL
        $exists = Stnk::whereHas('kendaraan', function ($q) use ($kendaraan) {
            $q->where('nopol', $kendaraan->nopol);
        })->exists();

        if ($exists) {
            return back()->with('error', 'Nopol ini sudah memiliki data STNK');
        }

        // upload file
        $bukti = null;

        if ($request->hasFile('bukti')) {
            $file = $request->file('bukti');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            $path = public_path('stnk/bukti');
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $file->move($path, $filename);
            $bukti = 'stnk/bukti/' . $filename;
        }

        Stnk::create([
            'kendaraan_id' => $request->kendaraan_id,
            'nopol'        => $kendaraan->nopol,
            'merk'         => $kendaraan->merk,
            'nama_pemilik' => $request->nama_pemilik,
            'jenis_model'  => $request->jenis_model,
            'masa_berlaku' => $request->masa_berlaku,
            'biaya'        => $request->biaya,
            'bukti'        => $bukti,
        ]);

        return back()->with('success', 'Data STNK berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kendaraan_id'   => 'required|exists:kendaraan,id',
            'nama_pemilik'   => 'required',
            'jenis_model'    => 'required',
            'masa_berlaku'   => 'required|date',
            'biaya'          => 'required|numeric',
            'bukti'          => 'nullable|file|max:5120',
        ]);

        $stnk = Stnk::findOrFail($id);
        $kendaraan = Kendaraan::findOrFail($request->kendaraan_id);

        $bukti = $stnk->bukti;

        if ($request->hasFile('bukti')) {

            // hapus lama
            if ($bukti && file_exists(public_path($bukti))) {
                unlink(public_path($bukti));
            }

            $file = $request->file('bukti');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            $path = public_path('stnk/bukti');
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $file->move($path, $filename);
            $bukti = 'stnk/bukti/' . $filename;
        }

        $stnk->update([
            'kendaraan_id' => $request->kendaraan_id,
            'nopol'        => $kendaraan->nopol,
            'merk'         => $kendaraan->merk,
            'nama_pemilik' => $request->nama_pemilik,
            'jenis_model'  => $request->jenis_model,
            'masa_berlaku' => $request->masa_berlaku,
            'biaya'        => $request->biaya,
            'bukti'        => $bukti,
        ]);

        return back()->with('success', 'Data STNK berhasil diupdate');
    }

    /**
     * Perpanjang STNK.
     * Data lama dipindahkan ke history (stnk_histories),
     * lalu data aktif diperbarui dengan masa berlaku & biaya baru.
     */
    public function perpanjang(Request $request, $id)
    {
        $request->validate([
            'masa_berlaku' => 'required|date',
            'biaya'        => 'required|numeric',
            'bukti'        => 'nullable|file|max:5120',
        ]);

        $stnk  = Stnk::findOrFail($id);
        $bukti = $stnk->bukti;

        $path = public_path('stnk/bukti');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $buktiLama = $bukti;
        $buktiBaru = $buktiLama;

        if ($request->hasFile('bukti')) {
            $file = $request->file('bukti');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($path, $filename);
            $buktiBaru = 'stnk/bukti/' . $filename;
        }

        \Illuminate\Support\Facades\DB::transaction(function () use (
            $request, $stnk, $buktiLama, $buktiBaru
        ) {
            // Simpan data BARU ke history (sebagai log perpanjangan)
            StnkHistory::create([
                'stnk_id'           => $stnk->id,
                'kendaraan_id'      => $stnk->kendaraan_id,
                'nopol'             => $stnk->nopol,
                'merk'              => $stnk->merk,
                'nama_pemilik'      => $stnk->nama_pemilik,
                'jenis_model'       => $stnk->jenis_model,
                'masa_berlaku'      => $request->masa_berlaku,
                'biaya'             => $request->biaya,
                'bukti'             => $buktiBaru,
                'diperpanjang_pada' => now(),
            ]);

            // 🔥 MASUK KE KEUANGAN (PENGELUARAN)
            $lastSaldo = (float) \Illuminate\Support\Facades\DB::table('keuangans')->lockForUpdate()->orderBy('id', 'desc')->value('saldo') ?? 0;
            $pengeluaran = $request->biaya;
            $kodeJurnal  = 'STNK-' . $stnk->id . '-' . now()->timestamp;

            Keuangan::create([
                'tanggal'     => now(),
                'reference'   => $kodeJurnal,
                'user_id'     => auth()->id(),
                'kategori'    => 'Pengeluaran',
                'metode'      => 'cash',
                'keterangan'  => 'Perpanjangan STNK kendaraan: ' . $stnk->nopol . ' - ' . $stnk->merk,
                'pemasukan'   => 0,
                'pengeluaran' => $pengeluaran,
                'saldo'       => $lastSaldo - $pengeluaran,
            ]);

            // Auto-posting ke Buku Besar
            $saldoBBTerakhir = (float) \Illuminate\Support\Facades\DB::table('bukubesars')->lockForUpdate()->orderBy('id', 'desc')->value('saldo') ?? 0;
            Bukubesar::create([
                'kode_jurnal' => $kodeJurnal,
                'transaksi'   => 'Beban STNK - ' . $stnk->nopol,
                'kategori'    => 'Beban',
                'tanggal'     => now()->toDateString(),
                'debit'       => $pengeluaran,
                'kredit'      => 0,
                'saldo'       => $saldoBBTerakhir - $pengeluaran,
                'aktivitas'   => 'Operasi',
                'keterangan'  => 'Auto-posting: Perpanjangan STNK ' . $stnk->merk . ' (' . $stnk->nopol . ')',
            ]);

            // Update data aktif
            $stnk->update([
                'masa_berlaku' => $request->masa_berlaku,
                'biaya'        => $request->biaya,
                'bukti'        => $buktiBaru,
            ]);
        });

        return back()->with('success', 'STNK berhasil diperpanjang');
    }

    public function destroy($id)
    {
        $stnk = Stnk::findOrFail($id);

        if ($stnk->bukti && file_exists(public_path($stnk->bukti))) {
            unlink(public_path($stnk->bukti));
        }

        $stnk->delete();

        return back()->with('success', 'Data STNK berhasil dihapus');
    }

    public function exportPdf(Request $request)
    {
        $search = $request->search;

        $query = Stnk::with('kendaraan');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nopol', 'like', "%{$search}%")
                    ->orWhere('merk', 'like', "%{$search}%")
                    ->orWhere('nama_pemilik', 'like', "%{$search}%")
                    ->orWhere('jenis_model', 'like', "%{$search}%");
            });
        }

        $data = $query->latest()->get();

        $setting = Setting::first();
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.stnk.pdf', compact('data', 'search', 'setting', 'logoSrc'));

        return $pdf->stream('data-stnk.pdf');
    }
}
