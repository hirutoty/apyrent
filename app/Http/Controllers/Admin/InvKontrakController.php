<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvKontrak;
use App\Models\InvPenawaran;
use App\Models\InvPenawaranItem;
use App\Models\Invoice;
use App\Models\Rental;
use App\Models\Kendaraan;
use App\Models\Pelanggan;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;


class InvKontrakController extends Controller
{
    /* ─────────────────────────────────────────────
       INDEX
    ───────────────────────────────────────────── */
    public function index()
    {
        // Auto-expire kontrak yang sudah melewati perjanjian_pembayaran
        InvKontrak::whereNotIn('status', ['expired', 'completed', 'terminated', 'rejected', 'selesai-belum lunas'])
            ->whereNotNull('perjanjian_pembayaran')
            ->get()
            ->each(function ($k) {
                $batas = Carbon::parse($k->perjanjian_pembayaran)->startOfDay();
                if (now()->startOfDay()->gt($batas)) {
                    $k->update(['status' => 'expired']);
                }
            });

        $kontraks = InvKontrak::with('penawaran.items.kendaraan')
            ->latest()
            ->paginate(15)->withQueryString();

        $penawarans = InvPenawaran::whereIn('status', ['approved', 'active'])
            ->latest()
            ->get();
        $setting    = Setting::first();

        $reminder = match ($setting->satuan_reminder) {
            'hari'   => $setting->batas_reminder,
            'minggu' => $setting->batas_reminder * 7,
            'bulan'  => $setting->batas_reminder * 30,
            'tahun'  => $setting->batas_reminder * 365,
            default  => $setting->batas_reminder,
        };

        foreach ($kontraks->getCollection() as $k) {
            $perjanjian   = Carbon::parse($k->perjanjian_pembayaran)->startOfDay();
            $k->sisaHari  = (int) now()->startOfDay()->diffInDays($perjanjian, false);
            $k->isExpired = $k->sisaHari < 0;
            $k->isSoon    = !$k->isExpired && $k->sisaHari <= $reminder;
            $k->showReminder = !in_array($k->status, [
                'completed', 'approved', 'rejected', 'active', 'expired', 'selesai-belum lunas',
            ]);
        }

        return view('admin.kontrak.index', compact('kontraks', 'penawarans', 'reminder', 'setting'));
    }

    /* ─────────────────────────────────────────────
       GET PENAWARAN DETAIL (AJAX)
    ───────────────────────────────────────────── */
    public function getPenawaranDetail($id)
    {
        $penawaran = InvPenawaran::with('items.kendaraan')->findOrFail($id);

        $items = $penawaran->items->map(function ($item) {
            return [
                'id'            => $item->id,
                'kendaraan_id'  => $item->kendaraan_id,
                'qty'           => $item->qty,
                'price'         => $item->price,
                'durasi'        => $item->durasi,
                'satuan_durasi' => $item->satuan_durasi,
                'kendaraan'     => $item->kendaraan ? [
                    'id'             => $item->kendaraan->id,
                    'merk'           => $item->kendaraan->merk,
                    'nopol'          => $item->kendaraan->nopol,
                    'warna'          => $item->kendaraan->warna,
                    'tahun_pembuatan'=> $item->kendaraan->tahun_pembuatan,
                    'status_kendaraan'=> $item->kendaraan->status_kendaraan,
                ] : null,
            ];
        });

        return response()->json([
            'id'            => $penawaran->id,
            'no_penawaran'  => $penawaran->no_penawaran,
            'kepada'        => $penawaran->kepada,
            'customer_name' => $penawaran->customer_name,
            'contact_person'=> $penawaran->contact_person,
            'alamat'        => $penawaran->alamat,
            'total'         => $penawaran->total,
            'periode'       => $penawaran->periode,
            'items'         => $items,
        ]);
    }

    /* ─────────────────────────────────────────────
       STORE  —  status = pending, generate draft PDF
    ───────────────────────────────────────────── */
    public function store(Request $request)
    {
        $prefix = 'KTR-' . now()->format('Ym');
        $last   = InvKontrak::where('no_kontrak', 'like', $prefix . '-%')
            ->orderByRaw('CAST(RIGHT(no_kontrak,4) AS UNSIGNED) DESC')
            ->first();
        $nextNumber = $last ? (int) substr($last->no_kontrak, -4) + 1 : 1;
        $no_kontrak = $prefix . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $request->validate([
            'penawaran_id'          => 'required|exists:inv_penawarans,id',
            'tanggal_kontrak'       => 'required|date',
            'perjanjian_pembayaran' => 'nullable|date',
            'pihak_pertama'         => 'required|string|max:255',
            'contact_pertama'       => 'nullable|string|max:255',
            'pihak_kedua'           => 'required|string|max:255',
            'contact_kedua'         => 'nullable|string|max:255',
        ]);

        // Hitung durasi & tanggal selesai
        $penawaran   = InvPenawaran::with('items.kendaraan')->findOrFail($request->penawaran_id);
        $firstItem   = $penawaran->items->first();
        $durasiValue = (int) ($firstItem->durasi ?? $penawaran->periode ?? 1);
        $durasiSat   = strtolower(trim($firstItem->satuan_durasi ?? 'bulan'));
        if (!in_array($durasiSat, ['hari', 'bulan', 'tahun'])) $durasiSat = 'bulan';

        $mulai = Carbon::parse($request->tanggal_kontrak);
        $selesai = match ($durasiSat) {
            'hari'  => $mulai->copy()->addDays($durasiValue),
            'tahun' => $mulai->copy()->addYears($durasiValue),
            default => $mulai->copy()->addMonths($durasiValue),
        };

        $data = [
            'penawaran_id'          => $request->penawaran_id,
            'no_kontrak'            => $no_kontrak,
            'tanggal_kontrak'       => $request->tanggal_kontrak,
            'perjanjian_pembayaran' => $request->perjanjian_pembayaran,
            'durasi_value'          => $durasiValue,
            'durasi_satuan'         => $durasiSat,
            'tanggal_selesai'       => $selesai->toDateString(),
            'pihak_pertama'         => $request->pihak_pertama,
            'contact_pertama'       => $request->contact_pertama,
            'pihak_kedua'           => $request->pihak_kedua,
            'contact_kedua'         => $request->contact_kedua,
            'status'                => 'pending',
        ];

        $kontrak = InvKontrak::create($data);

        // Generate draft PDF via DomPDF
        $kontrak->load('penawaran.items.kendaraan');
        $setting  = Setting::first();
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        try {
            $pdf      = Pdf::loadView('admin.kontrak.draft_pdf', compact('kontrak', 'setting', 'logoSrc'))
                ->setPaper('a4', 'portrait');
            $filename = 'draft_' . $no_kontrak . '.pdf';
            $savePath = public_path('uploads/kontrak/' . $filename);
            if (!is_dir(public_path('uploads/kontrak'))) {
                mkdir(public_path('uploads/kontrak'), 0755, true);
            }
            $pdf->save($savePath);
            $kontrak->update(['file_draft' => 'uploads/kontrak/' . $filename]);
        } catch (\Exception $e) {
            // PDF generation failed — kontrak tetap tersimpan
        }

        return redirect()->route('kontrak.index')
            ->with('success', 'Kontrak berhasil dibuat! Draft PDF sudah di-generate. Silakan download, tandatangani, lalu upload untuk Approve.');
    }

    /* ─────────────────────────────────────────────
       APPROVE  —  upload file TTD → status approved → buat rental
    ───────────────────────────────────────────── */
    public function approve(Request $request, $id)
    {
        $kontrak = InvKontrak::with('penawaran.items.kendaraan')->findOrFail($id);

        if ($kontrak->status !== 'pending') {
            return back()->with('error', 'Hanya kontrak berstatus pending yang dapat di-approve.');
        }

        $request->validate([
            'file_kontrak' => 'required|file|mimes:pdf|max:10240',
        ]);

        // Simpan file hasil TTD
        $file     = $request->file('file_kontrak');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/kontrak'), $filename);

        DB::transaction(function () use ($kontrak, $filename) {
            $kontrak->update([
                'file_kontrak' => 'uploads/kontrak/' . $filename,
                'status'       => 'approved',
            ]);

            // Auto-create Rental
            $penawaran = $kontrak->penawaran;
            if ($penawaran && $penawaran->items->isNotEmpty()) {
                $validItems = $penawaran->items->filter(fn($i) => !empty($i->kendaraan_id));
                if ($validItems->isNotEmpty()) {
                    $member = Pelanggan::updateOrCreate(
                        ['nama_pelanggan' => $penawaran->customer_name ?? $penawaran->kepada],
                        [
                            'kontak_pelanggan' => $penawaran->contact_person ?? null,
                            'email_pelanggan'  => $penawaran->email_person ?? null,
                            'alamat'           => $penawaran->alamat ?? null,
                            'jenis_pelanggan'  => $penawaran->jenis_pelanggan ?? 'perorangan',
                        ]
                    );

                    foreach ($validItems as $item) {
                        $durasi = (int) $item->durasi;
                        $satuan = strtolower(trim($item->satuan_durasi ?? 'bulan'));
                        if (!in_array($satuan, ['hari', 'bulan', 'tahun'])) $satuan = 'bulan';
                        if ($durasi <= 0) { $durasi = (int) ($penawaran->periode ?? 1); }
                        if ($durasi <= 0) $durasi = 1;

                        $mulai   = Carbon::parse($kontrak->tanggal_kontrak);
                        $selesai = match ($satuan) {
                            'hari'  => $mulai->copy()->addDays($durasi),
                            'tahun' => $mulai->copy()->addYears($durasi),
                            default => $mulai->copy()->addMonths($durasi),
                        };

                        Rental::create([
                            'user_id'              => auth()->id() ?? 1,
                            'kendaraan_id'         => (int) $item->kendaraan_id,
                            'member_id'            => $member->id,
                            'tanggal_mulai'        => $mulai,
                            'tanggal_selesai'      => $selesai,
                            'durasi_hari'          => $satuan === 'hari'  ? $durasi : null,
                            'durasi_bulan'         => $satuan === 'bulan' ? $durasi : null,
                            'durasi_tahun'         => $satuan === 'tahun' ? $durasi : null,
                            'biaya_dasar'          => ($item->qty ?? 1) * ($item->price ?? 0),
                            'biaya_tambahan_total' => 0,
                            'total_biaya'          => ($item->qty ?? 1) * ($item->price ?? 0),
                            'metode_pembayaran'    => 'transfer',
                            'jenis_pembayaran'     => 'lunas',
                            'status_pembayaran'    => 'belum_bayar',
                            'status'               => 'aktif',
                        ]);

                        Kendaraan::where('id', (int) $item->kendaraan_id)
                            ->update(['status_kendaraan' => 'disewa']);
                    }
                }
            }

            // Update status ke active
            $kontrak->update(['status' => 'active']);
        });

        return back()->with('success', 'Kontrak berhasil di-approve dan status menjadi Active. Rental kendaraan otomatis dibuat.');
    }

    /* ─────────────────────────────────────────────
       SELESAI  —  cek pembayaran, update status
    ───────────────────────────────────────────── */
    public function selesai($id)
    {
        $kontrak = InvKontrak::with('penawaran.items.kendaraan')->findOrFail($id);

        if ($kontrak->status !== 'active') {
            return back()->with('error', 'Hanya kontrak berstatus active yang dapat diselesaikan.');
        }

        // Cek semua invoice terkait kontrak ini sudah lunas
        $invoices      = Invoice::where('kontrak_id', $kontrak->id)->get();
        $semuaLunas    = $invoices->isNotEmpty()
            && $invoices->every(fn($inv) => $inv->payment_status === 'lunas');

        DB::transaction(function () use ($kontrak, $semuaLunas) {
            if ($semuaLunas) {
                $kontrak->update(['status' => 'completed']);
                // Kembalikan kendaraan ke tersedia
                if ($kontrak->penawaran) {
                    foreach ($kontrak->penawaran->items->filter(fn($i) => $i->kendaraan_id) as $item) {
                        Kendaraan::where('id', $item->kendaraan_id)
                            ->update(['status_kendaraan' => 'tersedia']);
                    }
                }
            } else {
                $kontrak->update(['status' => 'selesai-belum lunas']);
                // Tandai kendaraan selesai-belum lunas
                if ($kontrak->penawaran) {
                    foreach ($kontrak->penawaran->items->filter(fn($i) => $i->kendaraan_id) as $item) {
                        Kendaraan::where('id', $item->kendaraan_id)
                            ->update(['status_kendaraan' => 'selesai-belum lunas']);
                    }
                }
            }
        });

        $msg = $semuaLunas
            ? 'Kontrak selesai. Semua pembayaran lunas — status: Completed.'
            : 'Kontrak selesai namun masih ada pembayaran belum lunas — status: Selesai-Belum Lunas.';

        return back()->with($semuaLunas ? 'success' : 'warning', $msg);
    }

    /* ─────────────────────────────────────────────
       SHOW / EDIT / UPDATE / DESTROY
    ───────────────────────────────────────────── */
    public function show($id)
    {
        $kontrak = InvKontrak::with('penawaran')->findOrFail($id);
        return view('admin.kontrak.show', compact('kontrak'));
    }

    public function edit($id)
    {
        $kontrak   = InvKontrak::findOrFail($id);
        $penawarans = InvPenawaran::where(function ($q) use ($kontrak) {
            $q->where('status', 'approved')->orWhere('id', $kontrak->penawaran_id);
        })->latest()->get();
        return view('admin.kontrak.edit', compact('kontrak', 'penawarans'));
    }

    public function update(Request $request, $id)
    {
        $kontrak = InvKontrak::findOrFail($id);

        $request->validate([
            'penawaran_id'          => 'required|exists:inv_penawarans,id',
            'no_kontrak'            => 'required|unique:inv_kontraks,no_kontrak,' . $id,
            'tanggal_kontrak'       => 'required|date',
            'perjanjian_pembayaran' => 'nullable|date',
            'pihak_pertama'         => 'required|string|max:255',
            'contact_pertama'       => 'nullable|string|max:255',
            'pihak_kedua'           => 'required|string|max:255',
            'contact_kedua'         => 'nullable|string|max:255',
            'status'                => 'required',
        ]);

        $data = $request->except(['file_kontrak', 'file_persyaratan']);

        if ($request->hasFile('file_kontrak')) {
            $file     = $request->file('file_kontrak');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/kontrak'), $filename);
            $data['file_kontrak'] = 'uploads/kontrak/' . $filename;
        }

        if ($request->hasFile('file_persyaratan')) {
            $file     = $request->file('file_persyaratan');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/kontrak'), $filename);
            $data['file_persyaratan'] = 'uploads/kontrak/' . $filename;
        }

        $kontrak->update($data);

        return redirect()->route('kontrak.index')->with('success', 'Kontrak berhasil diupdate.');
    }

    public function destroy($id)
    {
        InvKontrak::findOrFail($id)->delete();
        return back()->with('success', 'Kontrak berhasil dihapus.');
    }

    /* ─────────────────────────────────────────────
       EXPORT EXCEL
    ───────────────────────────────────────────── */
    public function exportExcel(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\KontrakExport($request->search, $request->status),
            'Kontrak-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    /* ─────────────────────────────────────────────
       EXPORT PDF (LAPORAN TABEL)
    ───────────────────────────────────────────── */
    public function pdf(Request $request)
    {
        $query = InvKontrak::with('penawaran')->latest();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('no_kontrak', 'like', '%' . $request->search . '%')
                  ->orWhere('pihak_pertama', 'like', '%' . $request->search . '%')
                  ->orWhere('pihak_kedua', 'like', '%' . $request->search . '%')
                  ->orWhere('status', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $kontraks = $query->get();
        $setting  = Setting::first();

        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.kontrak.pdf', compact('kontraks', 'setting', 'logoSrc'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('Laporan-Kontrak-' . now()->format('Y-m-d') . '.pdf');
    }
}

