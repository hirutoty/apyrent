<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceHistory;
use App\Models\ServiceDetail;
use App\Models\Kendaraan;
use App\Models\Keuangan;
use App\Models\Bukubesar;
use App\Models\Setting;
use App\Models\Attachment;
use App\Models\ReminderService;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ServiceHistoryController extends Controller
{
    public function index(Request $request)
    {
        $bulan  = $request->bulan ?? now()->format('Y-m');
        $search = $request->search;

        $data = ServiceHistory::with(['kendaraan', 'attachments'])
            ->when($bulan, fn($q) => $q->whereRaw("DATE_FORMAT(tanggal_service,'%Y-%m') = ?", [$bulan]))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('keluhan', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhere('kilometer', 'like', "%{$search}%")
                        ->orWhereHas(
                            'kendaraan',
                            fn($k) =>
                            $k->where('merk', 'like', "%{$search}%")
                                ->orWhere('nopol', 'like', "%{$search}%")
                        );
                });
            })
            ->latest()
            ->paginate(15)->withQueryString();

        // Hanya kendaraan yang ada di service_detail dengan status 'Tidak Layak'
        $kendaraanIdsTidakLayak = ServiceDetail::where('status', 'Tidak Layak')
            ->pluck('kendaraan_id')
            ->unique();

        $kendaraan = Kendaraan::whereIn('id', $kendaraanIdsTidakLayak)
            ->orderBy('merk')
            ->get();

        // Data akumulasi per kendaraan dari service_detail status 'Tidak Layak'
        $detailPerKendaraan = ServiceDetail::where('status', 'Tidak Layak')
            ->whereIn('kendaraan_id', $kendaraanIdsTidakLayak)
            ->orderBy('tanggal_service', 'desc')
            ->get()
            ->groupBy('kendaraan_id')
            ->map(function ($items) {
                return [
                    // Gabungan semua keluhan
                    'keluhan_gabungan' => $items
                        ->pluck('keterangan')
                        ->filter()
                        ->implode(', '),
                    // Total biaya akumulasi
                    'total_biaya' => $items->sum('biaya'),
                    // Kilometer dari record terbaru
                    'kilometer' => $items->first()->kilometer ?? 0,
                    // Rincian per item untuk ditampilkan
                    'rincian' => $items->map(fn($d) => [
                        'keluhan' => $d->keterangan ?? '-',
                        'biaya'   => $d->biaya ?? 0,
                        'tanggal' => $d->tanggal_service ?? '-',
                    ])->values()->toArray(),
                ];
            });

        return view('admin.service.service_history', [
            'data'               => $data,
            'kendaraan'          => $kendaraan,
            'bulan'              => $bulan,
            'detailPerKendaraan' => $detailPerKendaraan,
        ]);
    }

    /**
     * Helper: simpan banyak attachment sekaligus untuk 1 service history
     * (konsisten dengan Pajak, Asuransi, GPS, KIR)
     */
    private function simpanAttachments($files, $serviceId)
    {
        $pathDir = public_path('service/attachments');
        if (!file_exists($pathDir)) mkdir($pathDir, 0777, true);

        foreach ($files as $file) {
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // ambil data SEBELUM file dipindah
            $originalName = $file->getClientOriginalName();
            $extension    = $file->getClientOriginalExtension();
            $size         = $file->getSize();

            $file->move($pathDir, $filename);

            Attachment::create([
                'relation_type' => 'service',
                'relation_id'   => $serviceId,
                'file_name'     => $originalName,
                'file_path'     => 'service/attachments/' . $filename,
                'file_type'     => $extension,
                'file_size'     => $size,
            ]);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'kendaraan_id'    => 'required|exists:kendaraan,id',
            'tanggal_service' => 'required|date',
            'total_biaya'     => 'required|numeric|min:0',
            'bukti_pembayaran' => 'nullable|file|max:5120',
            'bukti_attachment'   => 'nullable|array',
            'bukti_attachment.*' => 'file|max:5120',
        ]);

        $kendaraan    = Kendaraan::findOrFail($request->kendaraan_id);
        $limitService = $kendaraan->limit_bulan_service ?? 0;
        $bulan        = date('Y-m', strtotime($request->tanggal_service));

        // Hitung total service bulan ini
        $totalBulanIni = ServiceHistory::where('kendaraan_id', $request->kendaraan_id)
            ->whereRaw("DATE_FORMAT(tanggal_service, '%Y-%m') = ?", [$bulan])
            ->sum('total_biaya');

        $sisaLimit = $limitService - ($totalBulanIni + $request->total_biaya);

        // Hitung biaya tahunan otomatis
        $biayaTahunan = ServiceHistory::where('kendaraan_id', $request->kendaraan_id)
            ->whereYear('tanggal_service', date('Y', strtotime($request->tanggal_service)))
            ->sum('total_biaya') + $request->total_biaya;

        // maks_bulanan otomatis dari kendaraan
        $maksBulanan = $limitService;

        // Tentukan status_pengeluaran otomatis
        $limitTahunan      = $kendaraan->limit_tahun_service ?? 0;
        $statusPengeluaran = ($limitService > 0 && ($totalBulanIni + $request->total_biaya) > $limitService)
            ? 'overservice' : 'stabil';

        // Upload bukti pembayaran
        $buktiBayar = null;

        if ($request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $destination = public_path('bukti_pembayaran');

            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }
            $file->move($destination, $filename);
            $buktiBayar = 'bukti_pembayaran/' . $filename;
        }

        // Simpan file attachment jika ada
        $attachments = [];
        if ($request->hasFile('bukti_attachment')) {
            $pathDir = public_path('service/attachments');
            if (!file_exists($pathDir)) mkdir($pathDir, 0777, true);

            foreach ($request->file('bukti_attachment') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $originalName = $file->getClientOriginalName();
                $extension    = $file->getClientOriginalExtension();
                $size         = $file->getSize();

                $file->move($pathDir, $filename);

                $attachments[] = [
                    'file_name' => $originalName,
                    'file_path' => 'service/attachments/' . $filename,
                    'file_type' => $extension,
                    'file_size' => $size,
                ];
            }
        }

        \Illuminate\Support\Facades\DB::transaction(function () use (
            $request, $sisaLimit, $maksBulanan, $biayaTahunan, $statusPengeluaran,
            $buktiBayar, $kendaraan, $attachments
        ) {
            $service = ServiceHistory::create([
                'kendaraan_id'      => $request->kendaraan_id,
                'keluhan'           => $request->keluhan,
                'kilometer'         => $request->kilometer,
                'total_biaya'       => $request->total_biaya,
                'status'            => $request->status,
                'tanggal_service'   => $request->tanggal_service,
                'sisa_limit'        => $sisaLimit,
                'maks_bulanan'      => $maksBulanan,
                'biaya_tahunan'     => $biayaTahunan,
                'status_pengeluaran' => $statusPengeluaran,
                'bukti_pembayaran'  => $buktiBayar,
            ]);

            // Save attachments
            foreach ($attachments as $att) {
                \App\Models\Attachment::create([
                    'relation_type' => 'service',
                    'relation_id'   => $service->id,
                    'file_name'     => $att['file_name'],
                    'file_path'     => $att['file_path'],
                    'file_type'     => $att['file_type'],
                    'file_size'     => $att['file_size'],
                ]);
            }

            // Update status kendaraan
            $kendaraan->update([
                'status_kendaraan' => $request->status === 'proses' ? 'service' : 'tersedia',
            ]);

            // Ubah semua record service_detail kendaraan ini dari 'Tidak Layak' jadi 'Layak'
            ServiceDetail::where('kendaraan_id', $request->kendaraan_id)
                ->where('status', 'Tidak Layak')
                ->update(['status' => 'Layak']);

            // 🔔 Jika status = selesai → update tanggal_terakhir_service & reset ReminderService
            if ($request->status === 'selesai') {
                $kendaraan->update([
                    'tanggal_terakhir_service' => $request->tanggal_service,
                ]);
                $this->resetReminderService($kendaraan, $request->tanggal_service);
            }

            // Catat keuangan
            $lastSaldo = (float) \Illuminate\Support\Facades\DB::table('keuangans')->lockForUpdate()->orderBy('id', 'desc')->value('saldo') ?? 0;
            $kodeJurnal = 'SRV-' . $service->id;

            if (!Keuangan::where('reference', $kodeJurnal)->exists()) {
                Keuangan::create([
                    'tanggal'     => $request->tanggal_service,
                    'reference'   => $kodeJurnal,
                    'user_id'     => auth()->id(),
                    'kategori'    => 'Pengeluaran',
                    'metode'      => 'Cash',
                    'keterangan'  => 'Service Kendaraan',
                    'pemasukan'   => 0,
                    'pengeluaran' => $request->total_biaya,
                    'saldo'       => $lastSaldo - $request->total_biaya,
                    'source_type' => 'service_history',
                    'source_id'   => $service->id,
                ]);
            }

            // Auto-posting ke Buku Besar
            if (!Bukubesar::where('kode_jurnal', $kodeJurnal)->exists()) {
                $saldoBBTerakhir = (float) \Illuminate\Support\Facades\DB::table('bukubesars')->lockForUpdate()->orderBy('id', 'desc')->value('saldo') ?? 0;

                Bukubesar::create([
                    'kode_jurnal' => $kodeJurnal,
                    'transaksi'   => 'Beban Service - ' . ($service->kendaraan->merk ?? '-') . ' ' . ($service->kendaraan->nopol ?? '-'),
                    'kategori'    => 'Beban',
                    'tanggal'     => $request->tanggal_service,
                    'debit'       => $request->total_biaya,
                    'kredit'      => 0,
                    'saldo'       => $saldoBBTerakhir - $request->total_biaya, // Asumsi Beban mengurangi saldo BB
                    'aktivitas'   => 'Operasi',
                    'keterangan'  => 'Auto-posting: Service kendaraan ' . ($service->kendaraan->nopol ?? '-'),
                ]);
            }
        });

        return back()->with('success', 'Data service berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kendaraan_id'    => 'required|exists:kendaraan,id',
            'tanggal_service' => 'required|date',
            'total_biaya'     => 'required|numeric|min:0',
            'bukti_pembayaran' => 'nullable|file|max:5120',
            'bukti_attachment'   => 'nullable|array',
            'bukti_attachment.*' => 'file|max:5120',
        ]);

        $data         = ServiceHistory::findOrFail($id);
        $kendaraan    = Kendaraan::findOrFail($request->kendaraan_id);
        $limitService = $kendaraan->limit_bulan_service ?? 0;
        $bulan        = date('Y-m', strtotime($request->tanggal_service));

        $totalBulanIni = ServiceHistory::where('kendaraan_id', $request->kendaraan_id)
            ->where('id', '!=', $id)
            ->whereRaw("DATE_FORMAT(tanggal_service, '%Y-%m') = ?", [$bulan])
            ->sum('total_biaya');

        $sisaLimit = $limitService - ($totalBulanIni + $request->total_biaya);

        // Hitung biaya tahunan (exclude record ini)
        $biayaTahunan = ServiceHistory::where('kendaraan_id', $request->kendaraan_id)
            ->where('id', '!=', $id)
            ->whereYear('tanggal_service', date('Y', strtotime($request->tanggal_service)))
            ->sum('total_biaya') + $request->total_biaya;

        $maksBulanan       = $limitService;
        $limitTahunan      = $kendaraan->limit_tahun_service ?? 0;
        $statusPengeluaran = ($limitService > 0 && ($totalBulanIni + $request->total_biaya) > $limitService)
            ? 'overservice' : 'stabil';

        // Upload bukti pembayaran jika ada file baru
        $buktiBayar = $data->bukti_pembayaran;

        if ($request->hasFile('bukti_pembayaran')) {

            // hapus file lama
            if ($data->bukti_pembayaran && file_exists(public_path($data->bukti_pembayaran))) {
                unlink(public_path($data->bukti_pembayaran));
            }

            $file = $request->file('bukti_pembayaran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $destination = public_path('bukti_pembayaran');

            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }

            $file->move($destination, $filename);

            $buktiBayar = 'bukti_pembayaran/' . $filename;
        }

        $data->update([
            'kendaraan_id'       => $request->kendaraan_id,
            'keluhan'            => $request->keluhan,
            'kilometer'          => $request->kilometer,
            'total_biaya'        => $request->total_biaya,
            'status'             => $request->status,
            'tanggal_service'    => $request->tanggal_service,
            'sisa_limit'         => $sisaLimit,
            'maks_bulanan'       => $maksBulanan,
            'biaya_tahunan'      => $biayaTahunan,
            'status_pengeluaran' => $statusPengeluaran,
            'bukti_pembayaran'   => $buktiBayar,
        ]);

        // upload attachment tambahan
        if ($request->hasFile('bukti_attachment')) {
            $this->simpanAttachments($request->file('bukti_attachment'), $data->id);
        }

        // Update status kendaraan
        $kendaraan->update([
            'status_kendaraan' => $request->status === 'proses' ? 'service' : 'tersedia',
        ]);

        // 🔔 Jika status = selesai → update tanggal_terakhir_service & reset ReminderService
        if ($request->status === 'selesai') {
            $kendaraan->update([
                'tanggal_terakhir_service' => $request->tanggal_service,
            ]);
            $this->resetReminderService($kendaraan, $request->tanggal_service);
        }

        return back()->with('success', 'Data berhasil diupdate.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:proses,selesai',
        ]);

        $service = ServiceHistory::findOrFail($id);

        $service->update([
            'status' => $request->status,
        ]);

        // Sinkron status kendaraan
        $service->kendaraan->update([
            'status_kendaraan' => $request->status === 'proses'
                ? 'service'
                : 'tersedia',
        ]);

        // 🔔 Jika status berubah ke 'selesai' → update tanggal_terakhir_service & reset ReminderService
        if ($request->status === 'selesai') {
            $tanggalSelesai = $service->tanggal_service ?? now()->toDateString();
            $service->kendaraan->update([
                'tanggal_terakhir_service' => $tanggalSelesai,
            ]);
            $this->resetReminderService($service->kendaraan, $tanggalSelesai);
        }

        return back()->with('success', 'Status berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $service   = ServiceHistory::findOrFail($id);
        $kendaraan = $service->kendaraan;

        // hapus semua file attachment terkait
        foreach ($service->attachments as $att) {
            if (file_exists(public_path($att->file_path))) {
                unlink(public_path($att->file_path));
            }
            $att->delete();
        }

        $service->delete();

        // Jika tidak ada service aktif lain yang masih proses, kembalikan status tersedia
        if ($kendaraan) {
            $masihProses = ServiceHistory::where('kendaraan_id', $kendaraan->id)
                ->where('status', 'proses')
                ->exists();

            if (!$masihProses) {
                $kendaraan->update(['status_kendaraan' => 'tersedia']);
            }
        }

        return back()->with('success', 'Data berhasil dihapus.');
    }

    /**
     * Hapus 1 attachment tertentu
     */
    public function destroyAttachment($id)
    {
        $attachment = Attachment::where('relation_type', 'service')->findOrFail($id);

        if (file_exists(public_path($attachment->file_path))) {
            unlink(public_path($attachment->file_path));
        }

        $attachment->delete();

        return back()->with('success', 'Lampiran berhasil dihapus');
    }

    public function pdf(Request $request)
    {
        $search = $request->search;
        $bulan  = $request->bulan;

        $data = ServiceHistory::with(['kendaraan', 'attachments'])
            ->when($bulan, fn($q) => $q->whereRaw("DATE_FORMAT(tanggal_service, '%Y-%m') = ?", [$bulan]))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('keluhan', 'like', "%$search%")
                        ->orWhere('status', 'like', "%$search%")
                        ->orWhereHas(
                            'kendaraan',
                            fn($k) =>
                            $k->where('merk', 'like', "%$search%")
                                ->orWhere('nopol', 'like', "%$search%")
                        );
                });
            })
            ->latest()
            ->get();

        $setting = Setting::first();

        // Base64 logo untuk DomPDF
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf     = Pdf::loadView('admin.service.pdf_history', compact('data', 'search', 'bulan', 'setting', 'logoSrc'));

        return $pdf->stream('service-history.pdf');
    }

    // ── HELPER ──────────────────────────────────────────────────────────────

    /**
     * Reset ReminderService "Service Rutin" untuk kendaraan setelah service selesai.
     * - Tandai reminder lama (aktif/jatuh_tempo) sebagai 'selesai'.
     * - Buat reminder baru +3 bulan dari tanggal selesai service.
     */
    private function resetReminderService(Kendaraan $kendaraan, string $tanggalSelesai): void
    {
        // Tandai semua reminder Service Rutin yang masih aktif/jatuh_tempo sebagai selesai
        ReminderService::where('kendaraan_id', $kendaraan->id)
            ->where('nama_reminder', 'Service Rutin')
            ->whereIn('status', ['aktif', 'jatuh_tempo'])
            ->update(['status' => 'selesai']);

        // Buat reminder baru +3 bulan dari tanggal selesai service
        $tanggalMulai = Carbon::parse($tanggalSelesai);
        $jatuhTempo   = (clone $tanggalMulai)->addMonths(3);
        $statusReminder = Carbon::today()->gte($jatuhTempo) ? 'jatuh_tempo' : 'aktif';

        ReminderService::create([
            'kendaraan_id'         => $kendaraan->id,
            'nama_reminder'        => 'Service Rutin',
            'tanggal_mulai'        => $tanggalMulai->toDateString(),
            'interval_nilai'       => 3,
            'interval_satuan'      => 'bulan',
            'tanggal_jatuh_tempo'  => $jatuhTempo->toDateString(),
            'keterangan'           => 'Auto-reset setelah service selesai pada ' . $tanggalMulai->format('d/m/Y'),
            'status'               => $statusReminder,
            'sudah_dibuat_masalah' => false,
        ]);
    }
}