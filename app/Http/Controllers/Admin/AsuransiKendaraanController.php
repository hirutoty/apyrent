<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Asuransi;
use Illuminate\Http\Request;
use App\Models\AsuransiKendaraan;
use App\Models\Kendaraan;
use App\Models\Keuangan;
use App\Models\AsuransiHistory;
use App\Models\JenisAsuransi;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AsuransiKendaraanController extends Controller
{
    public function index()
    {
        // update otomatis status expired
        AsuransiKendaraan::where('tgl_berakhir', '<', now())
            ->where('status_kendaraan', 'aktif')
            ->update(['status_kendaraan' => 'expired']);

        $data = AsuransiKendaraan::with([
            'kendaraan',
            'asuransi',
            'jenisAsuransi'
        ])->latest()->get();

        $setting = Setting::first();
        $kendaraan = Kendaraan::all();
        $asuransi = Asuransi::all();
        $jenisAsuransi = JenisAsuransi::all();
        $setting = Setting::first();

        $reminder = match ($setting->satuan_reminder) {
            'hari'    => $setting->batas_reminder,
            'minggu'  => $setting->batas_reminder * 7,
            'bulan'   => $setting->batas_reminder * 30,
            'tahun'   => $setting->batas_reminder * 365,
            default   => $setting->batas_reminder,
        };

        foreach ($data as $d) {

            $tglBerakhir = \Carbon\Carbon::parse($d->tgl_berakhir)->startOfDay();
            $hariIni = now()->startOfDay();

            $d->sisaHari = (int) $hariIni->diffInDays($tglBerakhir, false);

            $d->isExpired = $d->sisaHari <= 0;

            $d->isSoon = !$d->isExpired && $d->sisaHari <= $reminder;
        }

        return view('admin.asuransi.asuransi_kendaraan', compact(
            'data',
            'kendaraan',
            'asuransi',
            'jenisAsuransi',
            'reminder',
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kendaraan_id'       => 'required|exists:kendaraan,id',
            'asuransi_id'        => 'required|exists:asuransi,id',
            'jenis_asuransi_id'  => 'required|exists:jenis_asuransi,id',
            'tgl_mulai'          => 'required|date',
            'tgl_berakhir'       => 'required|date|after_or_equal:tgl_mulai',

            'durasi_bulan'       => 'required|integer|min:1',
            'biaya'              => 'required|numeric|min:0',
             'bukti_bayar'        => 'required|file|max:5120',
        ]);

        $kendaraan = \App\Models\Kendaraan::findOrFail($request->kendaraan_id);

        // 🔥 CEK DUPLIKAT FULL KOMBINASI
        $exists = AsuransiKendaraan::where('kendaraan_id', $request->kendaraan_id)
            ->exists();

        if ($exists) {
            return back()->with(
                'error',
                'Kendaraan / nopol ini sudah terdaftar pada data asuransi'
            );
        }

        $buktiBayar = null;

        if ($request->hasFile('bukti_bayar')) {
            $file = $request->file('bukti_bayar');

            $filename = time() . '_' . $file->getClientOriginalName();

            $file->move(public_path('asuransi/bukti_bayar'), $filename);

            $buktiBayar = 'asuransi/bukti_bayar/' . $filename;
        }

        AsuransiKendaraan::create([
            'kendaraan_id'      => $request->kendaraan_id,
            'asuransi_id'       => $request->asuransi_id,
            'jenis_asuransi_id' => $request->jenis_asuransi_id,
            'tgl_mulai'         => $request->tgl_mulai,
            'tgl_berakhir'      => $request->tgl_berakhir,

            'durasi_bulan'      => $request->durasi_bulan,
            'biaya'             => $request->biaya,
            'bukti_bayar'       => $buktiBayar,

            'status_kendaraan'  => 'aktif',
        ]);

        return back()->with('success', 'Data berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kendaraan_id' => 'required|exists:kendaraan,id',
            'asuransi_id' => 'required|exists:asuransi,id',
            'jenis_asuransi_id' => 'required|exists:jenis_asuransi,id',
            'tgl_mulai' => 'required|date',
            'tgl_berakhir' => 'required|date',
            'status_kendaraan' => 'required',
            'durasi_bulan' => 'required|integer|min:1',
            'biaya'        => 'required|numeric|min:0',
            'bukti_bayar'        => 'required|file|max:5120',
        ]);

        $data = AsuransiKendaraan::findOrFail($id);

        // 🔥 CEK DUPLIKAT SEBELUM UPDATE
        $exists = AsuransiKendaraan::where('kendaraan_id', $request->kendaraan_id)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->with(
                'error',
                'Kendaraan / nopol ini sudah terdaftar pada data asuransi'
            );
        }

        // upload file
        $buktiBayar = $data->bukti_bayar;

        if ($request->hasFile('bukti_bayar')) {

            if ($buktiBayar && file_exists(public_path($buktiBayar))) {
                unlink(public_path($buktiBayar));
            }

            $file = $request->file('bukti_bayar');

            $filename = time() . '_' . $file->getClientOriginalName();

            $file->move(public_path('asuransi/bukti_bayar'), $filename);

            $buktiBayar = 'asuransi/bukti_bayar/' . $filename;
        }

        // baru update
        $data->update([
            'kendaraan_id'      => $request->kendaraan_id,
            'asuransi_id'       => $request->asuransi_id,
            'jenis_asuransi_id' => $request->jenis_asuransi_id,
            'tgl_mulai'         => $request->tgl_mulai,
            'tgl_berakhir'      => $request->tgl_berakhir,
            'status_kendaraan'  => $request->status_kendaraan,
            'durasi_bulan'      => $request->durasi_bulan,
            'biaya'             => $request->biaya,
            'bukti_bayar'       => $buktiBayar,
        ]);

        return back()->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        $data = AsuransiKendaraan::findOrFail($id);

        if ($data->bukti_bayar && file_exists(public_path($data->bukti_bayar))) {
            unlink(public_path($data->bukti_bayar));
        }

        $data->delete();

        return back()->with('success', 'Data berhasil dihapus');
    }

    public function exportPdf(Request $request)
    {
        $search = $request->search;

        $data = AsuransiKendaraan::with([
            'kendaraan',
            'asuransi',
            'jenisAsuransi'
        ])
            ->when($search, function ($q) use ($search) {
                $q->whereHas('kendaraan', function ($k) use ($search) {
                    $k->where('nopol', 'like', "%{$search}%")
                        ->orWhere('merk', 'like', "%{$search}%");
                })
                    ->orWhereHas('asuransi', function ($a) use ($search) {
                        $a->where('nama_asuransi', 'like', "%{$search}%");
                    })
                    ->orWhereHas('jenisAsuransi', function ($j) use ($search) {
                        $j->where('nama_jenis', 'like', "%{$search}%");
                    })
                    ->orWhere('status_kendaraan', 'like', "%{$search}%");
            })
            ->latest()
            ->get();

        $setting = Setting::first();

        $pdf = Pdf::loadView(
            'admin.asuransi.pdf_asuransi_kendaraan',
            [
                'data'   => $data,
                'search' => $search,
                'setting' => $setting,
            ]
        )->setPaper('A4', 'landscape');

        return $pdf->stream('laporan-asuransi-kendaraan.pdf');
    }

    public function perpanjang(Request $request, $id)
    {
        $request->validate([
            'asuransi_id'       => 'required|exists:asuransi,id',
            'jenis_asuransi_id' => 'required|exists:jenis_asuransi,id',
            'tgl_berakhir'      => 'required|date',
            'durasi_bulan'      => 'required|integer|min:1',
            'biaya'             => 'required|numeric|min:0',
            'bukti_bayar'       => 'require|file|max:5120',
        ]);

        $asuransi = AsuransiKendaraan::findOrFail($id);
        // Upload file baru terlebih dahulu
        $bukti = $asuransi->bukti_bayar;

        if ($request->hasFile('bukti_bayar')) {

            $file = $request->file('bukti_bayar');
            $filename = time() . '_' . $file->getClientOriginalName();

            $path = public_path('asuransi/bukti_bayar');

            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $file->move($path, $filename);

            $bukti = 'asuransi/bukti_bayar/' . $filename;
        }

        // Simpan history
        AsuransiHistory::create([
            'asuransi_kendaraan_id' => $asuransi->id,
            'kendaraan_id'          => $asuransi->kendaraan_id,
            'asuransi_id'           => $asuransi->asuransi_id,
            'jenis_asuransi_id'     => $asuransi->jenis_asuransi_id,
            'tgl_mulai'             => $asuransi->tgl_mulai,
            'tgl_berakhir'          => $asuransi->tgl_berakhir,
            'durasi_bulan'          => $asuransi->durasi_bulan,
            'biaya'                 => $asuransi->biaya,
            'bukti_bayar'           => $bukti, // <-- pakai file BARU
            'status_kendaraan'      => 'aktif',
            'diperpanjang_pada'     => now(),
        ]);


        // 🔥 MASUK KE KEUANGAN (PENGELUARAN)
        $lastSaldo = Keuangan::latest()->value('saldo') ?? 0;

        $pengeluaran = $request->biaya;
        // 🔥 TAMBAH KE KEUANGAN (PENGELUARAN)
        Keuangan::create([
            'tanggal'      => now(),
            'reference'    => 'Asuransi-' . $asuransi->id,
            'user_id'      => auth()->id(),
            'kategori'     => 'asuransi_kendaraan',
            'metode'       => '-', // bisa kamu ganti kalau ada field metode
            'keterangan'   => 'Pembayaran asuransi kendaraan: ' . $asuransi->jenis_pajak . ' - ' . $request->keterangan,
            'pemasukan'    => 0,
            'pengeluaran'  => $request->biaya,
            'saldo' => $lastSaldo - $pengeluaran,
        ]);


        // Update data aktif
        $asuransi->update([
            'asuransi_id'       => $request->asuransi_id,
            'jenis_asuransi_id' => $request->jenis_asuransi_id,
            'tgl_mulai'         => now()->toDateString(),
            'tgl_berakhir'      => $request->tgl_berakhir,
            'durasi_bulan'      => $request->durasi_bulan,
            'biaya'             => $request->biaya,
            'bukti_bayar'       => $bukti, // <-- pakai file BARU
            'status_kendaraan'  => 'aktif',
        ]);

        return back()->with('success', 'Asuransi berhasil diperpanjang!');
    }
}
