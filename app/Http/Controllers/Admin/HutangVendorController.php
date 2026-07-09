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
            'data'     => $query->latest()->paginate(15)->withQueryString(),
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
            'kategori' => 'required',
            'nominal' => 'required|numeric',
            'dibayar' => 'nullable|numeric',
            'jatuh_tempo' => 'required|date',
            'status' => 'required',
        ]);

        $dibayar = $request->dibayar ?? 0;

        $sisa = $request->nominal - $dibayar;

        $data = HutangVendor::create([
            'nama_vendor' => $request->nama_vendor,
            'kategori' => $request->kategori,
            'nominal' => $request->nominal,
            'dibayar' => $dibayar,
            'sisa' => $sisa,
            'jatuh_tempo' => $request->jatuh_tempo,
            'status' => $request->status,
            'keterangan' => $request->keterangan,
        ]);

        if ($request->status == 'lunas') {

            $kodeJurnal = 'HTG-' . $data->id;
            $lastSaldo  = Keuangan::latest()->value('saldo') ?? 0;
            $saldoBaru  = $lastSaldo - $request->nominal;

            Keuangan::create([
                'tanggal'    => now()->toDateString(),
                'reference'  => $kodeJurnal,
                'user_id'    => auth()->id(),
                'kategori'   => 'Pengeluaran',
                'metode'     => 'Cash',
                'keterangan' => 'Pelunasan Hutang Vendor - ' . $request->nama_vendor,
                'pemasukan'  => 0,
                'pengeluaran' => $request->nominal,
                'saldo'      => $saldoBaru,
            ]);

            // Auto-posting ke Buku Besar
            if (!Bukubesar::where('kode_jurnal', $kodeJurnal)->exists()) {
                Bukubesar::create([
                    'kode_jurnal' => $kodeJurnal,
                    'transaksi'   => 'Pelunasan Hutang Vendor - ' . $request->nama_vendor,
                    'kategori'    => 'Beban',
                    'tanggal'     => now()->toDateString(),
                    'debit'       => $request->nominal,
                    'kredit'      => 0,
                    'saldo'       => $request->nominal,
                    'aktivitas'   => 'Operasi',
                    'keterangan'  => 'Auto-posting: Pelunasan hutang kepada '
                                     . $request->nama_vendor
                                     . ' kategori ' . $request->kategori,
                ]);
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
    |--------------------------------------------------------------------------
    | UPDATE STATUS CEPAT DARI TABLE
    |--------------------------------------------------------------------------
    */

        if ($request->has('status') && count($request->all()) <= 3) {

            $statusLama = $data->status;

            $data->update([
                'status' => $request->status
            ]);

            $kodeJurnal = 'HTG-' . $data->id;

            if ($request->status == 'lunas' && $statusLama != 'lunas') {

                $lastSaldo = Keuangan::latest()->value('saldo') ?? 0;
                $saldoBaru = $lastSaldo - $data->nominal;

                Keuangan::create([
                    'tanggal'    => now()->toDateString(),
                    'reference'  => $kodeJurnal,
                    'user_id'    => auth()->id(),
                    'kategori'   => 'Pengeluaran',
                    'metode'     => 'Cash',
                    'keterangan' => 'Pelunasan Hutang Vendor - ' . $data->nama_vendor,
                    'pemasukan'  => 0,
                    'pengeluaran' => $data->nominal,
                    'saldo'      => $saldoBaru,
                ]);

                // Auto-posting ke Buku Besar (cegah duplikasi)
                if (!Bukubesar::where('kode_jurnal', $kodeJurnal)->exists()) {
                    Bukubesar::create([
                        'kode_jurnal' => $kodeJurnal,
                        'transaksi'   => 'Pelunasan Hutang Vendor - ' . $data->nama_vendor,
                        'kategori'    => 'Beban',
                        'tanggal'     => now()->toDateString(),
                        'debit'       => $data->nominal,
                        'kredit'      => 0,
                        'saldo'       => $data->nominal,
                        'aktivitas'   => 'Operasi',
                        'keterangan'  => 'Auto-posting: Pelunasan hutang kepada '
                                         . $data->nama_vendor
                                         . ' kategori ' . $data->kategori,
                    ]);
                }

            } elseif ($request->status != 'lunas' && $statusLama == 'lunas') {

                // Berubah dari lunas ke status lain — hapus jurnal & keuangan
                Keuangan::where('reference', $kodeJurnal)->delete();
                Bukubesar::where('kode_jurnal', $kodeJurnal)->delete();
            }

            return back()->with('success', 'Status berhasil diubah');
        }

        /*
    |--------------------------------------------------------------------------
    | UPDATE FULL FORM
    |--------------------------------------------------------------------------
    */

        $request->validate([
            'nama_vendor' => 'required',
            'kategori' => 'required',
            'nominal' => 'required|numeric',
            'dibayar' => 'nullable|numeric',
            'jatuh_tempo' => 'required|date',
            'status' => 'required',
        ]);

        $dibayar = $request->dibayar ?? 0;

        $sisa = $request->nominal - $dibayar;

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

            // --- Sinkron KEUANGAN ---
            $keuangan = Keuangan::where('reference', $kodeJurnal)->first();

            if ($keuangan) {
                // Update nominal jika berubah
                $keuangan->update([
                    'pengeluaran' => $request->nominal,
                    'keterangan'  => 'Pelunasan Hutang Vendor - ' . $request->nama_vendor,
                ]);
            } elseif ($statusLama != 'lunas') {
                // Baru lunas — buat entri Keuangan baru
                $lastSaldo = Keuangan::latest()->value('saldo') ?? 0;
                $saldoBaru = $lastSaldo - $request->nominal;

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
                ]);
            }

            // --- Sinkron BUKU BESAR ---
            $jurnal = Bukubesar::where('kode_jurnal', $kodeJurnal)->first();

            if ($jurnal) {
                // Update nominal jika berubah
                $jurnal->update([
                    'transaksi'  => 'Pelunasan Hutang Vendor - ' . $request->nama_vendor,
                    'debit'      => $request->nominal,
                    'saldo'      => $request->nominal,
                    'keterangan' => 'Auto-posting: Pelunasan hutang kepada '
                                    . $request->nama_vendor
                                    . ' kategori ' . $request->kategori,
                ]);
            } elseif ($statusLama != 'lunas') {
                // Baru lunas — buat jurnal baru
                Bukubesar::create([
                    'kode_jurnal' => $kodeJurnal,
                    'transaksi'   => 'Pelunasan Hutang Vendor - ' . $request->nama_vendor,
                    'kategori'    => 'Beban',
                    'tanggal'     => now()->toDateString(),
                    'debit'       => $request->nominal,
                    'kredit'      => 0,
                    'saldo'       => $request->nominal,
                    'aktivitas'   => 'Operasi',
                    'keterangan'  => 'Auto-posting: Pelunasan hutang kepada '
                                     . $request->nama_vendor
                                     . ' kategori ' . $request->kategori,
                ]);
            }

        } else {

            // Status bukan lunas — hapus jurnal & keuangan jika ada
            Keuangan::where('reference', $kodeJurnal)->delete();
            Bukubesar::where('kode_jurnal', $kodeJurnal)->delete();
        }

        return back()->with('success', 'Data berhasil diupdate');
    }

    /**
     * DELETE
     */
    public function destroy($id)
    {
        $hutang = HutangVendor::findOrFail($id);

        // Hapus jurnal Buku Besar & Keuangan terkait
        $kodeJurnal = 'HTG-' . $hutang->id;
        Bukubesar::where('kode_jurnal', $kodeJurnal)->delete();
        Keuangan::where('reference', $kodeJurnal)->delete();

        $hutang->delete();

        return back()->with(
            'success',
            'Data berhasil dihapus'
        );
    }





    public function pdf(Request $request)
    {
        $query = HutangVendor::query();

        // 🔥 pakai filter yang sama seperti di table
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
