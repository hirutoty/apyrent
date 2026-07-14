<?php

namespace App\Http\Controllers\Admin;

use App\Exports\RekonsiliasiExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RekonsiliasiBank;
use App\Models\Keuangan;
use App\Models\Bukubesar;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class RekonsiliasiController extends Controller
{
    // =========================================================================
    // INDEX
    // =========================================================================

    public function index()
    {
        $data = RekonsiliasiBank::latest('id')->paginate(15)->withQueryString();

        return view('admin.rekonsiliasi.index', compact('data'));
    }

    // =========================================================================
    // STORE
    // =========================================================================

    public function store(Request $request)
    {
        $request->validate([
            'tanggal'          => 'required',
            'deskripsi'        => 'required',
            'reference_no'     => 'required',
            'amount'           => 'required|numeric',
            'bukti_pembayaran' => 'required|file|max:5120',
        ]);

        $bukti_pembayaran = null;

        if ($request->hasFile('bukti_pembayaran')) {
            $file     = $request->file('bukti_pembayaran');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path     = public_path('rekonsiliasi/bukti_pembayaran');
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            $file->move($path, $filename);
            $bukti_pembayaran = 'rekonsiliasi/bukti_pembayaran/' . $filename;
        }

        DB::transaction(function () use ($request, $bukti_pembayaran) {
            $rekonsiliasi = RekonsiliasiBank::create([
                'tanggal'             => $request->tanggal,
                'deskripsi'           => $request->deskripsi,
                'reference_no'        => $request->reference_no,
                'amount'              => $request->amount,
                'currency'            => $request->currency,
                'status_rekonsiliasi' => $request->status_rekonsiliasi,
                'invoice_id'          => $request->invoice_id,
                'va'                  => $request->va,
                'bukti_pembayaran'    => $bukti_pembayaran,
            ]);

            // Auto-post ke Keuangan & Bukubesar jika status = matched
            if ($request->status_rekonsiliasi === 'matched') {
                $this->postKeuangan($rekonsiliasi);
                $this->postBukubesar($rekonsiliasi);
            }
        });

        return back()->with('success', 'Data berhasil ditambahkan');
    }

    // =========================================================================
    // UPDATE
    // =========================================================================

    public function update(Request $request, $id)
    {
        $data = RekonsiliasiBank::findOrFail($id);

        $oldStatus = $data->status_rekonsiliasi;
        $newStatus = $request->status_rekonsiliasi;

        $updateData = [
            'tanggal'             => $request->tanggal,
            'deskripsi'           => $request->deskripsi,
            'reference_no'        => $request->reference_no,
            'amount'              => $request->amount,
            'currency'            => $request->currency,
            'status_rekonsiliasi' => $newStatus,
            'invoice_id'          => $request->invoice_id,
            'va'                  => $request->va,
        ];

        // Hanya update bukti_pembayaran jika ada file baru
        if ($request->hasFile('bukti_pembayaran')) {
            $file     = $request->file('bukti_pembayaran');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path     = public_path('rekonsiliasi/bukti_pembayaran');
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            $file->move($path, $filename);
            $updateData['bukti_pembayaran'] = 'rekonsiliasi/bukti_pembayaran/' . $filename;
        }

        DB::transaction(function () use ($data, $updateData, $oldStatus, $newStatus) {
            $data->update($updateData);

            // Status berubah ke matched → buat/update entri Keuangan & Bukubesar
            if ($newStatus === 'matched' && $oldStatus !== 'matched') {
                $this->postKeuangan($data);
                $this->postBukubesar($data);
            }

            // Status berubah dari matched ke status lain → hapus entri & recalculate
            if ($oldStatus === 'matched' && $newStatus !== 'matched') {
                $this->removeKeuangan($data);
                $this->removeBukubesar($data);
            }

            // Amount berubah tapi tetap matched → update entri & recalculate
            if ($newStatus === 'matched' && $oldStatus === 'matched') {
                $this->updateKeuangan($data);
                $this->updateBukubesar($data);
            }
        });

        return back()->with('success', 'Data berhasil diupdate');
    }

    // =========================================================================
    // DESTROY
    // =========================================================================

    public function destroy($id)
    {
        $rekonsiliasi = RekonsiliasiBank::findOrFail($id);

        DB::transaction(function () use ($rekonsiliasi) {
            // Hapus entri Keuangan & Bukubesar terkait, lalu recalculate saldo
            if ($rekonsiliasi->status_rekonsiliasi === 'matched') {
                $this->removeKeuangan($rekonsiliasi);
                $this->removeBukubesar($rekonsiliasi);
            }

            $rekonsiliasi->delete();
        });

        return back()->with('success', 'Data berhasil dihapus');
    }

    // =========================================================================
    // PRIVATE HELPERS — Keuangan
    // =========================================================================

    /**
     * Auto-post satu baris Keuangan untuk rekonsiliasi matched.
     * Cegah duplikasi via kolom reference = 'REKON-{id}'.
     * HARUS dipanggil dalam DB::transaction.
     */
    private function postKeuangan(RekonsiliasiBank $rekonsiliasi): void
    {
        $reference = 'REKON-' . $rekonsiliasi->id;

        // Cegah duplikasi
        if (Keuangan::where('reference', $reference)->exists()) {
            return;
        }

        // lockForUpdate untuk menghindari race condition
        $saldoTerakhir = (float) DB::table('keuangans')
            ->lockForUpdate()
            ->orderBy('id', 'desc')
            ->value('saldo') ?? 0;

        $saldoBaru = $saldoTerakhir + (float) $rekonsiliasi->amount;

        Keuangan::create([
            'tanggal'     => $rekonsiliasi->tanggal,
            'reference'   => $reference,
            'user_id'     => auth()->id(),
            'kategori'    => 'Rekonsiliasi Bank',
            'metode'      => 'Transfer',
            'keterangan'  => 'Auto-posting Rekonsiliasi Bank: ' . $rekonsiliasi->deskripsi,
            'pemasukan'   => $rekonsiliasi->amount,
            'pengeluaran' => 0,
            'saldo'       => $saldoBaru,
            'sumber'      => 'auto',
        ]);
    }

    /**
     * Auto-post satu baris Bukubesar untuk rekonsiliasi matched.
     * Cegah duplikasi via kolom referensi = 'REKON-{id}'.
     * HARUS dipanggil dalam DB::transaction.
     */
    private function postBukubesar(RekonsiliasiBank $rekonsiliasi): void
    {
        $reference = 'REKON-' . $rekonsiliasi->id;

        // Cegah duplikasi
        if (Bukubesar::where('referensi', $reference)->exists()) {
            return;
        }

        // lockForUpdate untuk menghindari race condition
        $saldoTerakhir = (float) DB::table('bukubesars')
            ->lockForUpdate()
            ->orderBy('id', 'desc')
            ->value('saldo') ?? 0;

        $saldoBaru = $saldoTerakhir + (float) $rekonsiliasi->amount;

        Bukubesar::create([
            'kode_jurnal' => $reference,
            'transaksi'   => 'Rekonsiliasi Bank: ' . $rekonsiliasi->deskripsi,
            'kategori'    => 'Pendapatan',
            'tanggal'     => $rekonsiliasi->tanggal,
            'debit'       => 0,
            'kredit'      => $rekonsiliasi->amount,
            'saldo'       => $saldoBaru,
            'aktivitas'   => 'Operasi',
            'keterangan'  => 'Auto-posting Rekonsiliasi Bank #' . $rekonsiliasi->reference_no,
            'referensi'   => $reference,
        ]);
    }

    /**
     * Update entri Keuangan yang sudah ada (saat amount/tanggal berubah, status tetap matched).
     * Setelah update, recalculate saldo dari baris tersebut ke bawah.
     * HARUS dipanggil dalam DB::transaction.
     */
    private function updateKeuangan(RekonsiliasiBank $rekonsiliasi): void
    {
        $reference = 'REKON-' . $rekonsiliasi->id;
        $entri = Keuangan::where('reference', $reference)->first();

        if (!$entri) {
            // Belum ada, buat baru
            $this->postKeuangan($rekonsiliasi);
            return;
        }

        $entri->update([
            'tanggal'    => $rekonsiliasi->tanggal,
            'pemasukan'  => $rekonsiliasi->amount,
            'keterangan' => 'Auto-posting Rekonsiliasi Bank: ' . $rekonsiliasi->deskripsi,
        ]);

        // Recalculate saldo dari baris ini ke bawah
        PaymentsController::recalculateKeuanganSaldo($entri->id);
    }

    /**
     * Update entri Bukubesar yang sudah ada (saat amount/tanggal berubah, status tetap matched).
     * Setelah update, recalculate saldo dari baris tersebut ke bawah.
     * HARUS dipanggil dalam DB::transaction.
     */
    private function updateBukubesar(RekonsiliasiBank $rekonsiliasi): void
    {
        $reference = 'REKON-' . $rekonsiliasi->id;
        $entri = Bukubesar::where('referensi', $reference)->first();

        if (!$entri) {
            // Belum ada, buat baru
            $this->postBukubesar($rekonsiliasi);
            return;
        }

        $entri->update([
            'tanggal'    => $rekonsiliasi->tanggal,
            'kredit'     => $rekonsiliasi->amount,
            'transaksi'  => 'Rekonsiliasi Bank: ' . $rekonsiliasi->deskripsi,
            'keterangan' => 'Auto-posting Rekonsiliasi Bank #' . $rekonsiliasi->reference_no,
        ]);

        // Recalculate saldo dari baris ini ke bawah
        PaymentsController::recalculateBukubesarSaldo($entri->id);
    }

    /**
     * Hapus entri Keuangan terkait rekonsiliasi dan recalculate saldo.
     * HARUS dipanggil dalam DB::transaction.
     */
    private function removeKeuangan(RekonsiliasiBank $rekonsiliasi): void
    {
        $reference = 'REKON-' . $rekonsiliasi->id;
        $entri = Keuangan::where('reference', $reference)->first();

        if (!$entri) {
            return;
        }

        $fromId = $entri->id;
        $entri->delete();

        // Recalculate saldo dari row setelah yang dihapus
        PaymentsController::recalculateKeuanganSaldo($fromId);
    }

    /**
     * Hapus entri Bukubesar terkait rekonsiliasi dan recalculate saldo.
     * HARUS dipanggil dalam DB::transaction.
     */
    private function removeBukubesar(RekonsiliasiBank $rekonsiliasi): void
    {
        $reference = 'REKON-' . $rekonsiliasi->id;
        $entri = Bukubesar::where('referensi', $reference)->first();

        if (!$entri) {
            return;
        }

        $fromId = $entri->id;
        $entri->delete();

        // Recalculate saldo dari row setelah yang dihapus
        PaymentsController::recalculateBukubesarSaldo($fromId);
    }

    // =========================================================================
    // PDF
    // =========================================================================

    public function pdf(Request $request)
    {
        $query = RekonsiliasiBank::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('deskripsi', 'like', "%{$request->search}%")
                  ->orWhere('reference_no', 'like', "%{$request->search}%")
                  ->orWhere('currency', 'like', "%{$request->search}%")
                  ->orWhere('status_rekonsiliasi', 'like', "%{$request->search}%");
            });
        }

        $setting = Setting::first();

        // Base64 logo untuk DomPDF
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $data = $query->orderBy('tanggal', 'desc')->get();

        $pdf = Pdf::loadView('admin.rekonsiliasi.pdf', compact('data', 'setting', 'logoSrc'));

        return $pdf->stream('rekonsiliasi-bank.pdf');
    }

    // =========================================================================
    // EXPORT EXCEL
    // =========================================================================

    public function exportExcel(Request $request)
    {
        $filename = 'rekonsiliasi_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new RekonsiliasiExport($request->search),
            $filename
        );
    }
}
