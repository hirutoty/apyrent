<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stnk;
use App\Models\StnkHistory;
use App\Models\Kendaraan;
use App\Models\Keuangan;

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

        if ($request->hasFile('bukti')) {

            $file = $request->file('bukti');

            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            $file->move($path, $filename);

            if ($bukti && file_exists(public_path($bukti))) {
                unlink(public_path($bukti));
            }

            $bukti = 'stnk/bukti/' . $filename;
        }

        $finalBukti = $bukti;

        // Simpan data lama ke history
        StnkHistory::create([
            'stnk_id'           => $stnk->id,
            'kendaraan_id'      => $stnk->kendaraan_id,
            'nopol'             => $stnk->nopol,
            'merk'              => $stnk->merk,
            'nama_pemilik'      => $stnk->nama_pemilik,
            'jenis_model'       => $stnk->jenis_model,
            'masa_berlaku'      => $stnk->masa_berlaku,
            'biaya'             => $stnk->biaya,
            'bukti'             => $finalBukti,
            'diperpanjang_pada' => now(),
        ]);

        // 🔥 MASUK KE KEUANGAN (PENGELUARAN)
        $lastSaldo = Keuangan::latest()->value('saldo') ?? 0;

        $pengeluaran = $request->biaya;

        Keuangan::create([
            'tanggal'      => now(),
            'reference'    => 'STNK-' . $stnk->id,
            'user_id'      => auth()->id(),
            'kategori'     => 'Pengeluaran',
            'metode'       => 'cash',
            'keterangan'   => 'Perpanjangan STNK kendaraan: ' . $stnk->nopol . ' - ' . $stnk->merk,
            'pemasukan'    => 0,
            'pengeluaran'  => $pengeluaran,
            'saldo'        => $lastSaldo - $pengeluaran,
        ]);

        // Update data aktif
        $stnk->update([
            'masa_berlaku' => $request->masa_berlaku,
            'biaya'        => $request->biaya,
            'bukti'        => $finalBukti,
        ]);

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
}
