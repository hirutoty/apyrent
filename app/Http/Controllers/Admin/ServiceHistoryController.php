<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceHistory;
use App\Models\Kendaraan;
use App\Models\Keuangan;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

class ServiceHistoryController extends Controller
{
    public function index(Request $request)
    {
        $bulan  = $request->bulan ?? now()->format('Y-m');
        $search = $request->search;

        $data = ServiceHistory::with('kendaraan')
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
            ->get();

        return view('admin.service.service_history', [
            'data'      => $data,
            'kendaraan' => Kendaraan::all(),
            'bulan'     => $bulan,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kendaraan_id'    => 'required|exists:kendaraan,id',
            'tanggal_service' => 'required|date',
            'total_biaya'     => 'required|numeric|min:0',
            'bukti_pembayaran' => 'nullable|file|max:5120',
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

        // Update status kendaraan
        $kendaraan->update([
            'status_kendaraan' => $request->status === 'proses' ? 'service' : 'tersedia',
        ]);

        // Catat keuangan
        $lastSaldo = Keuangan::latest()->value('saldo') ?? 0;
        Keuangan::create([
            'tanggal'     => $request->tanggal_service,
            'reference'   => 'SRV-' . time(),
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

        return back()->with('success', 'Data service berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kendaraan_id'    => 'required|exists:kendaraan,id',
            'tanggal_service' => 'required|date',
            'total_biaya'     => 'required|numeric|min:0',
            'bukti_pembayaran' => 'nullable|file|max:5120',
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

        // Update status kendaraan
        $kendaraan->update([
            'status_kendaraan' => $request->status === 'proses' ? 'service' : 'tersedia',
        ]);

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

        return back()->with('success', 'Status berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $service   = ServiceHistory::findOrFail($id);
        $kendaraan = $service->kendaraan;

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

    public function pdf(Request $request)
    {
        $search = $request->search;
        $bulan  = $request->bulan;

        $data = ServiceHistory::with('kendaraan')
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
        $pdf     = Pdf::loadView('admin.service.pdf_history', compact('data', 'search', 'bulan', 'setting'));

        return $pdf->stream('service-history.pdf');
    }
}
