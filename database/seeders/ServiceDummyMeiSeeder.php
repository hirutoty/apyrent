<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\ServiceHistory;
use App\Models\ServiceDetail;
use App\Models\ServiceAsuransi;
use App\Models\ServicePart;
use App\Models\Kendaraan;
use App\Models\JenisAsuransi;

class ServiceDummyMeiSeeder extends Seeder
{
    /**
     * Seed data dummy service bulan Mei 2026.
     * Total biaya seluruh service_history = Rp 30.000.000
     * Dibagi merata ke 50 record → rata-rata Rp 600.000/record
     */
    public function run(): void
    {
        $kendaraanIds = Kendaraan::pluck('id')->toArray();

        if (empty($kendaraanIds)) {
            $this->command->warn('Tidak ada data kendaraan. Seeder dibatalkan.');
            return;
        }

        $jenisAsuransiIds = JenisAsuransi::pluck('id')->toArray();
        if (empty($jenisAsuransiIds)) {
            $jenisAsuransiIds = [null];
        }

        // ── Distribusi biaya: total tepat 30.000.000 tersebar ke 50 record ──
        $totalTarget    = 30_000_000;
        $jumlahRecord   = 50;
        $biayaPerRecord = $this->distribusiBiaya($totalTarget, $jumlahRecord);

        $keluhanList = [
            'Oli mesin sudah hitam dan kotor',
            'Rem belakang berbunyi saat diinjak',
            'AC tidak dingin, freon habis',
            'Ban depan aus dan retak',
            'Mesin getar berlebihan saat idle',
            'Suara kasar dari mesin',
            'Lampu indikator check engine menyala',
            'Transmisi slip saat ganti gigi',
            'Power steering berat dan berbunyi',
            'Aki drop, mesin susah dihidupkan',
            'Filter udara sangat kotor',
            'Kampas kopling tipis',
            'Knalpot bocor dan berisik',
            'Suspensi depan keras dan berbunyi',
            'Wiper tidak berfungsi optimal',
            'Kipas radiator tidak berputar',
            'Timing belt mendekati batas pemakaian',
            'Busi sudah aus dan perlu diganti',
            'Coolant habis dan mesin overheat',
            'Sistem rem ABS bermasalah',
            'Ganti oli mesin rutin',
            'Servis berkala 10.000 km',
            'Servis berkala 20.000 km',
            'Tune up lengkap',
            'Cek dan servis rem',
        ];

        // Kategori: AC, Ban, Bodi, Elektrikal, Kaki-kaki, Lainnya, Mesin, Transmisi
        $kategoris = \App\Models\ServiceCategory::pluck('id', 'nama')->toArray();
        $catId = fn(string $nama) => $kategoris[$nama] ?? $kategoris['Lainnya'] ?? null;

        $partList = [
            ['nama' => 'Oli Mesin Shell 10W-40',    'cat' => 'Mesin'],
            ['nama' => 'Filter Oli',                 'cat' => 'Mesin'],
            ['nama' => 'Filter Udara',               'cat' => 'Mesin'],
            ['nama' => 'Kampas Rem Depan',           'cat' => 'Kaki-kaki'],
            ['nama' => 'Kampas Rem Belakang',        'cat' => 'Kaki-kaki'],
            ['nama' => 'Busi NGK',                   'cat' => 'Mesin'],
            ['nama' => 'Aki GS 60Ah',                'cat' => 'Elektrikal'],
            ['nama' => 'V-Belt',                     'cat' => 'Mesin'],
            ['nama' => 'Timing Belt',                'cat' => 'Mesin'],
            ['nama' => 'Kopling Set',                'cat' => 'Transmisi'],
            ['nama' => 'Shock Absorber Depan',       'cat' => 'Kaki-kaki'],
            ['nama' => 'Ban Bridgestone 195/65 R15', 'cat' => 'Ban'],
            ['nama' => 'Freon AC R134a',             'cat' => 'AC'],
            ['nama' => 'Coolant Radiator',           'cat' => 'Mesin'],
            ['nama' => 'Wiper Blade',                'cat' => 'Elektrikal'],
        ];

        $keteranganPart = [
            'Part baru original', 'Part KW berkualitas baik', 'Penggantian rutin sesuai jadwal',
            'Kondisi lama sudah aus', 'Rekomendasi bengkel resmi', 'Tindak lanjut servis sebelumnya',
        ];

        $namaAsuransi = [
            'Asuransi Jasindo', 'Asuransi Wahana Tata', 'Asuransi MAG',
            'Asuransi Adira', 'Asuransi Sinar Mas',
        ];

        $statusHistory     = ['proses', 'selesai'];
        $statusDetail      = ['Layak', 'Tidak Layak'];
        $statusAsuransi    = ['bermasalah', 'selesai'];
        $kondisiPart       = ['Baik', 'Rusak', 'Perlu Ganti'];
        $intervalSatuan    = ['bulan', 'tahun'];

        $tahun = 2026;
        $bulan = 5;
        $daysInMonth = Carbon::createFromDate($tahun, $bulan, 1)->daysInMonth;

        $totalBiayaInserted = 0;

        for ($i = 1; $i <= $jumlahRecord; $i++) {
            $kendaraanId = $kendaraanIds[array_rand($kendaraanIds)];
            $tanggal     = Carbon::createFromDate($tahun, $bulan, rand(1, $daysInMonth));
            $kilometer   = rand(10000, 150000);
            $totalBiaya  = $biayaPerRecord[$i - 1];
            $totalBiayaInserted += $totalBiaya;

            $maksBulanan  = rand(5, 40) * 500000;
            $biayaTahunan = rand(10, 120) * 500000;

            // ── 1. SERVICE HISTORY ──
            $history = ServiceHistory::create([
                'kendaraan_id'       => $kendaraanId,
                'keluhan'            => $keluhanList[($i - 1) % count($keluhanList)],
                'kilometer'          => $kilometer,
                'total_biaya'        => $totalBiaya,
                'status'             => $statusHistory[$i % 5 === 0 ? 0 : 1],
                'tanggal_service'    => $tanggal->toDateString(),
                'sisa_limit'         => rand(0, 10) * 500000,
                'maks_bulanan'       => $maksBulanan,
                'biaya_tahunan'      => $biayaTahunan,
                'status_pengeluaran' => $totalBiaya > $maksBulanan ? 'overservice' : 'stabil',
                'bukti_pembayaran'   => null,
                'status_approval'    => 'approved',
                'approval_by'        => 1,
                'approval_at'        => $tanggal->copy()->addDay(),
                'is_request'         => false,
            ]);

            // ── 2. SERVICE DETAIL ──
            $jumlahDetail = rand(1, 2);
            for ($d = 0; $d < $jumlahDetail; $d++) {
                ServiceDetail::create([
                    'kendaraan_id'       => $kendaraanId,
                    'service_history_id' => $history->id,
                    'tanggal_service'    => $tanggal->toDateString(),
                    'kilometer'          => $kilometer + ($d * rand(100, 500)),
                    'status'             => $statusDetail[($i + $d) % 2 === 0 ? 1 : 0],
                    'biaya'              => (int) round($totalBiaya / max(1, $jumlahDetail)),
                    'keterangan'         => $keluhanList[($i + $d) % count($keluhanList)],
                    'bukti'              => null,
                    'attachment'         => null,
                ]);
            }

            // ── 3. SERVICE ASURANSI (setiap 3 record) ──
            if ($i % 3 === 0) {
                $periodeMulai   = $tanggal->copy()->startOfMonth();
                $periodeSelesai = $periodeMulai->copy()->addYear()->subDay();
                ServiceAsuransi::create([
                    'kendaraan_id'      => $kendaraanId,
                    'nama_asuransi'     => $namaAsuransi[array_rand($namaAsuransi)],
                    'jenis_asuransi_id' => $jenisAsuransiIds[array_rand($jenisAsuransiIds)],
                    'tanggal_service'   => $tanggal->toDateString(),
                    'periode_mulai'     => $periodeMulai->toDateString(),
                    'periode_selesai'   => $periodeSelesai->toDateString(),
                    'kilometer'         => $kilometer,
                    'biaya'             => rand(5, 50) * 100000,
                    'keterangan'        => 'Perpanjangan asuransi kendaraan Mei 2026',
                    'bukti'             => null,
                    'attachment'        => null,
                    'status'            => $statusAsuransi[$i % 3 === 0 ? 1 : 0],
                ]);
            }

            // ── 4. SERVICE PARTS (1–2 per history) ──
            $jumlahPart = rand(1, 2);
            for ($p = 0; $p < $jumlahPart; $p++) {
                $tglPasang     = $tanggal->copy();
                $intervalNilai = rand(3, 24);
                $intervalSat   = $intervalSatuan[array_rand($intervalSatuan)];
                $tanggalLimit  = $intervalSat === 'bulan'
                    ? $tglPasang->copy()->addMonths($intervalNilai)
                    : $tglPasang->copy()->addYears($intervalNilai);

                ServicePart::create([
                    'service_history_id' => $history->id,
                    'kendaraan_id'       => $kendaraanId,
                    'category_id'        => $catId($partList[($i + $p) % count($partList)]['cat']),
                    'nama_part'          => $partList[($i + $p) % count($partList)]['nama'],
                    'part_number'        => 'PN-' . strtoupper(substr(md5('mei' . $i . $p), 0, 8)),
                    'serial_number'      => 'SN-' . rand(100000, 999999),
                    'posisi'             => ['Depan Kiri', 'Depan Kanan', 'Belakang Kiri', 'Belakang Kanan', 'Tengah'][($i + $p) % 5],
                    'tgl_pasang'         => $tglPasang->toDateString(),
                    'kilometer_pasang'   => $kilometer,
                    'kondisi'            => $kondisiPart[($i + $p) % count($kondisiPart)],
                    'status'             => $tanggalLimit->isPast() ? 'Limit' : 'Terpasang',
                    'interval_nilai'     => $intervalNilai,
                    'interval_satuan'    => $intervalSat,
                    'tanggal_limit'      => $tanggalLimit->toDateString(),
                    'biaya'              => rand(1, 20) * 50000,
                    'status_pengeluaran' => 'stabil',
                    'bukti'              => null,
                    'keterangan'         => $keteranganPart[($i + $p) % count($keteranganPart)],
                    'is_request'         => false,
                    'status_approval'    => 'approved',
                    'approval_by'        => 1,
                    'approval_at'        => $tanggal->copy()->addDay(),
                ]);
            }
        }

        $this->command->info('');
        $this->command->info('✅ ServiceDummyMeiSeeder selesai!');
        $this->command->info('   Bulan           : Mei 2026');
        $this->command->info('   service_history : ' . $jumlahRecord . ' records');
        $this->command->info('   Total biaya     : Rp ' . number_format($totalBiayaInserted, 0, ',', '.'));
    }

    /**
     * Distribusikan $total ke $n angka bulat dengan variasi acak,
     * pastikan jumlahnya tepat $total.
     */
    private function distribusiBiaya(int $total, int $n): array
    {
        // Buat n-1 titik potong acak dalam range [1, total-1]
        $min    = 100_000;   // minimum per record
        $max    = 1_500_000; // maximum per record

        $result = [];
        $sisa   = $total;

        for ($i = 0; $i < $n - 1; $i++) {
            // Sisa dibagi rata untuk record berikutnya, dengan variasi ±50%
            $rata    = (int) ($sisa / ($n - $i));
            $varMin  = max($min, (int) ($rata * 0.3));
            $varMax  = min($max, min($sisa - $min * ($n - $i - 1), (int) ($rata * 1.8)));
            $varMax  = max($varMin, $varMax);
            $nilai   = rand($varMin, $varMax);
            // Bulatkan ke 50.000
            $nilai   = (int) round($nilai / 50000) * 50000;
            $nilai   = max($min, min($nilai, $sisa - $min * ($n - $i - 1)));
            $result[] = $nilai;
            $sisa    -= $nilai;
        }

        // Record terakhir dapat sisa
        $result[] = max($min, $sisa);

        shuffle($result);
        return $result;
    }
}
