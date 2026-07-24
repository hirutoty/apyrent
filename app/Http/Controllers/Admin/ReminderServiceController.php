<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReminderService;
use App\Models\ServiceDetail;
use App\Models\Kendaraan;
use Carbon\Carbon;

class ReminderServiceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $status = $request->status;

        $data = ReminderService::with('kendaraan')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('nama_reminder', 'like', "%{$search}%")
                        ->orWhere('keterangan', 'like', "%{$search}%")
                        ->orWhereHas('kendaraan', fn($k) =>
                            $k->where('merk', 'like', "%{$search}%")
                              ->orWhere('nopol', 'like', "%{$search}%")
                        );
                });
            })
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $kendaraan = Kendaraan::orderBy('merk')->get();

        // Summary counts
        $totalAktif      = ReminderService::where('status', 'aktif')->count();
        $totalJatuhTempo = ReminderService::where('status', 'jatuh_tempo')->count();
        $totalSelesai    = ReminderService::where('status', 'selesai')->count();

        return view('admin.service.reminder_service', compact(
            'data', 'kendaraan', 'totalAktif', 'totalJatuhTempo', 'totalSelesai'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kendaraan_id'              => 'required|exists:kendaraan,id',
            'items'                     => 'required|array|min:1',
            'items.*.nama_reminder'     => 'required|string|max:255',
            'items.*.tanggal_mulai'     => 'required|date',
            'items.*.interval_nilai'    => 'required|integer|min:1',
            'items.*.interval_satuan'   => 'required|in:hari,minggu,bulan,tahun',
            'items.*.keterangan'        => 'nullable|string',
            'items.*.biaya'             => 'nullable|numeric|min:0',
        ], [
            'items.required'                    => 'Minimal satu reminder harus diisi.',
            'items.*.nama_reminder.required'    => 'Nama reminder wajib diisi.',
            'items.*.tanggal_mulai.required'    => 'Tanggal mulai wajib diisi.',
            'items.*.interval_nilai.required'   => 'Interval wajib diisi.',
            'items.*.interval_satuan.required'  => 'Satuan interval wajib dipilih.',
            'items.*.biaya.numeric'             => 'Biaya harus berupa angka.',
            'items.*.biaya.min'                 => 'Biaya tidak boleh negatif.',
        ]);

        foreach ($request->items as $item) {
            $tanggalMulai = Carbon::parse($item['tanggal_mulai']);
            $jatuhTempo   = $this->hitungJatuhTempo(
                $tanggalMulai,
                (int) $item['interval_nilai'],
                $item['interval_satuan']
            );

            $status = Carbon::today()->gte($jatuhTempo) ? 'jatuh_tempo' : 'aktif';

            $reminder = ReminderService::create([
                'kendaraan_id'         => $request->kendaraan_id,
                'nama_reminder'        => $item['nama_reminder'],
                'tanggal_mulai'        => $tanggalMulai->toDateString(),
                'interval_nilai'       => $item['interval_nilai'],
                'interval_satuan'      => $item['interval_satuan'],
                'tanggal_jatuh_tempo'  => $jatuhTempo->toDateString(),
                'keterangan'           => $item['keterangan'] ?? null,
                'biaya'                => isset($item['biaya']) && $item['biaya'] !== '' ? $item['biaya'] : null,
                'status'               => $status,
                'sudah_dibuat_masalah' => false,
            ]);

            // Kalau langsung jatuh tempo saat dibuat, auto-create service_detail
            if ($status === 'jatuh_tempo') {
                $this->buatServiceDetail($reminder);
            }
        }

        $jumlah = count($request->items);
        return back()->with('success', $jumlah . ' reminder berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kendaraan_id'    => 'required|exists:kendaraan,id',
            'nama_reminder'   => 'required|string|max:255',
            'tanggal_mulai'   => 'required|date',
            'interval_nilai'  => 'required|integer|min:1',
            'interval_satuan' => 'required|in:hari,minggu,bulan,tahun',
            'keterangan'      => 'nullable|string',
            'biaya'           => 'nullable|numeric|min:0',
        ]);

        $reminder     = ReminderService::findOrFail($id);
        $tanggalMulai = Carbon::parse($request->tanggal_mulai);
        $jatuhTempo   = $this->hitungJatuhTempo(
            $tanggalMulai,
            $request->interval_nilai,
            $request->interval_satuan
        );

        $status = Carbon::today()->gte($jatuhTempo) ? 'jatuh_tempo' : 'aktif';

        $reminder->update([
            'kendaraan_id'        => $request->kendaraan_id,
            'nama_reminder'       => $request->nama_reminder,
            'tanggal_mulai'       => $request->tanggal_mulai,
            'interval_nilai'      => $request->interval_nilai,
            'interval_satuan'     => $request->interval_satuan,
            'tanggal_jatuh_tempo' => $jatuhTempo->toDateString(),
            'keterangan'          => $request->keterangan,
            'biaya'               => $request->biaya !== '' ? $request->biaya : null,
            'status'              => $status,
            // Reset sudah_dibuat_masalah jika tanggal/interval berubah
            'sudah_dibuat_masalah' => false,
        ]);

        return back()->with('success', 'Reminder berhasil diupdate.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:aktif,jatuh_tempo,selesai',
        ]);

        $reminder = ReminderService::findOrFail($id);
        $reminder->update(['status' => $request->status]);

        return back()->with('success', 'Status reminder berhasil diubah.');
    }

    public function destroy($id)
    {
        ReminderService::findOrFail($id)->delete();
        return back()->with('success', 'Reminder berhasil dihapus.');
    }

    // ── HELPER ─────────────────────────────────────────────

    private function hitungJatuhTempo(Carbon $tanggal, int $nilai, string $satuan): Carbon
    {
        return match ($satuan) {
            'hari'   => (clone $tanggal)->addDays($nilai),
            'minggu' => (clone $tanggal)->addWeeks($nilai),
            'bulan'  => (clone $tanggal)->addMonths($nilai),
            'tahun'  => (clone $tanggal)->addYears($nilai),
            default  => (clone $tanggal)->addMonths($nilai),
        };
    }

    /**
     * Buat entry service_detail (Mobil Bermasalah) dari reminder yang jatuh tempo
     */
    public static function buatServiceDetail(ReminderService $reminder): void
    {
        if ($reminder->sudah_dibuat_masalah) return;

        ServiceDetail::create([
            'kendaraan_id'    => $reminder->kendaraan_id,
            'tanggal_service' => Carbon::today()->toDateString(),
            'kilometer'       => 0,
            'status'          => 'Tidak Layak',
            'biaya'           => $reminder->biaya ?? 0,
            'keterangan'      => '[Reminder: ' . $reminder->nama_reminder . '] ' . ($reminder->keterangan ?? ''),
            'bukti'           => null,
        ]);

        $reminder->update([
            'sudah_dibuat_masalah' => true,
            'status'               => 'jatuh_tempo',
        ]);
    }
}
