<?php

namespace App\Http\Controllers\Admin;

use App\Exports\HutangVendorExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HutangVendor;
use App\Models\Keuangan;
use App\Models\Bukubesar;
use App\Models\Setting;
use Illuminate\Support\Facades\DB; // P0 #3 / P1 #2: needed for lockForUpdate

class HutangVendorController extends Controller
{
    /**
     * INDEX
     */
    public function index(Request $request)
    {
        $query = HutangVendor::query();

        if ($request->search) {
            $query->where('nama_vendor', 'like', '%' . $request->search . '%')
                ->orWhere('kategori', 'like', '%' . $request->search . '%')
                ->orWhere('keterangan', 'like', '%' . $request->search . '%');
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $setting = Setting::first();
        // Base64 logo untuk DomPDF
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $reminder = match ($setting->satuan_reminder) {
            'hari'    => $setting->batas_reminder,
            'minggu'  => $setting->batas_reminder * 7,
            'bulan'   => $setting->batas_reminder * 30,
            'tahun'   => $setting->batas_reminder * 365,
            default   => $setting->batas_reminder,
        };

        return view('admin.hutang_vendor.index', [
            'data'     => $query->latest('id')->paginate(15)->withQueryString(), // MEDIUM #8: latest('id')
            'reminder' => $reminder,
        ]);
    }

    /**
     * STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_vendor' => 'required',
            'kategori'    => 'required',
            'nominal'     => 'required|numeric',
            'dibayar'     => 'nullable|numeric',
            'jatuh_tempo' => 'required|date',
            'status'      => 'required',
        ]);

        $dibayar = $request->dibayar ?? 0;
        $sisa    = $request->nominal - $dibayar;

        $data = HutangVendor::create([
            'nama_vendor' => $request->nama_vendor,
            'kategori'    => $request->kategori,
            'nominal'     => $request->nominal,
            'dibayar'     => $dibayar,
            'sisa'        => $sisa,
            'jatuh_tempo' => $request->jatuh_tempo,
            'status'      => $request->status,
            'keterangan'  => $request->keterangan,
        ]);

        if ($request->status == 'lunas') {

            DB::beginTransaction();
            try {
                $kodeJurnal = 'HTG-' . $data->id;

                // P0 #3 FIX RACE CONDITION: lockForUpdate inside transaction
                $lastSaldo = (float) DB::table('keuangans')
                    ->lockForUpdate()
                    ->orderBy('id', 'desc')
                    ->value('saldo') ?? 0;

                $saldoBaru = $lastSaldo - $request->nominal;

                // P0 #1: create Keuangan; saldo already safe via lockForUpdate (no recalculate needed after create)
                Keuangan::create([
                    'tanggal'     => now()->toDateString(),
                    'reference'   => $kodeJurnal,
                    'user_id'     => auth()->id(),
                    'kategori'    => 'Pengeluaran',
                    'metode'      => 'Cash',
                    'keterangan'  => 'Pelunasan Hutang Vendor - ' . $request->nama_vendor,
                    'pemasukan'   => 0,
                    'pengeluaran' => $request->nominal,
                    'saldo'       => $saldoBaru,
                    'sumber'      => 'auto',
                ]);

                // P1 #2 FIX BUKU BESAR SALDO: accumulative saldo with lockForUpdate
                if (!Bukubesar::where('kode_jurnal', $kodeJurnal)->exists()) {
                    $saldoBBTerakhir = (float) DB::table('bukubesars')
                        ->lockForUpdate()
                        ->orderBy('id', 'desc')
                        ->value('saldo') ?? 0;

                    Bukubesar::create([
                        'kode_jurnal' => $kodeJurnal,
                        'transaksi'   => 'Pelunasan Hutang Vendor - ' . $request->nama_vendor,
                        'kategori'    => 'Beban',
                        'tanggal'     => now()->toDateString(),
                        'debit'       => $request->nominal,
                        'kredit'      => 0,
                        'saldo'       => $saldoBBTerakhir + $request->nominal, // P1 #2: accumulative
                        'aktivitas'   => 'Operasi',
                        'keterangan'  => 'Auto-posting: Pelunasan hutang kepada '
                                         . $request->nama_vendor
                                         . ' kategori ' . $request->kategori,
                    ]);
                }

                // TODO P1#6: aging_aps belum punya relasi FK ke hutang_vendor. Butuh migration untuk link keduanya.

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                throw $e;
            }
        }

        return back()->with('success', 'Data hutang vendor berhasil ditambahkan');
    }

    /**
     * UPDATE
     */
    public function update(Request $request, $id)
    {
        $data = HutangVendor::findOrFail($id);

        /*
        |----------------------------------------------------------------------
        | UPDATE STATUS CEPAT DARI TABLE
        |----------------------------------------------------------------------
        */
        if ($request->has('status') && count($request->all()) <= 3) {

            $statusLama = $data->status;

            $data->update([
                'status' => $request->status,
            ]);

            $kodeJurnal = 'HTG-' . $data->id;

            if ($request->status == 'lunas' && $statusLama != 'lunas') {

                DB::beginTransaction();
                try {
                    // P0 #3 FIX RACE CONDITION: lockForUpdate inside transaction
                    $lastSaldo = (float) DB::table('keuangans')
                        ->lockForUpdate()
                        ->orderBy('id', 'desc')
                        ->value('saldo') ?? 0;

                    $saldoBaru = $lastSaldo - $data->nominal;

                    // P0 #1: create Keuangan; no recalculate needed after create (saldo safe via lockForUpdate)
                    Keuangan::create([
                        'tanggal'     => now()->toDateString(),
                        'reference'   => $kodeJurnal,
                        'user_id'     => auth()->id(),
                        'kategori'    => 'Pengeluaran',
                        'metode'      => 'Cash',
                        'keterangan'  => 'Pelunasan Hutang Vendor - ' . $data->nama_vendor,
                        'pemasukan'   => 0,
                        'pengeluaran' => $data->nominal,
                        'saldo'       => $saldoBaru,
                        'sumber'      => 'auto',
                    ]);

                    // Auto-posting ke Buku Besar (cegah duplikasi)
                    if (!Bukubesar::where('kode_jurnal', $kodeJurnal)->exists()) {
                        // P1 #2 FIX BUKU BESAR SALDO: accumulative saldo with lockForUpdate
                        $saldoBBTerakhir = (float) DB::table('bukubesars')
                            ->lockForUpdate()
                            ->orderBy('id', 'desc')
                            ->value('saldo') ?? 0;

                        Bukubesar::create([
                            'kode_jurnal' => $kodeJurnal,
                            'transaksi'   => 'Pelunasan Hutang Vendor - ' . $data->nama_vendor,
                            'kategori'    => 'Beban',
                            'tanggal'     => now()->toDateString(),
                            'debit'       => $data->nominal,
                            'kredit'      => 0,
                            'saldo'       => $saldoBBTerakhir + $data->nominal, // P1 #2: accumulative
                            'aktivitas'   => 'Operasi',
                            'keterangan'  => 'Auto-posting: Pelunasan hutang kepada '
                                             . $data->nama_vendor
                                             . ' kategori ' . $data->kategori,
                        ]);
                    }

                    // TODO P1#6: aging_aps belum punya relasi FK ke hutang_vendor. Butuh migration untuk link keduanya.

                    DB::commit();
                } catch (\Throwable $e) {
                    DB::rollBack();
                    throw $e;
                }

            } elseif ($request->status != 'lunas' && $statusLama == 'lunas') {

                // Berubah dari lunas ke status lain — hapus jurnal & keuangan
                DB::beginTransaction();
                try {
                    // P0 #1 FIX: simpan id sebelum delete, lalu recalculate
                    $keuangan = Keuangan::where('reference', $kodeJurnal)->first();
                    if ($keuangan) {
                        $keuanganId = $keuangan->id;
                        $keuangan->delete();
                        \App\Http\Controllers\Admin\PaymentsController::recalculateKeuanganSaldo($keuanganId);
                    }

                    $jurnal = Bukubesar::where('kode_jurnal', $kodeJurnal)->first();
                    if ($jurnal) {
                        $jurnalId = $jurnal->id;
                        $jurnal->delete();
                        \App\Http\Controllers\Admin\PaymentsController::recalculateBukubesarSaldo($jurnalId);
                    }

                    DB::commit();
                } catch (\Throwable $e) {
                    DB::rollBack();
                    throw $e;
                }
            }

            return back()->with('success', 'Status berhasil diubah');
        }

        /*
        |----------------------------------------------------------------------
        | UPDATE FULL FORM
        |----------------------------------------------------------------------
        */
        $request->validate([
            'nama_vendor' => 'required',
            'kategori'    => 'required',
            'nominal'     => 'required|numeric',
            'dibayar'     => 'nullable|numeric',
            'jatuh_tempo' => 'required|date',
            'status'      => 'required',
        ]);

        $dibayar    = $request->dibayar ?? 0;
        $sisa       = $request->nominal - $dibayar;
        $statusLama = $data->status;

        $data->update([
            'nama_vendor' => $request->nama_vendor,
            'kategori'    => $request->kategori,
            'nominal'     => $request->nominal,
            'dibayar'     => $dibayar,
            'sisa'        => $sisa,
            'jatuh_tempo' => $request->jatuh_tempo,
            'status'      => $request->status,
            'keterangan'  => $request->keterangan,
        ]);

        $kodeJurnal = 'HTG-' . $data->id;

        if ($request->status == 'lunas') {

            DB::beginTransaction();
            try {
                // --- Sinkron KEUANGAN ---
                $keuangan = Keuangan::where('reference', $kodeJurnal)->first();

                if ($keuangan) {
                    // P0 #1 FIX: update nominal, lalu recalculate saldo dari baris ini ke bawah
                    $keuangan->update([
                        'pengeluaran' => $request->nominal,
                        'keterangan'  => 'Pelunasan Hutang Vendor - ' . $request->nama_vendor,
                    ]);
                    \App\Http\Controllers\Admin\PaymentsController::recalculateKeuanganSaldo($keuangan->id);

                } elseif ($statusLama != 'lunas') {
                    // Baru lunas — buat entri Keuangan baru
                    // P0 #3 FIX RACE CONDITION: lockForUpdate inside transaction
                    $lastSaldo = (float) DB::table('keuangans')
                        ->lockForUpdate()
                        ->orderBy('id', 'desc')
                        ->value('saldo') ?? 0;

                    $saldoBaru = $lastSaldo - $request->nominal;

                    // P0 #1: create; saldo safe via lockForUpdate (no recalculate needed)
                    Keuangan::create([
                        'tanggal'     => now()->toDateString(),
                        'reference'   => $kodeJurnal,
                        'user_id'     => auth()->id(),
                        'kategori'    => 'Pengeluaran',
                        'metode'      => 'Cash',
                        'keterangan'  => 'Pelunasan Hutang Vendor - ' . $request->nama_vendor,
                        'pemasukan'   => 0,
                        'pengeluaran' => $request->nominal,
                        'saldo'       => $saldoBaru,
                        'sumber'      => 'auto',
                    ]);
                }

                // --- Sinkron BUKU BESAR ---
                $jurnal = Bukubesar::where('kode_jurnal', $kodeJurnal)->first();

                if ($jurnal) {
                    // P1 #2 FIX: update jurnal, lalu recalculate saldo buku besar
                    $jurnal->update([
                        'transaksi'  => 'Pelunasan Hutang Vendor - ' . $request->nama_vendor,
                        'debit'      => $request->nominal,
                        'keterangan' => 'Auto-posting: Pelunasan hutang kepada '
                                        . $request->nama_vendor
                                        . ' kategori ' . $request->kategori,
                    ]);
                    \App\Http\Controllers\Admin\PaymentsController::recalculateBukubesarSaldo($jurnal->id);

                } elseif ($statusLama != 'lunas') {
                    // Baru lunas — buat jurnal baru
                    // P1 #2 FIX BUKU BESAR SALDO: accumulative saldo with lockForUpdate
                    $saldoBBTerakhir = (float) DB::table('bukubesars')
                        ->lockForUpdate()
                        ->orderBy('id', 'desc')
                        ->value('saldo') ?? 0;

                    Bukubesar::create([
                        'kode_jurnal' => $kodeJurnal,
                        'transaksi'   => 'Pelunasan Hutang Vendor - ' . $request->nama_vendor,
                        'kategori'    => 'Beban',
                        'tanggal'     => now()->toDateString(),
                        'debit'       => $request->nominal,
                        'kredit'      => 0,
                        'saldo'       => $saldoBBTerakhir + $request->nominal, // P1 #2: accumulative
                        'aktivitas'   => 'Operasi',
                        'keterangan'  => 'Auto-posting: Pelunasan hutang kepada '
                                         . $request->nama_vendor
                                         . ' kategori ' . $request->kategori,
                    ]);
                }

                // TODO P1#6: aging_aps belum punya relasi FK ke hutang_vendor. Butuh migration untuk link keduanya.

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                throw $e;
            }

        } else {

            // Status bukan lunas — hapus jurnal & keuangan jika ada
            DB::beginTransaction();
            try {
                // P0 #1 FIX: simpan id sebelum delete, lalu recalculate
                $keuangan = Keuangan::where('reference', $kodeJurnal)->first();
                if ($keuangan) {
                    $keuanganId = $keuangan->id;
                    $keuangan->delete();
                    \App\Http\Controllers\Admin\PaymentsController::recalculateKeuanganSaldo($keuanganId);
                }

                $jurnal = Bukubesar::where('kode_jurnal', $kodeJurnal)->first();
                if ($jurnal) {
                    $jurnalId = $jurnal->id;
                    $jurnal->delete();
                    \App\Http\Controllers\Admin\PaymentsController::recalculateBukubesarSaldo($jurnalId);
                }

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                throw $e;
            }
        }

        return back()->with('success', 'Data berhasil diupdate');
    }

    /**
     * DELETE
     */
    public function destroy($id)
    {
        $hutang = HutangVendor::findOrFail($id);

        $kodeJurnal = 'HTG-' . $hutang->id;

        DB::beginTransaction();
        try {
            // P0 #1 FIX: simpan id sebelum delete, lalu recalculate setelah delete
            $keuangan = Keuangan::where('reference', $kodeJurnal)->first();
            if ($keuangan) {
                $keuanganId = $keuangan->id;
                $keuangan->delete();
                \App\Http\Controllers\Admin\PaymentsController::recalculateKeuanganSaldo($keuanganId);
            }

            $jurnal = Bukubesar::where('kode_jurnal', $kodeJurnal)->first();
            if ($jurnal) {
                $jurnalId = $jurnal->id;
                $jurnal->delete();
                \App\Http\Controllers\Admin\PaymentsController::recalculateBukubesarSaldo($jurnalId);
            }

            $hutang->delete();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return back()->with('success', 'Data berhasil dihapus');
    }

    /**
     * PDF EXPORT
     */
    public function pdf(Request $request)
    {
        $query = HutangVendor::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_vendor', 'like', "%{$request->search}%")
                    ->orWhere('kategori', 'like', "%{$request->search}%")
                    ->orWhere('keterangan', 'like', "%{$request->search}%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $setting = Setting::first();
        // Base64 logo untuk DomPDF
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }
        $data = $query->get();

        $pdf = PDF::loadView('admin.hutang_vendor.pdf', compact('data', 'setting', 'logoSrc'))
            ->setPaper('A4', 'landscape');

        return $pdf->stream('hutang-vendor.pdf');
    }

    /**
     * EXCEL EXPORT
     */
    public function exportExcel(Request $request)
    {
        $filename = 'hutang_vendor_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new HutangVendorExport(
                $request->search,
                $request->status
            ),
            $filename
        );
    }
}
