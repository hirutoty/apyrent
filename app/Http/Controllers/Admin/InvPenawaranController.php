<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\InvPenawaran;
use App\Models\InvPenawaranItem;
use App\Models\Kendaraan;
use App\Models\Rental;
use App\Models\Member;
use App\Models\Setting;
use Carbon\Carbon;


class InvPenawaranController
{
    public function index(Request $request)
{
    // Update otomatis menjadi expired
    InvPenawaran::whereNotIn('status', ['approved', 'rejected', 'expired'])
        ->get()
        ->each(function ($penawaran) {

            $expiredDate = Carbon::parse($penawaran->tanggal_penawaran)
                ->addMonths($penawaran->periode)
                ->startOfDay();

            if (now()->startOfDay()->gt($expiredDate)) {
                $penawaran->update([
                    'status' => 'expired'
                ]);
            }
        });

    $query = InvPenawaran::with('items.kendaraan')->latest();

    if ($request->search) {
        $query->where(function ($q) use ($request) {
            $q->where('no_penawaran', 'like', '%' . $request->search . '%')
                ->orWhere('customer_name', 'like', '%' . $request->search . '%')
                ->orWhere('kepada', 'like', '%' . $request->search . '%');
        });
    }

    $penawarans = $query->paginate(10);

    $kendaraans = Kendaraan::whereIn('status_kendaraan', ['tersedia', 'disewa'])
        ->orderBy('nopol')
        ->get();

    // Setting reminder
    $setting = Setting::first();
        // Base64 logo untuk DomPDF
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

    $reminder = match ($setting->satuan_reminder) {
        'hari'   => $setting->batas_reminder,
        'minggu' => $setting->batas_reminder * 7,
        'bulan'  => $setting->batas_reminder * 30,
        'tahun'  => $setting->batas_reminder * 365,
        default  => $setting->batas_reminder,
    };

    // Hitung reminder tiap penawaran
    foreach ($penawarans as $p) {

        $p->tanggalBerakhir = Carbon::parse($p->tanggal_penawaran)
            ->startOfDay()
            ->addMonths($p->periode);

        $p->sisaHari = (int) now()->startOfDay()
            ->diffInDays($p->tanggalBerakhir, false);

        // Tidak dihitung jika sudah selesai
        if (in_array($p->status, ['approved', 'rejected', 'expired'])) {

            $p->isExpired = false;
            $p->isSoon = false;

        } else {

            $p->isExpired = $p->sisaHari < 0;
            $p->isSoon = !$p->isExpired && $p->sisaHari <= $reminder;

        }
    }

    return view('admin.penawaran.index', compact(
        'penawarans',
        'kendaraans',
        'reminder'
    ));
}

    public function store(Request $request)
    {
        // BUG FIX: no_penawaran di-generate, bukan dijadikan rule validasi
        $noPenawaran = $this->generateNoPenawaran();

        $request->validate([
            'tanggal_penawaran' => 'required',
            'customer_name'     => 'required',
            'kendaraan_id'      => 'required|array|min:1',
            'kendaraan_id.*'    => 'required',
            'qty'               => 'required|array|min:1',
            'qty.*'             => 'required|numeric|min:1',
            'price'             => 'required|array|min:1',
            'price.*'           => 'required|numeric|min:0',
        ]);

        $total = 0;
        foreach ($request->price as $i => $price) {
            $total += ($request->qty[$i] * $price);
        }

        DB::transaction(function () use ($request, $total, $noPenawaran) {

            $penawaran = InvPenawaran::create([
                'no_penawaran'      => $noPenawaran,
                'tanggal_penawaran' => $request->tanggal_penawaran,
                'kepada'            => $request->kepada,
                'up'                => $request->up,
                'perihal'           => $request->perihal,
                'customer_name'     => $request->customer_name,
                'contact_person'    => $request->contact_person,
                'pengirim'          => $request->pengirim,
                'periode'           => $request->periode,
                'staff'             => $request->staff,
                'name_staff'        => $request->name_staff,
                'direktur'          => $request->direktur,
                'name_direktur'     => $request->name_direktur,

                'total'             => $total,
            ]);

            foreach ($request->kendaraan_id as $i => $kendaraan) {
                InvPenawaranItem::create([
                    'penawaran_id'  => $penawaran->id,
                    'kendaraan_id'  => $kendaraan,
                    'qty'           => $request->qty[$i],
                    'tahun_unit'    => $request->tahun_unit[$i] ?? null,
                    'price'         => $request->price[$i],
                    'durasi'        => $request->durasi[$i] ?? null,
                    'satuan_durasi' => $request->satuan_durasi[$i] ?? null,
                ]);
            }
        });

        return back()->with('success', 'Penawaran berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $penawaran = InvPenawaran::with('items.kendaraan')->findOrFail($id);

        return response()->json([
            'id' => $penawaran->id,
            'no_penawaran' => $penawaran->no_penawaran,
            'tanggal_penawaran' => optional($penawaran->tanggal_penawaran)->format('Y-m-d'),
            'kepada' => $penawaran->kepada,
            'up' => $penawaran->up,
            'perihal' => $penawaran->perihal,
            'customer_name' => $penawaran->customer_name,
            'contact_person' => $penawaran->contact_person,
            'pengirim' => $penawaran->pengirim,
            'staff' => $penawaran->staff,
            'name_staff' => $penawaran->name_staff,
            'direktur' => $penawaran->direktur,
            'name_direktur' => $penawaran->name_direktur,
            'periode' => $penawaran->periode,
            'items' => $penawaran->items,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_penawaran' => 'required',
            'customer_name'     => 'required',
            'kendaraan_id'      => 'required|array|min:1',
            'kendaraan_id.*'    => 'required',
            'qty'               => 'required|array|min:1',
            'qty.*'             => 'required|numeric|min:1',
            'price'             => 'required|array|min:1',
            'price.*'           => 'required|numeric|min:0',
        ]);

        $total = 0;
        foreach ($request->price as $i => $price) {
            $total += ($request->qty[$i] * $price);
        }

        DB::transaction(function () use ($request, $id, $total) {

            $penawaran = InvPenawaran::findOrFail($id);

            // Update data penawaran
            $penawaran->update([
                'no_penawaran'      => $request->no_penawaran,
                'tanggal_penawaran' => $request->tanggal_penawaran,
                'kepada'            => $request->kepada,
                'up'                => $request->up,
                'perihal'           => $request->perihal,
                'customer_name'     => $request->customer_name,
                'contact_person'    => $request->contact_person,
                'pengirim'          => $request->pengirim,
                'periode'           => $request->periode,
                'staff'             => $request->staff,
                'name_staff'        => $request->name_staff,
                'direktur'          => $request->direktur,
                'name_direktur'     => $request->name_direktur,
                'total'             => $total,
            ]);

            // Hapus item lama
            $penawaran->items()->delete();

            // Simpan item baru
            foreach ($request->kendaraan_id as $i => $kendaraan) {
                InvPenawaranItem::create([
                    'penawaran_id'  => $penawaran->id,
                    'kendaraan_id'  => $kendaraan,
                    'qty'           => $request->qty[$i],
                    'tahun_unit'    => $request->tahun_unit[$i] ?? null,
                    'price'         => $request->price[$i],
                    'durasi'        => $request->durasi[$i] ?? null,
                    'satuan_durasi' => $request->satuan_durasi[$i] ?? null,
                ]);
            }

            // Ubah status hanya jika belum approved atau rejected
            if (!in_array($penawaran->status, ['approved', 'rejected'])) {
                $periode = (int) $request->input('periode');

                $expired = Carbon::parse($request->tanggal_penawaran)
                    ->addMonths($periode);

                $penawaran->update([
                    'status' => now()->gt($expired) ? 'expired' : 'pending'
                ]);
            }
        });

        return back()->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $penawaran = InvPenawaran::findOrFail($id);
        $penawaran->delete();

        return back()->with('success', 'Data berhasil dihapus.');
    }

    private function generateNoPenawaran(): string
    {
        $prefix = 'PNW-' . date('Ym') . '-';

        $last = InvPenawaran::where('no_penawaran', 'like', $prefix . '%')
            ->latest('id')
            ->first();

        if (!$last) {
            return $prefix . '0001';
        }

        $number = (int) substr($last->no_penawaran, -4);

        return $prefix . str_pad($number + 1, 4, '0', STR_PAD_LEFT);
    }

    public function approve($id)
    {
        DB::beginTransaction();

        try {
            \Log::info('APPROVE JALAN', ['id' => $id]);

            $penawaran = InvPenawaran::with('items')->findOrFail($id);

            if ($penawaran->items->isEmpty()) {
                return back()->with('error', 'Item penawaran kosong');
            }

            // =========================
            // 1. CREATE / GET MEMBER (HANYA 1X)
            // =========================
            $member = Member::firstOrCreate(
                ['nama_member' => $penawaran->kepada],
                [
                    'kontak_member' => $penawaran->contact_person ?? null,
                ]
            );

            // update status penawaran
            $penawaran->update(['status' => 'approved']);

            // =========================
            // 2. LOOP ITEM
            // =========================
            foreach ($penawaran->items as $item) {

                $tanggalMulai = now();
                $durasi = (int) $item->durasi;
                $satuan = strtolower($item->satuan_durasi);

                // default fallback
                $tanggalSelesai = $tanggalMulai->copy()->addDay();

                if ($durasi > 0) {
                    if ($satuan === 'hari') {
                        $tanggalSelesai = $tanggalMulai->copy()->addDays($durasi);
                    } elseif ($satuan === 'bulan') {
                        $tanggalSelesai = $tanggalMulai->copy()->addMonths($durasi);
                    } elseif ($satuan === 'tahun') {
                        $tanggalSelesai = $tanggalMulai->copy()->addYears($durasi);
                    }
                }

                Rental::create([
                    'user_id'      => auth()->id() ?? 1,
                    'kendaraan_id' => $item->kendaraan_id,
                    'member_id'    => $member->id,

                    'tanggal_mulai'   => $tanggalMulai,
                    'tanggal_selesai' => $tanggalSelesai,

                    'durasi_hari'  => $satuan === 'hari'  ? $durasi : null,
                    'durasi_bulan' => $satuan === 'bulan' ? $durasi : null,
                    'durasi_tahun' => $satuan === 'tahun' ? $durasi : null,

                    'biaya_dasar'          => $item->qty * $item->price,
                    'biaya_tambahan_total' => 0,
                    'total_biaya'          => $item->qty * $item->price,

                    'metode_pembayaran' => 'transfer',
                    'jenis_pembayaran'  => 'lunas',
                    'status_pembayaran' => 'belum_bayar',
                    'status'            => 'booking',
                ]);

                Kendaraan::where('id', $item->kendaraan_id);
            }

            DB::commit();

            return back()->with('success', 'Penawaran berhasil di-approve & masuk rental');
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('APPROVE ERROR', [
                'msg' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', $e->getMessage());
        }
    }

    public function reject($id)
    {
        DB::beginTransaction();

        try {

            $penawaran = InvPenawaran::findOrFail($id);

            // Jangan bisa reject jika sudah approved
            if ($penawaran->status == 'approved') {
                return back()->with('error', 'Penawaran yang sudah di-approve tidak dapat di-reject.');
            }

            $penawaran->update([
                'status' => 'rejected'
            ]);

            DB::commit();

            return back()->with('success', 'Penawaran berhasil ditolak.');
        } catch (\Exception $e) {

            DB::rollBack();

            \Log::error('REJECT ERROR', [
                'msg' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', $e->getMessage());
        }
    }

    public function exportPdf(Request $request)
    {
        $query = InvPenawaran::with('items.kendaraan')->latest();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('no_penawaran', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $request->search . '%')
                  ->orWhere('kepada', 'like', '%' . $request->search . '%');
            });
        }

        $penawarans = $query->get();
        $setting    = Setting::first();
        // Base64 logo untuk DomPDF
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.penawaran.pdf', [
            'penawarans' => $penawarans,
            'setting'    => $setting,
            'logoSrc'    => $logoSrc,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('Daftar-Penawaran-' . now()->format('Y-m-d') . '.pdf');
    }
}
