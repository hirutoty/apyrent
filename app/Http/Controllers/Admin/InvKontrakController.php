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
use App\Helpers\KontrakHelper;
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
        // Cache auto-expire check for 10 minutes to avoid running on every page load
        $cacheKey = 'kontrak_auto_expire_check';
        \Cache::remember($cacheKey, 600, function () {
            InvKontrak::whereNotIn('status', ['expired', 'completed', 'terminated', 'rejected', 'selesai-belum lunas'])
                ->whereNotNull('perjanjian_pembayaran')
                ->get()
                ->each(function ($k) {
                    $batas = Carbon::parse($k->perjanjian_pembayaran)->startOfDay();
                    if (now()->startOfDay()->gt($batas)) {
                        $k->update(['status' => 'expired']);
                    }
                });
            return true;
        });

        // Optimize with selective eager loading - only load what's needed for table display
        $query = InvKontrak::with([
                'penawaran:id,no_penawaran,customer_name,total',
                'penawaran.items:id,penawaran_id,kendaraan_id,qty',
                'penawaran.items.kendaraan:id,merk,nopol,status_kendaraan'
            ])
            ->latest();

        // Apply search filter
        if (request('search')) {
            $query->where(function ($q) {
                $search = request('search');
                $q->where('no_kontrak', 'like', '%' . $search . '%')
                  ->orWhere('pihak_pertama', 'like', '%' . $search . '%')
                  ->orWhere('pihak_kedua', 'like', '%' . $search . '%')
                  ->orWhere('status', 'like', '%' . $search . '%');
            });
        }

        // Apply status filter
        if (request('status')) {
            $query->where('status', request('status'));
        }

        $kontraks = $query->paginate(15)->withQueryString();

        // Limit penawaran query to only what's needed for dropdown (first 100 latest)
        $penawarans = InvPenawaran::whereIn('status', ['approved', 'active'])
            ->select('id', 'no_penawaran', 'customer_name', 'total')
            ->latest()
            ->limit(100)
            ->get();
            
        $setting    = Setting::first();

        $reminder = match ($setting->satuan_reminder) {
            'hari'   => $setting->batas_reminder,
            'minggu' => $setting->batas_reminder * 7,
            'bulan'  => $setting->batas_reminder * 30,
            'tahun'  => $setting->batas_reminder * 365,
            default  => $setting->batas_reminder,
        };

        $resolvedKetentuan = [];

        foreach ($kontraks->getCollection() as $k) {
            $perjanjian   = Carbon::parse($k->perjanjian_pembayaran)->startOfDay();
            $k->sisaHari  = (int) now()->startOfDay()->diffInDays($perjanjian, false);
            $k->isExpired = $k->sisaHari < 0;
            $k->isSoon    = !$k->isExpired && $k->sisaHari <= $reminder;
            $k->showReminder = !in_array($k->status, [
                'completed', 'approved', 'rejected', 'active', 'expired', 'selesai-belum lunas',
            ]);

            // Resolve semua placeholder server-side untuk textarea editor
            $kontrakData     = KontrakHelper::buildKontrakData($k);
            $allReplacements = KontrakHelper::buildReplacements($setting, $kontrakData);

            $rawId = $k->ketentuan_id
                ?? ($k->pasal_ketentuan ? KontrakHelper::pasalToPlainText($k->pasal_ketentuan, 'id') : null)
                ?? KontrakHelper::defaultPlainText('id');
            $rawEn = $k->ketentuan_en
                ?? ($k->pasal_ketentuan ? KontrakHelper::pasalToPlainText($k->pasal_ketentuan, 'en') : null)
                ?? KontrakHelper::defaultPlainText('en');

            // Kirim teks RAW (dengan placeholder) ke textarea editor — jangan resolve di sini.
            // Placeholder di-resolve hanya saat render print/pdf.
            $resolvedKetentuan[$k->id] = [
                'id' => $rawId,
                'en' => $rawEn,
            ];
        }

        // Build default ketentuan text — raw (tanpa resolve), untuk modal Create
        $defaultKetentuan = [
            'id' => KontrakHelper::defaultPlainText('id'),
            'en' => KontrakHelper::defaultPlainText('en'),
        ];

        return view('admin.kontrak.index', compact('kontraks', 'penawarans', 'reminder', 'setting', 'defaultKetentuan', 'resolvedKetentuan'));
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
            'id'             => $penawaran->id,
            'no_penawaran'   => $penawaran->no_penawaran,
            'kepada'         => $penawaran->kepada,
            'customer_name'  => $penawaran->customer_name,
            'contact_person' => $penawaran->contact_person,
            'email_person'   => $penawaran->email_person,
            'no_ktp'         => $penawaran->no_ktp,
            'jenis_pelanggan'=> $penawaran->jenis_pelanggan,
            'alamat'         => $penawaran->alamat,
            'total'          => $penawaran->total,
            'periode'        => $penawaran->periode,
            'items'          => $items,
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
            'jenis_pelanggan'       => 'nullable|string|in:perorangan,perusahaan',
            'perwakilan_pihak_kedua'=> 'nullable|string|max:255',
            'jabatan_pihak_kedua'   => 'nullable|string|max:100',
            'ketentuan_id'          => 'nullable|string',
            'ketentuan_en'          => 'nullable|string',
        ]);

        // ── DEBUG: Log request data untuk field perwakilan ──
        \Log::info('Kontrak Store - Perwakilan Data:', [
            'jenis_pelanggan'        => $request->jenis_pelanggan,
            'perwakilan_pihak_kedua' => $request->perwakilan_pihak_kedua,
            'jabatan_pihak_kedua'    => $request->jabatan_pihak_kedua,
        ]);

        // Hitung durasi & tanggal selesai — pakai durasi terpanjang dari semua item
        $penawaran = InvPenawaran::with('items.kendaraan')->findOrFail($request->penawaran_id);

        $toDays = function (int $val, string $sat): int {
            return match ($sat) {
                'tahun' => $val * 365,
                'bulan' => $val * 30,
                default => $val,
            };
        };

        $maxDays     = 0;
        $durasiValue = (int) ($penawaran->periode ?? 1);
        $durasiSat   = 'bulan';

        foreach ($penawaran->items as $item) {
            $val = (int) ($item->durasi ?? $penawaran->periode ?? 1);
            $sat = strtolower(trim($item->satuan_durasi ?? 'bulan'));
            if (!in_array($sat, ['hari', 'bulan', 'tahun'])) $sat = 'bulan';
            if ($val <= 0) $val = 1;

            $days = $toDays($val, $sat);
            if ($days > $maxDays) {
                $maxDays     = $days;
                $durasiValue = $val;
                $durasiSat   = $sat;
            }
        }

        if ($maxDays === 0) {
            // Fallback: tidak ada item
            $durasiValue = (int) ($penawaran->periode ?? 1);
            $durasiSat   = 'bulan';
        }

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
            'no_ktp_kedua'          => $request->no_ktp_kedua,
            'email_kedua'           => $request->email_kedua,
            'jenis_pelanggan'       => $request->jenis_pelanggan,
            'alamat_kedua'          => $request->alamat_kedua,
            'perwakilan_pihak_kedua'=> $request->perwakilan_pihak_kedua,
            'jabatan_pihak_kedua'   => $request->jabatan_pihak_kedua,
            // customer_name: ambil dari perwakilan jika perusahaan, atau nama pihak kedua jika perorangan
            'customer_name'         => $request->jenis_pelanggan === 'perusahaan'
                ? ($request->perwakilan_pihak_kedua ?? $request->pihak_kedua)
                : $request->pihak_kedua,
            'status'                => 'pending',
        ];

        // Simpan plain text ketentuan; fallback ke teks default jika kosong
        // Ambil setting untuk resolve placeholder {NAMA_PERUSAHAAN}, {PPN}, dll.
        $settingForReplace  = \App\Models\Setting::first();

        // Format tanggal untuk substitusi
        Carbon::setLocale('id');
        $mulaiId   = $mulai->isoFormat('D MMMM YYYY');
        $selesaiId = $selesai->isoFormat('D MMMM YYYY');
        $mulaiEn   = $mulai->locale('en')->isoFormat('D MMMM YYYY');
        $selesaiEn = $selesai->locale('en')->isoFormat('D MMMM YYYY');
        Carbon::setLocale('id');

        $durasiStr = $durasiValue . ' ' . ucfirst($durasiSat);

        // Resolve data pihak kedua (cek pelanggan jika alamat kosong)
        $namaP2   = $request->pihak_kedua ?? '';
        $alamatP2 = $request->alamat_kedua ?? '';
        if (empty($alamatP2)) {
            $pelP2    = \App\Models\Pelanggan::where('nama_pelanggan', $namaP2)->first();
            $alamatP2 = $pelP2?->alamat ?? '';
        }

        $kontrakData = [
            'durasi'              => $durasiStr,
            'tanggal_mulai'       => $mulaiId,
            'tanggal_selesai'     => $selesaiId,
            'tanggal_mulai_en'    => $mulaiEn,
            'tanggal_selesai_en'  => $selesaiEn,
            'nama_pihak_kedua'    => $namaP2,
            'alamat_pihak_kedua'  => $alamatP2,
            'kontak_pihak_kedua'  => $request->contact_kedua ?? '',
            'pihak_pertama'       => $request->pihak_pertama ?? '',
        ];

        $replacements = KontrakHelper::buildReplacements($settingForReplace, $kontrakData);

        // Simpan plain text ketentuan — JANGAN resolve placeholder di sini,
        // biarkan {ATAS_NAMA}, {NAMA_BANK}, dll. tetap sebagai placeholder di DB.
        // Placeholder di-resolve saat render (draft_print / draft_pdf).
        $data['ketentuan_id'] = $request->filled('ketentuan_id')
            ? $request->ketentuan_id
            : KontrakHelper::defaultPlainText('id');
        $data['ketentuan_en'] = $request->filled('ketentuan_en')
            ? $request->ketentuan_en
            : KontrakHelper::defaultPlainText('en');

        // Kosongkan pasal_ketentuan (tidak lagi dipakai untuk editor baru)
        $data['pasal_ketentuan'] = null;

        $kontrak = InvKontrak::create($data);

        // ── DEBUG: Verify data tersimpan di database ──
        \Log::info('Kontrak Created - Saved Data:', [
            'id'                     => $kontrak->id,
            'no_kontrak'             => $kontrak->no_kontrak,
            'jenis_pelanggan'        => $kontrak->jenis_pelanggan,
            'perwakilan_pihak_kedua' => $kontrak->perwakilan_pihak_kedua,
            'jabatan_pihak_kedua'    => $kontrak->jabatan_pihak_kedua,
        ]);

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

        // Clear chart cache
        \Cache::forget('chart_kontrak');

        return redirect()->route('kontrak.index')
            ->with('success', 'Kontrak berhasil dibuat! Draft PDF sudah di-generate. Silakan download, tandatangani, lalu upload untuk Approve.');
    }

    /* ─────────────────────────────────────────────
       APPROVE  —  upload file TTD → status active → buat rental
    ───────────────────────────────────────────── */
    public function approve(Request $request, $id)
    {
        $kontrak = InvKontrak::with('penawaran.items.kendaraan')->findOrFail($id);

        if (!in_array($kontrak->status, ['pending', 'approved'])) {
            return back()->with('error', 'Kontrak dengan status ini tidak dapat di-approve.');
        }

        $request->validate([
            'file_kontrak' => 'required|file|mimes:pdf|max:10240',
            'attachments'  => 'nullable|array',
            'attachments.*'=> 'nullable|file|max:10240',
        ]);

        // Simpan file kontrak TTD
        $file     = $request->file('file_kontrak');
        $filename = time() . '_' . $file->getClientOriginalName();
        if (!is_dir(public_path('uploads/kontrak'))) {
            mkdir(public_path('uploads/kontrak'), 0755, true);
        }
        $file->move(public_path('uploads/kontrak'), $filename);

        // Simpan attachments (multiple files)
        $attachmentData = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $att) {
                if ($att && $att->isValid()) {
                    $attName = time() . '_' . $att->getClientOriginalName();
                    $att->move(public_path('uploads/kontrak/attachments'), $attName);
                    $attachmentData[] = [
                        'name' => $att->getClientOriginalName(),
                        'path' => 'uploads/kontrak/attachments/' . $attName,
                    ];
                }
            }
        }

        DB::transaction(function () use ($kontrak, $filename, $attachmentData) {
            // Gunakan data customer dari kontrak yang sudah disimpan saat create
            $customerName = $kontrak->customer_name
                ?? ($kontrak->jenis_pelanggan === 'perusahaan'
                    ? ($kontrak->perwakilan_pihak_kedua ?? $kontrak->pihak_kedua)
                    : $kontrak->pihak_kedua);

            // Update file & status kontrak
            $updateData = [
                'file_kontrak' => 'uploads/kontrak/' . $filename,
                'status'       => 'approved',
            ];
            if (!empty($attachmentData)) {
                $updateData['file_attachments'] = json_encode($attachmentData);
            }
            $kontrak->update($updateData);

            // Auto-create Rental
            $penawaran = $kontrak->penawaran;
            if ($penawaran && $penawaran->items->isNotEmpty()) {
                $validItems = $penawaran->items->filter(fn($i) => !empty($i->kendaraan_id));
                if ($validItems->isNotEmpty()) {
                    $member = Pelanggan::updateOrCreate(
                        ['nama_pelanggan' => $customerName ?? $penawaran->customer_name ?? $penawaran->kepada],
                        [
                            'kontak_pelanggan' => $kontrak->contact_kedua     ?? $penawaran->contact_person ?? null,
                            'email_pelanggan'  => $kontrak->email_kedua       ?? $penawaran->email_person   ?? null,
                            'alamat'           => $kontrak->alamat_kedua      ?? $penawaran->alamat         ?? null,
                            'jenis_pelanggan'  => $kontrak->jenis_pelanggan   ?? $penawaran->jenis_pelanggan ?? 'perorangan',
                            'no_ktp'           => $kontrak->no_ktp_kedua      ?? null,
                        ]
                    );

                    $mulai = $kontrak->perjanjian_pembayaran
                        ? Carbon::parse($kontrak->perjanjian_pembayaran)->addDay()
                        : Carbon::parse($kontrak->tanggal_kontrak);

                    foreach ($validItems as $item) {
                        $durasi = (int) $item->durasi;
                        $satuan = strtolower(trim($item->satuan_durasi ?? 'bulan'));
                        if (!in_array($satuan, ['hari', 'bulan', 'tahun'])) $satuan = 'bulan';
                        if ($durasi <= 0) { $durasi = (int) ($penawaran->periode ?? 1); }
                        if ($durasi <= 0) $durasi = 1;

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

            $kontrak->update(['status' => 'active']);
        });

        // Clear chart cache
        \Cache::forget('chart_kontrak');

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

        // Clear chart cache
        \Cache::forget('chart_kontrak');

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
            'ketentuan_id'          => 'nullable|string',
            'ketentuan_en'          => 'nullable|string',
        ]);

        // Exclude status — status tidak boleh diubah dari form edit biasa
        $data = $request->except(['file_kontrak', 'file_persyaratan', 'ketentuan', 'pasal', 'status']);

        // Resolve placeholder {NAMA_PERUSAHAAN}, {PPN}, dll. sebelum simpan ke DB
        $settingForReplace  = Setting::first();

        // Format tanggal dari request atau fallback ke data kontrak lama
        Carbon::setLocale('id');
        $tglMulai   = Carbon::parse($request->tanggal_kontrak ?? $kontrak->tanggal_kontrak);
        $tglSelesai = Carbon::parse($kontrak->tanggal_selesai);
        $mulaiId    = $tglMulai->isoFormat('D MMMM YYYY');
        $selesaiId  = $tglSelesai->isoFormat('D MMMM YYYY');
        $mulaiEn    = $tglMulai->locale('en')->isoFormat('D MMMM YYYY');
        $selesaiEn  = $tglSelesai->locale('en')->isoFormat('D MMMM YYYY');
        Carbon::setLocale('id');

        $durasiVal = $kontrak->durasi_value  ?? '';
        $durasiSat = $kontrak->durasi_satuan ? ucfirst($kontrak->durasi_satuan) : '';
        $durasiStr = ($durasiVal && $durasiSat) ? "$durasiVal $durasiSat" : '';

        $namaP2   = $request->pihak_kedua  ?? $kontrak->pihak_kedua  ?? '';
        $alamatP2 = $request->alamat_kedua ?? $kontrak->alamat_kedua ?? '';
        if (empty($alamatP2)) {
            $pelP2    = \App\Models\Pelanggan::where('nama_pelanggan', $namaP2)->first();
            $alamatP2 = $pelP2?->alamat ?? '';
        }

        $kontrakData = [
            'durasi'             => $durasiStr,
            'tanggal_mulai'      => $mulaiId,
            'tanggal_selesai'    => $selesaiId,
            'tanggal_mulai_en'   => $mulaiEn,
            'tanggal_selesai_en' => $selesaiEn,
            'nama_pihak_kedua'   => $namaP2,
            'alamat_pihak_kedua' => $alamatP2,
            'kontak_pihak_kedua' => $request->contact_kedua ?? $kontrak->contact_kedua ?? '',
            'pihak_pertama'      => $request->pihak_pertama ?? $kontrak->pihak_pertama ?? '',
        ];

        $replacements = KontrakHelper::buildReplacements($settingForReplace, $kontrakData);

        // Simpan plain text ketentuan — HANYA jika user membuka Tab 2 (ketentuan_edited=1).
        // Kalau tidak, biarkan ketentuan di DB tidak berubah.
        if ($request->input('ketentuan_edited') === '1') {
            $data['ketentuan_id'] = $request->filled('ketentuan_id')
                ? $request->ketentuan_id
                : ($kontrak->ketentuan_id ?? KontrakHelper::defaultPlainText('id'));
            $data['ketentuan_en'] = $request->filled('ketentuan_en')
                ? $request->ketentuan_en
                : ($kontrak->ketentuan_en ?? KontrakHelper::defaultPlainText('en'));
        } else {
            // Tab 2 tidak dibuka — jangan sentuh ketentuan di DB sama sekali
            unset($data['ketentuan_id']);
            unset($data['ketentuan_en']);
        }

        // Tidak lagi menggunakan pasal_ketentuan JSON untuk editor baru
        // (pasal_ketentuan tetap ada di DB untuk backward compat, tidak ditimpa)

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

        // Regenerate draft PDF dengan ketentuan terbaru
        $kontrak->refresh()->load('penawaran.items.kendaraan');
        $setting  = Setting::first();
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }
        try {
            $pdfFilename = 'draft_' . $kontrak->no_kontrak . '.pdf';
            $savePath    = public_path('uploads/kontrak/' . $pdfFilename);
            if (!is_dir(public_path('uploads/kontrak'))) {
                mkdir(public_path('uploads/kontrak'), 0755, true);
            }
            Pdf::loadView('admin.kontrak.draft_pdf', compact('kontrak', 'setting', 'logoSrc'))
                ->setPaper('a4', 'portrait')
                ->save($savePath);
            $kontrak->update(['file_draft' => 'uploads/kontrak/' . $pdfFilename]);
        } catch (\Exception $e) {
            // PDF regeneration gagal — update tetap berhasil
        }

        // Clear chart cache
        \Cache::forget('chart_kontrak');

        return redirect()->route('kontrak.index')->with('success', 'Kontrak berhasil diupdate.');
    }

    public function destroy($id)
    {
        InvKontrak::findOrFail($id)->delete();

        // Clear chart cache
        \Cache::forget('chart_kontrak');

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
       DRAFT PRINT — halaman HTML untuk Ctrl+P
    ───────────────────────────────────────────── */
    public function draftPrint($id)
    {
        $kontrak = InvKontrak::with('penawaran.items.kendaraan')->findOrFail($id);
        $setting = Setting::first();
        return view('admin.kontrak.draft_print', compact('kontrak', 'setting'));
    }

    /* ─────────────────────────────────────────────
       REGENERATE DRAFT PDF
    ───────────────────────────────────────────── */
    public function regenerateDraft($id)
    {
        $kontrak = InvKontrak::with('penawaran.items.kendaraan')->findOrFail($id);
        $setting  = Setting::first();
        $logoPath = $setting?->logo ? public_path($setting->logo) : public_path('images/icon.png');
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $mime    = mime_content_type($logoPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdfFilename = 'draft_' . $kontrak->no_kontrak . '.pdf';
        $savePath    = public_path('uploads/kontrak/' . $pdfFilename);

        if (!is_dir(public_path('uploads/kontrak'))) {
            mkdir(public_path('uploads/kontrak'), 0755, true);
        }

        // Hapus file lama jika ada
        if (file_exists($savePath)) {
            unlink($savePath);
        }

        $pdf = Pdf::loadView('admin.kontrak.draft_pdf', compact('kontrak', 'setting', 'logoSrc'))
            ->setPaper('a4', 'portrait');
        $pdf->save($savePath);
        $kontrak->update(['file_draft' => 'uploads/kontrak/' . $pdfFilename]);

        return response()->download($savePath, $pdfFilename, [
            'Content-Type' => 'application/pdf',
        ]);
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

    /* ─────────────────────────────────────────────
       HELPER — Proses array pasal dari form request
       Mengembalikan array pasal yang sudah disanitasi,
       atau default dari KontrakHelper jika kosong.
    ───────────────────────────────────────────── */
    private static function processPasalFromRequest(Request $request): array
    {
        $rawPasal = $request->input('pasal', []);
        $result   = [];

        // Inject \n antara "PASAL N" dan nama topik jika belum ada
        $normalizeJudul = function(string $judul): string {
            if ($judul === '' || str_contains($judul, "\n")) return $judul;
            // "PASAL 2MASA SEWA" → "PASAL 2\nMASA SEWA"
            // "ARTICLE 2RENTAL PERIOD" → "ARTICLE 2\nRENTAL PERIOD"
            return preg_replace(
                '/^((?:PASAL|ARTICLE)\s+\d+)\s*([A-Z].*)$/si',
                "$1\n$2",
                $judul
            ) ?? $judul;
        };

        foreach ($rawPasal as $pasal) {
            $judulId = $normalizeJudul(trim($pasal['judul_id'] ?? ''));
            $judulEn = $normalizeJudul(trim($pasal['judul_en'] ?? ''));
            $tipe    = in_array($pasal['tipe'] ?? '', ['paragraf', 'list', 'sublist'])
                       ? $pasal['tipe']
                       : 'list';

            $poin = [];
            foreach ($pasal['poin'] ?? [] as $p) {
                $pId = trim($p['id'] ?? '');
                $pEn = trim($p['en'] ?? '');
                if ($pId !== '' || $pEn !== '') {
                    $poin[] = ['id' => $pId, 'en' => $pEn];
                }
            }

            // Skip pasal yang judulnya kosong & tidak punya poin
            if ($judulId === '' && $judulEn === '' && empty($poin)) {
                continue;
            }

            $result[] = [
                'judul_id' => $judulId,
                'judul_en' => $judulEn,
                'tipe'     => $tipe,
                'poin'     => $poin,
            ];
        }

        // Jika form tidak mengirim pasal sama sekali, pakai default
        if (empty($result)) {
            return KontrakHelper::defaultPasalKetentuan();
        }

        return $result;
    }
}

