<?php

namespace App\Services;

use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PengeluaranInterceptorService
{
    /**
     * Intercept form submit dan extract data untuk disimpan ke Pembayaran
     *
     * @param Request $request
     * @param string $sourceType (asuransi_kendaraan, pajak, service_part, gps, kir, stnk, service_asuransi)
     * @return array Data yang sudah diformat untuk Pembayaran
     */
    public function intercept(Request $request, string $sourceType): array
    {
        // Extract semua data dari request
        $data = $request->except(['_token', '_method', 'bukti', 'bukti_attachment', 'attachment', 'attachments']);

        // Jika dari Ganti Baru (from_part), inject replace_part_id ke setiap part di source_data
        if ($request->filled('from_part') && $sourceType === 'service_part') {
            $replacePartId = (int) $request->input('from_part');
            if (!empty($data['parts']) && is_array($data['parts'])) {
                foreach ($data['parts'] as &$partData) {
                    $partData['replace_part_id'] = $replacePartId;
                }
                unset($partData);
            }
        }

        // Inject keterangan_limit, kondisi, dan status otomatis untuk service_part
        // karena ketiga field tersebut sudah dihapus dari form dan di-generate server-side
        if ($sourceType === 'service_part' && !empty($data['parts']) && is_array($data['parts'])) {
            $kendaraanId = (int) ($data['kendaraan_id'] ?? 0);
            $kmInput     = (int) ($data['kilometer'] ?? 0);

            // Pre-load limit rules sekali
            $limitRules = \App\Models\ServiceCategoryLimit::where('kendaraan_id', $kendaraanId)
                ->get()
                ->keyBy('category_id');

            // ── Akumulasi biaya per kategori dalam satu batch ────────────────────
            // Karena part-part baru belum tersimpan ke DB saat loop berjalan,
            // kita harus meneruskan biaya item sebelumnya (sekategori) secara manual.
            // Key: category_id (int) → total biaya item-item sebelumnya dalam batch ini.
            $batchBiayaPerCategory = [];

            foreach ($data['parts'] as &$partData) {
                // Status — selalu Proses saat diajukan
                $partData['status'] = 'Proses';

                // Kondisi — Perlu Ganti saat baru diinput
                $partData['kondisi'] = 'Perlu Ganti';

                // ── Inject replace_part_id otomatis berdasarkan kategori + posisi ──────
                // Prioritas: dari hidden input auto (form) → dari from_part → dari deteksi DB
                $autoReplaceId = !empty($partData['replace_part_id_auto'])
                    ? (int) $partData['replace_part_id_auto']
                    : null;

                if ($autoReplaceId && empty($partData['replace_part_id'])) {
                    $partData['replace_part_id'] = $autoReplaceId;
                }

                // Fallback: jika belum ada replace_part_id, cari dari DB berdasarkan kategori+posisi
                if (empty($partData['replace_part_id']) && $kendaraanId) {
                    $categoryId  = $partData['category_id'] ?? null;
                    $posisi      = trim($partData['posisi'] ?? '');
                    $partLamaQuery = \App\Models\ServicePart::where('kendaraan_id', $kendaraanId)
                        ->whereIn('status', ['Terpasang', 'aktif'])
                        ->whereNotNull('kilometer_pasang');

                    if ($categoryId) {
                        $partLamaQuery->where('category_id', $categoryId);
                    } else {
                        $partLamaQuery->whereNull('category_id');
                    }
                    if ($posisi) {
                        $partLamaQuery->whereRaw('LOWER(TRIM(posisi)) = ?', [strtolower($posisi)]);
                    } else {
                        $partLamaQuery->where(fn($q) => $q->whereNull('posisi')->orWhereRaw("TRIM(posisi) = ''"));
                    }

                    $partLama = $partLamaQuery->orderByDesc('tgl_pasang')->first();
                    if ($partLama) {
                        $partData['replace_part_id']  = $partLama->id;
                        $partData['nama_part_lama']   = $partLama->nama_part;
                        $partData['ket_limit_lama']   = $partLama->keterangan_limit ?? '';
                    }
                }

                // Simpan nama part lama untuk keterangan jika replace_part_id sudah ada
                if (!empty($partData['replace_part_id']) && empty($partData['nama_part_lama'])) {
                    $partLamaById = \App\Models\ServicePart::find((int) $partData['replace_part_id']);
                    if ($partLamaById) {
                        $partData['nama_part_lama'] = $partLamaById->nama_part;
                        $partData['ket_limit_lama'] = $partLamaById->keterangan_limit ?? '';
                    }
                }

                // Keterangan limit otomatis
                $categoryId = $partData['category_id'] ?? null;
                $limitRule  = ($categoryId && isset($limitRules[$categoryId]))
                    ? $limitRules[$categoryId]
                    : null;

                // Biaya dari item-item sebelumnya dalam batch ini (sekategori, belum di-DB)
                $extraBiayaFromBatch = ($categoryId !== null)
                    ? (int) ($batchBiayaPerCategory[(int) $categoryId] ?? 0)
                    : 0;

                // Server-side selalu recalculate dengan kumulatif batch yang benar.
                // Nilai dari form (JS) dipakai HANYA sebagai fallback jika server-side gagal,
                // karena JS mungkin tidak memperhitungkan item lain dalam batch (Task 2 memperbaikinya).
                $partData['keterangan_limit'] = $this->buildKeteranganLimitForIntercept(
                    $partData,
                    $kmInput,
                    $limitRule,
                    $data['tanggal_service'] ?? null,
                    $extraBiayaFromBatch
                );

                // Fallback ke nilai form jika server-side menghasilkan '-' atau kosong
                $ketFromForm = trim($partData['keterangan_limit_from_form'] ?? $partData['keterangan_limit'] ?? '');
                if (($partData['keterangan_limit'] === '-' || empty($partData['keterangan_limit']))
                    && $ketFromForm && $ketFromForm !== '-') {
                    $partData['keterangan_limit'] = $ketFromForm;
                }

                // Akumulasikan biaya item ini ke batch counter (untuk item berikutnya sekategori)
                if ($categoryId !== null) {
                    $catIdInt = (int) $categoryId;
                    $batchBiayaPerCategory[$catIdInt] = ($batchBiayaPerCategory[$catIdInt] ?? 0) + (int) ($partData['biaya'] ?? 0);
                }

                // Inject limit_snapshot: nilai aktual service vs nilai limit per dimensi
                // Digunakan untuk kolom perbandingan Service vs Limit di view PO
                $tglPasangSnap      = \Carbon\Carbon::parse($partData['tgl_pasang'] ?? now());
                $intervalNilaiSnap  = (int) ($partData['interval_nilai'] ?? 0);
                $intervalSatuanSnap = $partData['interval_satuan'] ?? 'bulan';
                $tglLimitSnap = match ($intervalSatuanSnap) {
                    'hari'   => (clone $tglPasangSnap)->addDays($intervalNilaiSnap),
                    'minggu' => (clone $tglPasangSnap)->addWeeks($intervalNilaiSnap),
                    'tahun'  => (clone $tglPasangSnap)->addYears($intervalNilaiSnap),
                    default  => (clone $tglPasangSnap)->addMonths($intervalNilaiSnap),
                };
                $kmPasangSnap = (int) ($partData['kilometer_pasang'] ?? $kmInput);
                // ── Jumlah pasang snapshot ───────────────────────────────────────
                $snapLimitJumlah = ($limitRule && $limitRule->jumlah) ? (int) $limitRule->jumlah : null;
                $snapAktifCount  = null;
                if ($snapLimitJumlah && $limitRule->kendaraan_id) {
                    $qSnap = \App\Models\ServicePart::where('kendaraan_id', $limitRule->kendaraan_id)
                        ->where('category_id', $limitRule->category_id)
                        ->whereIn('status', ['Terpasang', 'Limit'])
                        ->where(fn($qs) => $qs->whereNull('persetujuan')
                                              ->orWhereNotIn('persetujuan', ['Ditolak Pembayaran']));
                    // Jika ada reset_at, hanya hitung part setelah reset
                    if ($limitRule->reset_at) {
                        $qSnap->where('created_at', '>=', $limitRule->reset_at);
                    }
                    $snapAktifCount = $qSnap->count();
                }
                $snapJumlahLewat = $snapLimitJumlah !== null && $snapAktifCount !== null && $snapAktifCount > $snapLimitJumlah;
                $snapJumlahSama  = $snapLimitJumlah !== null && $snapAktifCount !== null && $snapAktifCount === $snapLimitJumlah;
                $snapSisaPasang  = ($snapLimitJumlah !== null && $snapAktifCount !== null) ? ($snapLimitJumlah - $snapAktifCount) : null;

                // ── Biaya kumulatif snapshot ─────────────────────────────────────
                // Hitung kumulatif (total periode + biaya baru + biaya item sebelumnya dalam batch)
                // untuk biaya_lewat/biaya_sama agar konsisten dengan keterangan_limit.
                $snapBiaya          = (int) ($partData['biaya'] ?? 0);
                $snapBiayaKumulatif = $snapBiaya + $extraBiayaFromBatch; // fallback: biaya + batch sebelumnya
                $kumulatifDataSnap  = null; // inisialisasi agar selalu terdefinisi
                if ($limitRule && $limitRule->limit_price && $limitRule->limit_nilai && $limitRule->kendaraan_id) {
                    $controller       = app(\App\Http\Controllers\Admin\ServiceHistoryController::class);
                    $kumulatifDataSnap = $controller->getKumulatifBiayaKategori(
                        (int) $limitRule->kendaraan_id,
                        (int) $limitRule->category_id,
                        (int) $limitRule->limit_nilai,
                        $limitRule->limit_satuan ?? 'bulan',
                        (int) $limitRule->limit_price,
                        $tglPasangSnap->toDateString(),
                        $limitRule->reset_at?->toDateString()
                    );
                    // Jika null tapi ada reset_at → periode baru, total = 0
                    if ($kumulatifDataSnap === null && $limitRule->reset_at) {
                        $kumulatifDataSnap = ['total_dalam_periode' => 0, 'sisa_limit' => (int) $limitRule->limit_price];
                    }
                    if ($kumulatifDataSnap !== null) {
                        // total dari DB + biaya item ini + biaya item sebelumnya dalam batch
                        $snapBiayaKumulatif = $kumulatifDataSnap['total_dalam_periode'] + $snapBiaya + $extraBiayaFromBatch;
                    }
                }
                $snapBiayaLewat = $limitRule && $limitRule->limit_price
                    ? ($snapBiayaKumulatif > (int) $limitRule->limit_price)
                    : false;
                $snapBiayaSama = $limitRule && $limitRule->limit_price
                    ? ($snapBiayaKumulatif === (int) $limitRule->limit_price)
                    : false;

                // ── Sisa limit biaya (limit - total_periode_dari_DB - biaya_batch_sebelumnya - biaya_item_ini) ──
                // Merefleksikan sisa limit SETELAH item ini (dan item sebelumnya dalam batch) ditambahkan.
                $snapLimitPrice = $limitRule ? ((int) ($limitRule->limit_price ?? 0) ?: null) : null;
                $snapTotalDalamPeriode = $kumulatifDataSnap['total_dalam_periode'] ?? null;
                $snapSisaLimitBiaya = ($snapLimitPrice !== null && $snapTotalDalamPeriode !== null)
                    ? ($snapLimitPrice - $snapTotalDalamPeriode - $extraBiayaFromBatch - $snapBiaya)
                    : null;

                // ── Sisa limit KM (target_km - km_pasang_saat_ini) ──────────────
                $snapLimitKm = ($limitRule && $limitRule->limit_km) ? (int) $limitRule->limit_km : null;
                $snapSisaLimitKm = ($snapLimitKm !== null)
                    ? ($snapLimitKm - $kmPasangSnap)
                    : null;

                $partData['limit_snapshot'] = [
                    // Biaya
                    'service_biaya'     => $snapBiaya,
                    'limit_biaya'       => $snapLimitPrice,
                    'sisa_limit_biaya'  => $snapSisaLimitBiaya,
                    // Tanggal pasang vs interval limit
                    'service_tanggal'      => $tglPasangSnap->format('d M Y'),
                    'tgl_limit_interval'   => ($intervalNilaiSnap > 0) ? $tglLimitSnap->format('Y-m-d') : null,
                    'limit_interval_label' => ($intervalNilaiSnap > 0)
                                                ? $intervalNilaiSnap . ' ' . ucfirst($intervalSatuanSnap)
                                                : null,
                    // KM pasang vs KM limit (murni dari rule, tanpa ditambah km_pasang)
                    'service_km'      => $kmPasangSnap,
                    'limit_km_target' => $snapLimitKm,
                    'sisa_limit_km'   => $snapSisaLimitKm,
                    // Jumlah pasang vs limit jumlah
                    'limit_jumlah'   => $snapLimitJumlah,
                    'aktif_count'    => $snapAktifCount,
                    'sisa_pasang'    => $snapSisaPasang,
                    // Flag lewat atau tidak per dimensi (untuk pewarnaan merah)
                    // biaya_lewat & biaya_sama menggunakan kumulatif agar konsisten dengan keterangan_limit
                    'biaya_lewat'    => $snapBiayaLewat,
                    'biaya_sama'     => $snapBiayaSama,
                    'tanggal_lewat'  => ($intervalNilaiSnap > 0)
                                        ? $tglLimitSnap->lt(\Carbon\Carbon::parse($data['tanggal_service'] ?? now())->startOfDay())
                                        : false,
                    'tanggal_sama'   => ($intervalNilaiSnap > 0)
                                        ? $tglLimitSnap->eq(\Carbon\Carbon::parse($data['tanggal_service'] ?? now())->startOfDay())
                                        : false,
                    'km_lewat'       => ($limitRule && $limitRule->limit_km)
                                        ? ($kmInput > (int) $limitRule->limit_km)
                                        : false,
                    'km_sama'        => ($limitRule && $limitRule->limit_km)
                                        ? ($kmInput === (int) $limitRule->limit_km)
                                        : false,
                    'jumlah_lewat'   => $snapJumlahLewat,
                    'jumlah_sama'    => $snapJumlahSama,
                ];
            }
            unset($partData);
        }

        // Get user info
        $user = Auth::user();

        // Mapping role → departemen (konsisten dengan PembayaranController)
        $deptMap = [
            'keuangan'   => 'Keuangan',
            'produksi'   => 'Produksi',
            'hrd'        => 'HRD',
            'purchase'   => 'Purchase',
            'sales'      => 'Sales',
            'marketing'  => 'Marketing',
            'it'         => 'IT',
            'operasi'    => 'Operasi',
            'superadmin' => 'Superadmin',
        ];

        // Inject pemohon & departemen ke dalam source_data
        // supaya tersimpan di PO dan bisa dibaca saat approve → buat Pembayaran
        $data['pemohon']    = $user->name ?? $user->email;
        $data['departemen'] = $deptMap[$user->role ?? ''] ?? $user->departemen ?? '-';

        // Format data berdasarkan source type
        $formattedData = [
            'source_type' => $sourceType,
            'source_data' => $data,
            'departemen' => $deptMap[$user->role ?? ''] ?? $user->departemen ?? '-',
            'pemohon' => $user->name ?? $user->email,
            'alasan_permintaan' => $this->getAlasanPermintaan($sourceType, $data),
            'nominal' => $this->extractNominal($sourceType, $data),
            'nama_bank' => $request->input('nama_bank'),
            'no_rekening' => $request->input('no_rekening'),
            'nama_rekening' => $request->input('nama_rekening'),
            'informasi' => $request->input('informasi') ?? $request->input('keterangan'),
        ];
        
        return $formattedData;
    }

    /**
     * Generate keterangan_limit otomatis untuk source_data PO/Pembayaran.
     * Mirror logika ServiceHistoryController::generateKeteranganLimit():
     * selalu tampilkan status + angka sisa untuk semua dimensi yang dikonfigurasi.
     *
     * Catatan: dipanggil saat part BELUM tersimpan ke DB, jadi biaya kumulatif
     * = total_dalam_periode (dari DB) + extraBiayaFromBatch (item sebelumnya di batch ini) + biaya baru ini.
     *
     * @param int $extraBiayaFromBatch  Total biaya item-item sebelumnya dalam batch ini (sekategori, belum di-DB)
     */
    private function buildKeteranganLimitForIntercept(array $partData, int $kmInput, ?\App\Models\ServiceCategoryLimit $limitRule, ?string $tanggalServis = null, int $extraBiayaFromBatch = 0): string
    {
        if (!$limitRule) {
            return '-';
        }

        $biaya          = (int) ($partData['biaya'] ?? 0);
        $tglPasang      = \Carbon\Carbon::parse($partData['tgl_pasang'] ?? now());
        $intervalNilai  = (int) ($partData['interval_nilai'] ?? 0);
        $intervalSatuan = $partData['interval_satuan'] ?? 'bulan';

        // Hitung tanggal limit (tgl_pasang + interval)
        $tglLimit = match ($intervalSatuan) {
            'hari'   => (clone $tglPasang)->addDays($intervalNilai),
            'minggu' => (clone $tglPasang)->addWeeks($intervalNilai),
            'tahun'  => (clone $tglPasang)->addYears($intervalNilai),
            default  => (clone $tglPasang)->addMonths($intervalNilai),
        };
        $refTanggal = \Carbon\Carbon::parse($tanggalServis ?? now())->startOfDay();

        $hargaLimit  = $limitRule->limit_price;
        $kmLimit     = $limitRule->limit_km;
        $intervalAda = $intervalNilai > 0;

        // ── Biaya kumulatif per periode ──────────────────────────────────────
        // Part belum tersimpan ke DB → tambahkan $biaya + $extraBiayaFromBatch ke total periode yang ada
        $biayaKumulatif = $biaya + $extraBiayaFromBatch;
        $sisaBiaya      = $hargaLimit ? ((int)$hargaLimit - $biayaKumulatif) : null;
        if ($hargaLimit && $limitRule->limit_nilai && $limitRule->kendaraan_id) {
            $controller    = app(\App\Http\Controllers\Admin\ServiceHistoryController::class);
            $kumulatifData = $controller->getKumulatifBiayaKategori(
                (int) $limitRule->kendaraan_id,
                (int) $limitRule->category_id,
                (int) $limitRule->limit_nilai,
                $limitRule->limit_satuan ?? 'bulan',
                (int) $hargaLimit,
                $tglPasang->toDateString(),
                $limitRule->reset_at?->toDateString()
            );
            // Jika null dan ada reset_at → periode baru, total = 0
            if ($kumulatifData === null && $limitRule->reset_at) {
                $kumulatifData = ['total_dalam_periode' => 0, 'sisa_limit' => (int) $hargaLimit];
            }
            if ($kumulatifData !== null) {
                // DB total + biaya item sebelumnya dalam batch + biaya item ini
                $biayaKumulatif = $kumulatifData['total_dalam_periode'] + $extraBiayaFromBatch + $biaya;
                $sisaBiaya      = (int)$hargaLimit - $biayaKumulatif; // bisa negatif
            }
        }

        $biayaLewat = $hargaLimit && $biayaKumulatif > $hargaLimit;
        $biayaSama  = $hargaLimit && $biayaKumulatif === $hargaLimit;
        $biayaAman  = !$hargaLimit || $biayaKumulatif < $hargaLimit;

        $waktuLewat = $intervalAda && $tglLimit->lt($refTanggal);
        $waktuSama  = $intervalAda && $tglLimit->eq($refTanggal);
        $waktuAman  = !$intervalAda || $tglLimit->gt($refTanggal);

        $kmAda   = $kmLimit && $kmLimit > 0;
        $sisaKm  = $kmAda ? ((int)$kmLimit - $kmInput) : null; // bisa negatif
        $kmSama  = $kmAda && $kmInput === (int)$kmLimit;
        $kmLewat = $kmAda && $kmInput  >  (int)$kmLimit;
        $kmAman  = !$kmAda || $kmInput  <  (int)$kmLimit;

        // ── Dimensi Jumlah pasang ─────────────────────────────────────────────
        $limitJumlah = $limitRule->jumlah ? (int) $limitRule->jumlah : null;
        $aktifCount  = null;
        if ($limitJumlah && $limitRule->kendaraan_id) {
            $qAktif = \App\Models\ServicePart::where('kendaraan_id', $limitRule->kendaraan_id)
                ->where('category_id', $limitRule->category_id)
                ->where(function ($qa) {
                    $qa->whereIn('status', ['Terpasang', 'Limit', 'aktif'])
                       ->orWhere(function ($qa2) {
                           $qa2->where('status', 'tidak_aktif')
                               ->where(fn($qa3) => $qa3->whereNull('persetujuan')
                                                       ->orWhereNotIn('persetujuan', ['Ditolak Pembayaran']));
                       });
                });
            if ($limitRule->reset_at) {
                $qAktif->where('created_at', '>=', $limitRule->reset_at);
            }
            $aktifCount = $qAktif->count();
        }
        $jumlahAda   = $limitJumlah !== null && $limitJumlah > 0 && $aktifCount !== null;
        $jumlahSama  = $jumlahAda && $aktifCount === $limitJumlah;
        $jumlahLewat = $jumlahAda && $aktifCount  >  $limitJumlah;
        $jumlahAman  = !$jumlahAda || $aktifCount  <  $limitJumlah;
        $sisaPasang  = $jumlahAda ? ($limitJumlah - $aktifCount) : null; // bisa negatif

        // Tidak ada limit dikonfigurasi → "-"
        $adaLimit = $hargaLimit || $intervalAda || $kmAda || $jumlahAda;
        if (!$adaLimit) {
            return '-';
        }

        // ── Bangun keterangan: 1 baris status per dimensi + sisa ────────────
        $kalimat = [];

        // KM — status + sisa KM jika belum lewat
        if ($kmAda) {
            if ($kmSama)       $kalimat[] = 'Sudah mencapai batas limit KM';
            elseif ($kmLewat)  $kalimat[] = 'Sudah melebihi batas limit KM';
            else               $kalimat[] = 'Belum mencapai batas limit KM (sisa ' . number_format($sisaKm, 0, ',', '.') . ' km)';
        }

        // Jangka waktu — status + sisa waktu jika belum lewat
        if ($intervalAda) {
            if ($waktuSama)       $kalimat[] = 'Sudah mencapai batas limit jangka waktu';
            elseif ($waktuLewat)  $kalimat[] = 'Sudah melebihi batas limit jangka waktu';
            else {
                $sisaHari  = max(0, (int) $refTanggal->diffInDays($tglLimit, false));
                $kalimat[] = 'Belum mencapai limit jangka waktu (' . $this->formatSisaWaktu($sisaHari) . ')';
            }
        }

        // Biaya — status + sisa biaya jika belum lewat
        if ($hargaLimit) {
            if ($biayaSama)       $kalimat[] = 'Sudah mencapai batas limit biaya';
            elseif ($biayaLewat)  $kalimat[] = 'Sudah melebihi limit biaya';
            else                  $kalimat[] = 'Belum mencapai limit biaya (sisa Rp ' . number_format($sisaBiaya, 0, ',', '.') . ')';
        }

        // Jumlah pasang — status + sisa pcs
        if ($jumlahAda) {
            if ($jumlahSama)       $kalimat[] = 'Sudah mencapai batas pemasangan part';
            elseif ($jumlahLewat)  $kalimat[] = 'Sudah melebihi batas pemasangan part';
            else                   $kalimat[] = 'Belum mencapai batas pemasangan part (sisa ' . $sisaPasang . ' pcs)';
        }

        if (empty($kalimat)) {
            return '-';
        }

        $result = implode(', ', $kalimat);

        // Tambahkan info "Menggantikan [nama_part lama] yang sudah limit X" jika ada
        $namaPartLama = $partData['nama_part_lama'] ?? null;
        if ($namaPartLama) {
            $ketLama = strtolower($partData['ket_limit_lama'] ?? '');
            if (str_contains($ketLama, 'melebihi batas limit km') || str_contains($ketLama, 'sudah mencapai batas limit km')) {
                $alasanGanti = 'sudah limit KM';
            } elseif (str_contains($ketLama, 'melebihi batas limit jangka waktu') || str_contains($ketLama, 'sudah mencapai batas limit jangka waktu')) {
                $alasanGanti = 'sudah limit waktu';
            } elseif (str_contains($ketLama, 'sudah melebihi limit biaya') || str_contains($ketLama, 'sudah mencapai batas limit biaya')) {
                $alasanGanti = 'sudah melebihi limit biaya';
            } else {
                $alasanGanti = 'sudah limit';
            }
            $result .= '. Menggantikan ' . $namaPartLama . ' yang ' . $alasanGanti;
        }

        return $result;
    }

    /**
     * Save data ke Pembayaran table
     *
     * @param array $data Data hasil intercept
     * @param string $sourceType
     * @return Pembayaran
     */
    public function saveToPembayaran(array $data, string $sourceType): Pembayaran
    {
        DB::beginTransaction();
        
        try {
            $pembayaran = Pembayaran::create([
                'no_pr' => $this->generateNoPR($sourceType),
                'tanggal' => now(),
                'departemen' => $data['departemen'],
                'tipe_pembayaran' => 'service', // All pengeluaran (vehicle expenses) use 'service' type
                'pemohon' => $data['pemohon'],
                'alasan_permintaan' => $data['alasan_permintaan'],
                'nominal' => $data['nominal'],
                'nama_bank' => $data['nama_bank'] ?? null,
                'no_rekening' => $data['no_rekening'] ?? null,
                'nama_rekening' => $data['nama_rekening'] ?? null,
                'informasi' => $data['informasi'] ?? null,
                'status' => 'Diajukan',        // Langsung Diajukan, tidak perlu klik Ajukan lagi
                'terakhir_diajukan' => now(),
                'source_type' => $sourceType,
                'source_data' => $data['source_data'],
                'target_id' => null,
                'can_edit' => false, // Tidak bisa edit setelah submit
            ]);
            
            DB::commit();
            
            return $pembayaran;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Generate No PR unik untuk pengeluaran
     *
     * @param string $sourceType
     * @return string
     */
    public function generateNoPR(string $sourceType): string
    {
        // Format: PR-PENGELUARAN-{TYPE}-{INCREMENT}
        $typeMap = [
            'asuransi_kendaraan'             => 'ASR',
            'asuransi_kendaraan_perpanjang'  => 'APP',
            'pajak'                          => 'PJK',
            'pajak_perpanjang'               => 'PJP',
            'service_part'                   => 'SVC',
            'service_incident'               => 'SIN',
            'gps'                            => 'GPS',
            'gps_perpanjang'                 => 'GPP',
            'kir'                            => 'KIR',
            'kir_perpanjang'                 => 'KRP',
            'stnk'                           => 'STN',
            'service_asuransi'               => 'SAS',
            'purchase_order'                 => 'PO',
        ];
        
        $typeCode = $typeMap[$sourceType] ?? 'PGL';
        
        // Get last PR for this type
        $lastPR = Pembayaran::where('source_type', $sourceType)
            ->where('no_pr', 'like', "PR-{$typeCode}-%")
            ->orderBy('id', 'desc')
            ->first();
        
        $increment = 1;
        
        if ($lastPR) {
            // Extract increment from last PR
            preg_match('/PR-' . $typeCode . '-(\d+)/', $lastPR->no_pr, $matches);
            if (isset($matches[1])) {
                $increment = intval($matches[1]) + 1;
            }
        }
        
        return sprintf('PR-%s-%03d', $typeCode, $increment);
    }

    /**
     * Get alasan permintaan berdasarkan source type
     *
     * @param string $sourceType
     * @param array $data
     * @return string
     */
    protected function getAlasanPermintaan(string $sourceType, array $data): string
    {
        return match($sourceType) {
            'asuransi_kendaraan'            => 'Pembayaran Asuransi Kendaraan - ' . ($data['keterangan'] ?? 'N/A'),
            'asuransi_kendaraan_perpanjang' => 'Perpanjangan Asuransi Kendaraan',
            'pajak'                         => 'Pembayaran Pajak Kendaraan - ' . ($data['jenis_pajak'] ?? 'N/A'),
            'pajak_perpanjang'              => 'Perpanjangan Pajak Kendaraan',
            'service_part'                  => 'Pembelian Service Part - ' . (
                isset($data['parts']) && is_array($data['parts'])
                    ? collect($data['parts'])->pluck('nama_part')->filter()->take(3)->implode(', ')
                    : ($data['nama_part'] ?? 'N/A')
            ),
            'gps'                           => 'Pembayaran GPS Kendaraan - ' . ($data['keterangan'] ?? 'N/A'),
            'gps_perpanjang'                => 'Perpanjangan GPS Kendaraan',
            'kir'                           => 'Pembayaran KIR Kendaraan',
            'kir_perpanjang'                => 'Perpanjangan KIR Kendaraan',
            'stnk'                          => 'Pembayaran STNK Kendaraan',
            'service_asuransi'              => 'Klaim Asuransi Service - ' . ($data['keterangan'] ?? 'N/A'),
            'purchase_order'                => 'Purchase Order - ' . ($data['vendor'] ?? 'N/A'),
            default => 'Pengeluaran Kendaraan',
        };
    }

    /**
     * Extract nominal dari data berdasarkan source type
     *
     * @param string $sourceType
     * @param array $data
     * @return float
     */
    protected function extractNominal(string $sourceType, array $data): float
    {
        return match($sourceType) {
            'asuransi_kendaraan',
            'asuransi_kendaraan_perpanjang' => floatval($data['biaya'] ?? $data['premi'] ?? 0),
            'pajak',
            'pajak_perpanjang'              => floatval($data['nominal'] ?? 0),
            'service_part'                  => floatval(
                // Jika ada total_biaya_override, pakai itu; jika tidak, sum dari semua parts
                ($data['total_biaya_override'] ?? 0) > 0
                    ? $data['total_biaya_override']
                    : collect($data['parts'] ?? [])->sum(fn($p) => floatval($p['biaya'] ?? 0))
            ),
            'gps'                           => collect($data['gps_items'] ?? [])->sum(fn($item) => floatval($item['biaya_sewa'] ?? 0)),
            'gps_perpanjang'                => collect($data['gps_items'] ?? [])->sum(fn($item) => floatval($item['biaya_sewa'] ?? 0)),
            'kir',
            'kir_perpanjang'                => floatval($data['biaya'] ?? 0),
            'stnk'                          => floatval($data['biaya'] ?? 0),
            'service_asuransi'              => floatval($data['biaya'] ?? 0),
            'purchase_order'                => floatval($data['total_harga'] ?? 0),
            default => 0,
        };
    }

    /**
     * Resubmit rejected pembayaran dengan data baru
     * Update existing pembayaran record, reset status to Pending, update source_data
     *
     * @param int $pembayaranId
     * @param Request $request
     * @param string $sourceType
     * @return Pembayaran
     */
    public function resubmitToPembayaran(int $pembayaranId, Request $request, string $sourceType): Pembayaran
    {
        DB::beginTransaction();
        
        try {
            $pembayaran = Pembayaran::findOrFail($pembayaranId);
            
            // Validation: Only rejected pengeluaran can be resubmitted
            if ($pembayaran->status !== 'Ditolak' && !($pembayaran->status === 'Disetujui' && !empty(collect($pembayaran->source_data['item_decisions'] ?? [])->where('action', 'rejected')->all()))) {
                throw new \Exception('Hanya pengajuan yang ditolak yang dapat diajukan ulang.');
            }
            
            if (!$pembayaran->can_edit) {
                throw new \Exception('Pengajuan ini tidak dapat diedit.');
            }
            
            // Intercept new data
            $interceptedData = $this->intercept($request, $sourceType);

            // Upload new temp files (jika ada)
            $newUploadedFiles = $this->uploadTemporaryFiles($request, $pembayaranId);

            // Ambil temp_files lama
            $oldTempFiles = $pembayaran->source_data['temp_files'] ?? [];

            // Merge: per part, pakai baru kalau ada; pertahankan lama kalau tidak
            $mergedTempFiles = $this->mergeTemporaryFiles($oldTempFiles, $newUploadedFiles, $interceptedData['source_data']['parts'] ?? []);

            // Hapus file lama HANYA yang sudah diganti
            $this->deleteReplacedTempFiles($oldTempFiles, $newUploadedFiles);

            // Update pembayaran
            $pembayaran->update([
                'source_data' => array_merge($interceptedData['source_data'], ['temp_files' => $mergedTempFiles]),
                'alasan_permintaan' => $interceptedData['alasan_permintaan'],
                'nominal' => $interceptedData['nominal'],
                'nama_bank' => $interceptedData['nama_bank'] ?? null,
                'no_rekening' => $interceptedData['no_rekening'] ?? null,
                'nama_rekening' => $interceptedData['nama_rekening'] ?? null,
                'informasi' => $interceptedData['informasi'] ?? null,
                'status' => 'Diajukan',
                'terakhir_diajukan' => now(),
                'can_edit' => false,
            ]);
            
            DB::commit();
            
            return $pembayaran;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Resubmit rejected Purchase Order dengan data baru
     * Update existing PO record, reset status to Pending, update source_data
     *
     * @param int $poId
     * @param Request $request
     * @param string $sourceType
     * @return \App\Models\PurchaseOrder
     */
    public function resubmitToPurchaseOrder(int $poId, Request $request, string $sourceType): \App\Models\PurchaseOrder
    {
        DB::beginTransaction();
        
        try {
            $po = \App\Models\PurchaseOrder::findOrFail($poId);
            
            // Validation: Only rejected PO can be resubmitted.
            // Termasuk partial approval: status 'Disetujui' tapi ada item_decisions dengan action=rejected.
            if (!$po->isRejected()) {
                throw new \Exception('Hanya Purchase Order yang ditolak yang dapat diajukan ulang.');
            }

            // can_edit wajib true, KECUALI untuk partial approval (status Disetujui + ada rejected items).
            // Data lama mungkin belum memiliki can_edit=true saat partial approval terjadi.
            $isPartialApproval = $po->status === 'Disetujui';
            if (!$isPartialApproval && !$po->can_edit) {
                throw new \Exception('Purchase Order ini tidak dapat diedit.');
            }
            
            // Intercept new data
            $interceptedData = $this->intercept($request, $sourceType);

            // Upload new temp files (jika ada)
            $newUploadedFiles = $this->uploadTemporaryFiles($request, $poId, 'purchase_order');

            // Ambil temp_files lama dari PO yang ditolak
            $oldTempFiles = $po->source_data['temp_files'] ?? [];

            // Merge: per part, kalau user upload baru → pakai baru; kalau tidak → pakai lama
            $mergedTempFiles = $this->mergeTemporaryFiles($oldTempFiles, $newUploadedFiles, $interceptedData['source_data']['parts'] ?? []);

            // Hapus file lama HANYA yang sudah diganti dengan file baru
            $this->deleteReplacedTempFiles($oldTempFiles, $newUploadedFiles, 'purchase_order');

            // Extract vendor and total items
            $vendor = $interceptedData['source_data']['vendor'] ?? 'Vendor ' . ucfirst(str_replace('_', ' ', $sourceType));

            // ── Partial approval: gabungkan parts lama (approved) dengan parts baru (resubmit) ──
            // Saat PO partial (status=Disetujui + ada rejected), form hanya mengirim
            // part yang ditolak. Kita perlu mempertahankan part yang sudah approved di source_data.
            $oldSourceData   = is_array($po->source_data) ? $po->source_data : [];
            $oldItemDecisions = $oldSourceData['item_decisions'] ?? [];
            $newSourceData   = $interceptedData['source_data'];

            if (!empty($oldItemDecisions)) {
                // Bangun map index → decision dari item_decisions lama
                $oldDecMap = collect($oldItemDecisions)->keyBy('idx');

                // Parts lama di PO (sebelum filter approved)
                $oldAllParts = $oldSourceData['parts'] ?? [];

                // Parts baru dari form (hanya yang ditolak, sudah di-filter controller)
                $newParts = $newSourceData['parts'] ?? [];

                // Index part yang rejected di PO lama
                $rejectedIdx = collect($oldItemDecisions)
                    ->where('action', 'rejected')
                    ->pluck('idx')
                    ->values()
                    ->toArray();

                // Index part yang sudah approved — akan di-lock agar tidak bisa diapprove ulang
                $approvedIdx = collect($oldItemDecisions)
                    ->where('action', 'approved')
                    ->pluck('idx')
                    ->values()
                    ->toArray();

                // Gabung: mulai dari semua parts lama, replace yang rejected dengan versi baru dari form
                $mergedParts = $oldAllParts; // copy semua (approved + rejected lama)
                $newPartCursor = 0;
                foreach ($rejectedIdx as $oldIdx) {
                    if (isset($newParts[$newPartCursor])) {
                        $mergedParts[$oldIdx] = $newParts[$newPartCursor];
                        $newPartCursor++;
                    }
                }

                $newSourceData['parts'] = array_values($mergedParts);

                // Simpan daftar index yang sudah locked approved agar approveItems() bisa guard-nya.
                // item_decisions di-reset supaya approver bisa putuskan ulang HANYA untuk part baru.
                // Part approved tetap terlindungi via locked_approved_idx.
                $newSourceData['locked_approved_idx'] = array_values($approvedIdx);
                unset($newSourceData['item_decisions']);
            }

            $totalItems = $this->extractTotalItems($sourceType, $newSourceData);

            // Update PO
            $po->update([
                'source_data' => array_merge($newSourceData, ['temp_files' => $mergedTempFiles]),
                'vendor' => $vendor,
                'total_barang' => $totalItems,
                'total_harga' => (int) $interceptedData['nominal'],
                'catatan' => $interceptedData['informasi'] ?? $interceptedData['source_data']['keterangan'] ?? null,
                'keterangan' => $interceptedData['source_data']['keterangan'] ?? null,
                'status' => 'Pending',
                'can_edit' => false,
                'terakhir_diajukan' => now(),
            ]);
            
            DB::commit();
            
            return $po;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Save data ke PurchaseOrder table (untuk tambah baru yang lewat PO)
     *
     * @param array $data Data hasil intercept
     * @param string $sourceType
     * @return \App\Models\PurchaseOrder
     */
    public function saveToPurchaseOrder(array $data, string $sourceType): \App\Models\PurchaseOrder
    {
        DB::beginTransaction();
        
        try {
            // Extract vendor (opsional, default jika kosong)
            $vendor = $data['source_data']['vendor'] ?? 'Vendor ' . ucfirst(str_replace('_', ' ', $sourceType));
            
            // Extract total items count
            $totalItems = $this->extractTotalItems($sourceType, $data['source_data']);
            
            // Create Purchase Order
            $po = \App\Models\PurchaseOrder::create([
                'tanggal_po'        => now()->toDateString(),
                'vendor'            => $vendor,
                'pemohon'           => $data['source_data']['pemohon'] ?? $data['pemohon'] ?? null,
                'departemen'        => $data['source_data']['departemen'] ?? $data['departemen'] ?? null,
                'terkait_rfq'       => $data['source_data']['terkait_rfq'] ?? null,
                'total_barang'      => $totalItems,
                'total_harga'       => (int) $data['nominal'],
                'status_po'         => 'Pending',
                'catatan'           => $data['informasi'] ?? $data['source_data']['keterangan'] ?? null,
                'keterangan'        => $data['source_data']['keterangan'] ?? null,
                'source_type'       => $sourceType,
                'source_data'       => $data['source_data'],
                'status'            => 'Pending',
                'can_edit'          => false,
                'terakhir_diajukan' => now(),
            ]);
            
            DB::commit();
            
            return $po;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Extract total items count dari data berdasarkan source type
     */
    protected function extractTotalItems(string $sourceType, array $data): int
    {
        return match($sourceType) {
            'gps'              => count($data['gps_items'] ?? []),
            'service_part'     => count($data['parts'] ?? []),
            'service_incident' => count($data['parts'] ?? []),
            'service_asuransi' => count($data['kejadians'] ?? []),
            default => 1,
        };
    }

    /**
     * Upload temporary files untuk pengeluaran yang menunggu approval
     * Support both Pembayaran and PurchaseOrder
     *
     * @param Request $request
     * @param int $entityId
     * @param string $entityType 'pembayaran' or 'purchase_order'
     * @return array Array of file metadata
     */
    public function uploadTemporaryFiles(Request $request, int $entityId, string $entityType = 'pembayaran'): array
    {
        $uploadedFiles = [];
        $timestamp = time();
        
        // Directory untuk temp files
        $tempDir = "{$entityType}/temp/{$entityId}";
        
        // Upload bukti files
        if ($request->hasFile('bukti')) {
            $files = is_array($request->file('bukti')) 
                ? $request->file('bukti') 
                : [$request->file('bukti')];
            
            foreach ($files as $index => $file) {
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $storedName = "{$timestamp}_{$index}_{$originalName}";
                
                $path = $file->storeAs($tempDir . '/bukti', $storedName, 'public');
                
                $uploadedFiles['bukti'][] = [
                    'original_name' => $originalName,
                    'stored_name' => $storedName,
                    'path' => $path,
                    'full_path' => storage_path('app/public/' . $path),
                    'size' => $file->getSize(),
                    'extension' => $extension,
                ];
            }
        }
        
        // Upload attachment files (opsional)
        $attachmentFields = ['bukti_attachment', 'attachment', 'attachments', 'lampiran'];
        
        foreach ($attachmentFields as $field) {
            if ($request->hasFile($field)) {
                $files = is_array($request->file($field)) 
                    ? $request->file($field) 
                    : [$request->file($field)];
                
                foreach ($files as $index => $file) {
                    if (!$file->isValid()) continue;
                    
                    $originalName = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $storedName = "{$timestamp}_{$index}_{$originalName}";
                    
                    $path = $file->storeAs($tempDir . '/attachments', $storedName, 'public');
                    
                    $uploadedFiles['attachments'][] = [
                        'original_name' => $originalName,
                        'stored_name' => $storedName,
                        'path' => $path,
                        'full_path' => storage_path('app/public/' . $path),
                        'size' => $file->getSize(),
                        'extension' => $extension,
                    ];
                }
            }
        }
        
        // Upload parts per-item bukti (service_part multi-item form)
        $partFiles = $request->file('parts');
        if (is_array($partFiles)) {
            foreach ($partFiles as $idx => $part) {
                if (empty($part['bukti']) || !is_array($part['bukti'])) continue;
                foreach ($part['bukti'] as $bi => $buktiFile) {
                    if (!$buktiFile || !$buktiFile->isValid()) continue;
                    $originalName = $buktiFile->getClientOriginalName();
                    $extension    = $buktiFile->getClientOriginalExtension();
                    $storedName   = "{$timestamp}_{$idx}_{$bi}_{$originalName}";
                    $path = $buktiFile->storeAs($tempDir . '/parts/' . $idx . '/bukti', $storedName, 'public');
                    $uploadedFiles['parts'][$idx]['bukti'][] = [
                        'original_name' => $originalName,
                        'stored_name'   => $storedName,
                        'path'          => $path,
                        'full_path'     => storage_path('app/public/' . $path),
                        'size'          => $buktiFile->getSize(),
                        'extension'     => $extension,
                    ];
                }
            }
        }

        // Upload gps_items per-item lampiran (GPS multi-item form)
        $gpsItemFiles = $request->file('gps_items');
        if (is_array($gpsItemFiles)) {
            foreach ($gpsItemFiles as $idx => $gpsItem) {
                // Lampiran per item GPS
                if (!empty($gpsItem['lampiran']) && is_array($gpsItem['lampiran'])) {
                    foreach ($gpsItem['lampiran'] as $li => $lampiranFile) {
                        if ($lampiranFile && $lampiranFile->isValid()) {
                            $originalName = $lampiranFile->getClientOriginalName();
                            $extension    = $lampiranFile->getClientOriginalExtension();
                            $storedName   = "{$timestamp}_{$idx}_{$li}_{$originalName}";

                            $path = $lampiranFile->storeAs($tempDir . '/gps_items/' . $idx . '/lampiran', $storedName, 'public');

                            $uploadedFiles['gps_items'][$idx]['lampiran'][] = [
                                'original_name' => $originalName,
                                'stored_name'   => $storedName,
                                'path'          => $path,
                                'full_path'     => storage_path('app/public/' . $path),
                                'size'          => $lampiranFile->getSize(),
                                'extension'     => $extension,
                            ];
                        }
                    }
                }
            }
        }
        
        return $uploadedFiles;
    }

    /**
     * Handle perpanjang flow - create pembayaran for renewal approval
     * 
     * @param Request $request
     * @param string $sourceType  Base source type (pajak, asuransi_kendaraan, kir, gps)
     * @param mixed $existingRecord
     * @return Pembayaran
     */
    public function perpanjangViaPembayaran(Request $request, string $sourceType, $existingRecord): Pembayaran
    {
        // Map base source_type ke perpanjang source_type
        $perpanjangTypeMap = [
            'pajak'              => 'pajak_perpanjang',
            'asuransi_kendaraan' => 'asuransi_kendaraan_perpanjang',
            'kir'                => 'kir_perpanjang',
            'gps'                => 'gps_perpanjang',
        ];
        $perpanjangSourceType = $perpanjangTypeMap[$sourceType] ?? $sourceType;

        DB::beginTransaction();
        
        try {
            // Build perpanjang data
            $perpanjangData = $request->all();
            $perpanjangData['is_perpanjang'] = true;
            $perpanjangData['existing_record_id'] = $existingRecord->id;
            
            // Add existing record info for context
            if (property_exists($existingRecord, 'kendaraan_id')) {
                $perpanjangData['kendaraan_id'] = $existingRecord->kendaraan_id;
            }
            if (method_exists($existingRecord, 'kendaraan') && $existingRecord->kendaraan) {
                $perpanjangData['kendaraan_nopol'] = $existingRecord->kendaraan->nopol ?? '-';
            }
            
            // Create fake request for intercept
            $fakeRequest = new Request($perpanjangData);
            $fakeRequest->merge($request->all());
            
            // Intercept data (menggunakan perpanjang source_type)
            $interceptedData = $this->intercept($fakeRequest, $perpanjangSourceType);
            
            // Alasan permintaan sudah di-set oleh getAlasanPermintaan untuk perpanjang types
            
            // Save to Pembayaran dengan perpanjang source_type
            $pembayaran = $this->saveToPembayaran($interceptedData, $perpanjangSourceType);
            
            // Upload files (file image/bukti dari form perpanjangan)
            $uploadedFiles = $this->uploadTemporaryFiles($fakeRequest, $pembayaran->id);
            
            // Sertakan lampiran lama dari GPS record ke source_data
            // supaya tampil di modal approval pembayaran
            $gpsItems = $interceptedData['source_data']['gps_items'] ?? [];
            if (!empty($gpsItems)) {
                foreach ($gpsItems as $idx => $item) {
                    $gpsKendaraanId = $item['gps_kendaraan_id'] ?? null;
                    if (!$gpsKendaraanId) continue;
                    $lampiranLama = \App\Models\Attachment::where('relation_type', 'gps')
                        ->where('relation_id', $gpsKendaraanId)
                        ->get()
                        ->map(fn($a) => [
                            'original_name' => $a->file_name,
                            'path'          => $a->file_path,
                            'size'          => $a->file_size,
                            'extension'     => $a->file_type,
                        ])
                        ->toArray();
                    if (!empty($lampiranLama)) {
                        $uploadedFiles['gps_items'][$idx]['lampiran'] = array_merge(
                            $uploadedFiles['gps_items'][$idx]['lampiran'] ?? [],
                            $lampiranLama
                        );
                    }
                }
            }

            // Update source_data
            $sourceData = $pembayaran->source_data;
            $sourceData['temp_files'] = $uploadedFiles;
            $pembayaran->update(['source_data' => $sourceData]);
            
            DB::commit();
            
            return $pembayaran;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Get human-readable source type name
     */
    private function getSourceTypeName(string $sourceType): string
    {
        return match($sourceType) {
            'gps'                            => 'GPS Kendaraan',
            'gps_perpanjang'                 => 'Perpanjangan GPS',
            'asuransi_kendaraan'             => 'Asuransi Kendaraan',
            'asuransi_kendaraan_perpanjang'  => 'Perpanjangan Asuransi Kendaraan',
            'pajak'                          => 'Pajak Kendaraan',
            'pajak_perpanjang'               => 'Perpanjangan Pajak Kendaraan',
            'kir'                            => 'KIR',
            'kir_perpanjang'                 => 'Perpanjangan KIR',
            'stnk'                           => 'STNK',
            'service_asuransi'               => 'Service Asuransi',
            'service_part'                   => 'Service Part',
            'purchase_order'                 => 'Purchase Order',
            default => ucfirst(str_replace('_', ' ', $sourceType)),
        };
    }

    /**
     * Delete temporary files untuk pembayaran yang dibatalkan
     *
     * @param int $pembayaranId
     * @return bool
     */
    public function deleteTemporaryFiles(int $entityId, string $entityType = 'pembayaran'): bool
    {
        $tempDir = match($entityType) {
            'purchase_order' => "purchase_order/temp/{$entityId}",
            default => "pembayaran/temp/{$entityId}",
        };
        
        try {
            Storage::disk('public')->deleteDirectory($tempDir);
            return true;
        } catch (\Exception $e) {
            \Log::error("Failed to delete temp files for {$entityType} {$entityId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Merge temp files lama dengan yang baru.
     * - Level bukti/attachments: kalau ada baru → pakai baru, kalau tidak → pakai lama
     * - Level parts[idx][bukti]: per-part, kalau user upload baru → pakai baru, kalau tidak → pakai lama
     */
    private function mergeTemporaryFiles(array $oldFiles, array $newFiles, array $parts = []): array
    {
        $merged = $oldFiles; // mulai dari lama

        // Override bukti & attachments kalau ada yang baru
        if (!empty($newFiles['bukti'])) {
            $merged['bukti'] = $newFiles['bukti'];
        }
        if (!empty($newFiles['attachments'])) {
            $merged['attachments'] = $newFiles['attachments'];
        }

        // Per-part bukti: override per index kalau ada file baru untuk index itu
        if (!empty($newFiles['parts'])) {
            foreach ($newFiles['parts'] as $idx => $partFiles) {
                if (!empty($partFiles['bukti'])) {
                    // User upload file baru untuk part ini → ganti
                    $merged['parts'][$idx]['bukti'] = $partFiles['bukti'];
                }
                // Kalau tidak ada file baru untuk part ini, data lama ($merged['parts'][$idx]) tetap dipertahankan
            }
        }

        // GPS items
        if (!empty($newFiles['gps_items'])) {
            foreach ($newFiles['gps_items'] as $idx => $gpsFiles) {
                if (!empty($gpsFiles['lampiran'])) {
                    $merged['gps_items'][$idx]['lampiran'] = $gpsFiles['lampiran'];
                }
            }
        }

        return $merged;
    }

    /**
     * Hapus hanya file lama yang sudah diganti dengan file baru.
     * File lama yang tidak diganti TIDAK dihapus.
     */
    private function deleteReplacedTempFiles(array $oldFiles, array $newFiles, string $entityType = 'pembayaran'): void
    {
        // Hapus bukti lama kalau ada bukti baru
        if (!empty($newFiles['bukti']) && !empty($oldFiles['bukti'])) {
            foreach ($oldFiles['bukti'] as $f) {
                if (!empty($f['path'])) {
                    Storage::disk('public')->delete($f['path']);
                }
            }
        }

        // Hapus attachments lama kalau ada baru
        if (!empty($newFiles['attachments']) && !empty($oldFiles['attachments'])) {
            foreach ($oldFiles['attachments'] as $f) {
                if (!empty($f['path'])) {
                    Storage::disk('public')->delete($f['path']);
                }
            }
        }

        // Hapus per-part bukti lama kalau ada yang baru per index
        if (!empty($newFiles['parts'])) {
            foreach ($newFiles['parts'] as $idx => $partNew) {
                if (!empty($partNew['bukti']) && !empty($oldFiles['parts'][$idx]['bukti'])) {
                    foreach ($oldFiles['parts'][$idx]['bukti'] as $f) {
                        if (!empty($f['path'])) {
                            Storage::disk('public')->delete($f['path']);
                        }
                    }
                }
            }
        }
    }

    /**
     * Format sisa hari menjadi string yang mudah dibaca.
     * ≤ 30 hari         → "sisa X hari"
     * ≤ 365 hari        → "sisa X bulan Y hari"
     * > 365 hari        → "sisa X tahun Y bulan"
     */
    private function formatSisaWaktu(int $sisaHari): string
    {
        if ($sisaHari <= 0) return 'sisa 0 hari';

        if ($sisaHari <= 30) {
            return 'sisa ' . $sisaHari . ' hari';
        }

        if ($sisaHari < 360) {
            $bulan    = (int) floor($sisaHari / 30);
            $hariSisa = $sisaHari - ($bulan * 30);
            $str      = 'sisa ' . $bulan . ' bulan';
            if ($hariSisa > 0) $str .= ' ' . $hariSisa . ' hari';
            return $str;
        }

        // >= 360 hari → tampilkan dalam tahun
        $tahun            = (int) floor($sisaHari / 365);
        $sisaSetelahTahun = $sisaHari - ($tahun * 365);
        $bulan            = (int) floor($sisaSetelahTahun / 30);
        $str              = 'sisa ' . $tahun . ' tahun';
        if ($bulan > 0) $str .= ' ' . $bulan . ' bulan';
        return $str;
    }
}
