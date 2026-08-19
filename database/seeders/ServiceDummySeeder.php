<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\ServiceHistory;
use App\Models\ServiceDetail;
use App\Models\ServiceAsuransi;
use App\Models\ServicePart;
use App\Models\Kendaraan;
use App\Models\JenisAsuransi;

class ServiceDummySeeder extends Seeder
{
    /**
     * Seed data dummy service untuk Juli, Agustus, September
     * Masing-masing bulan: 50 service_history + detail + asuransi + parts
     */
    public function run(): void
    {
        // Ambil semua kendaraan yang ada
        $kendaraanIds = Kendaraan::pluck('id')->toArray();

        if (empty($kendaraanIds)) {
            $this->command->warn('Tidak ada data kendaraan. Seeder dibatalkan.');
            return;
        }

        $jenisAsuransiIds = JenisAsuransi::pluck('id')->toArray();
        if (empty($jenisAsuransiIds)) {
            $jenisAsuransiIds = [null];
        }

        $bulan = [
            ['year' => 2026, 'month' => 7,  'label' => 'Juli'],
            ['year' => 2026, 'month' => 8,  'label' => 'Agustus'],
            ['year' => 2026, 'month' => 9,  'label' => 'September'],
        ];

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
            'Spooring dan balancing ban',
            'Ganti filter oli dan udara',
            'Servis AC dan isi freon',
            'Cuci injector bahan bakar',
            'Cek dan kencangkan baut chassis',
        ];

        $keteranganPart = [
            'Part baru original', 'Part KW berkualitas baik', 'Penggantian rutin sesuai jadwal',
            'Kondisi lama sudah aus', 'Rekomendasi bengkel resmi', 'Tindak lanjut servis sebelumnya',
            'Spare part import', 'Part lokal berkualitas', 'Penggantian darurat',
            'Servis preventif', 'Overhaul komponen', 'Pengecekan berkala',
        ];

        // Setiap part punya nama + category sesuai kategori DB
        // Kategori: AC, Ban, Bodi, Elektrikal, Kaki-kaki, Lainnya, Mesin, Transmisi
        $kategoris = \App\Models\ServiceCategory::pluck('id', 'nama')->toArray();
        // Helper ambil id, fallback ke Lainnya
        $catId = fn(string $nama) => $kategoris[$nama] ?? $kategoris['Lainnya'] ?? null;

        $partList = [
            ['nama' => 'Oli Mesin Shell 10W-40',       'cat' => 'Mesin'],
            ['nama' => 'Filter Oli',                    'cat' => 'Mesin'],
            ['nama' => 'Filter Udara',                  'cat' => 'Mesin'],
            ['nama' => 'Kampas Rem Depan',              'cat' => 'Kaki-kaki'],
            ['nama' => 'Kampas Rem Belakang',           'cat' => 'Kaki-kaki'],
            ['nama' => 'Busi NGK',                      'cat' => 'Mesin'],
            ['nama' => 'Aki GS 60Ah',                   'cat' => 'Elektrikal'],
            ['nama' => 'V-Belt',                        'cat' => 'Mesin'],
            ['nama' => 'Timing Belt',                   'cat' => 'Mesin'],
            ['nama' => 'Kopling Set',                   'cat' => 'Transmisi'],
            ['nama' => 'Shock Absorber Depan',          'cat' => 'Kaki-kaki'],
            ['nama' => 'Shock Absorber Belakang',       'cat' => 'Kaki-kaki'],
            ['nama' => 'Ban Bridgestone 195/65 R15',    'cat' => 'Ban'],
            ['nama' => 'Freon AC R134a',                'cat' => 'AC'],
            ['nama' => 'Coolant Radiator',              'cat' => 'Mesin'],
            ['nama' => 'Wiper Blade',                   'cat' => 'Elektrikal'],
            ['nama' => 'Lampu Rem',                     'cat' => 'Elektrikal'],
            ['nama' => 'Sensor O2',                     'cat' => 'Elektrikal'],
            ['nama' => 'Bearing Roda',                  'cat' => 'Kaki-kaki'],
            ['nama' => 'Puli AC',                       'cat' => 'AC'],
        ];

        $namaAsuransi = [
            'Asuransi Jasindo',
            'Asuransi Wahana Tata',
            'Asuransi MAG',
            'Asuransi Adira',
            'Asuransi Sinar Mas',
            'Asuransi Jasa Raharja',
        ];

        $statusHistory   = ['proses', 'selesai'];
        $statusDetail    = ['Layak', 'Tidak Layak'];
        $statusAsuransi  = ['bermasalah', 'selesai'];
        $statusPengeluaran = ['stabil', 'overservice'];
        $kondisiPart     = ['Baik', 'Rusak', 'Perlu Ganti'];
        $intervalSatuan  = ['bulan', 'tahun', 'km'];

        $totalInserted = 0;

        foreach ($bulan as $b) {
            $this->command->info("Seeding service bulan {$b['label']} {$b['year']}...");
            $daysInMonth = Carbon::createFromDate($b['year'], $b['month'], 1)->daysInMonth;

            for ($i = 1; $i <= 50; $i++) {
                $kendaraanId  = $kendaraanIds[array_rand($kendaraanIds)];
                $tanggal      = Carbon::createFromDate($b['year'], $b['month'], rand(1, $daysInMonth));
                $kilometer    = rand(10000, 150000);
                $totalBiaya   = rand(2, 60) * 50000;
                $maks_bulanan = rand(5, 40) * 500000;
                $biaya_tahunan = rand(10, 120) * 500000;
                $sisaLimit    = rand(0, 10) * 500000;
                $statusIdx    = ($i % 5 === 0) ? 0 : 1; // 20% proses, 80% selesai

                // ── 1. SERVICE HISTORY ──
                $history = ServiceHistory::create([
                    'kendaraan_id'       => $kendaraanId,
                    'keluhan'            => $keluhanList[($i - 1) % count($keluhanList)],
                    'kilometer'          => $kilometer,
                    'total_biaya'        => $totalBiaya,
                    'status'             => $statusHistory[$statusIdx],
                    'tanggal_service'    => $tanggal->toDateString(),
                    'sisa_limit'         => $sisaLimit,
                    'maks_bulanan'       => $maks_bulanan,
                    'biaya_tahunan'      => $biaya_tahunan,
                    'status_pengeluaran' => $statusPengeluaran[$totalBiaya > $maks_bulanan ? 1 : 0],
                    'bukti_pembayaran'   => null,
                    'status_approval'    => 'approved',
                    'approval_by'        => 1,
                    'approval_at'        => $tanggal->copy()->addDay(),
                    'is_request'         => false,
                ]);

                // ── 2. SERVICE DETAIL (1–2 per history) ──
                $jumlahDetail = rand(1, 2);
                for ($d = 0; $d < $jumlahDetail; $d++) {
                    $detailBiaya = rand(1, 20) * 50000;
                    ServiceDetail::create([
                        'kendaraan_id'       => $kendaraanId,
                        'service_history_id' => $history->id,
                        'tanggal_service'    => $tanggal->toDateString(),
                        'kilometer'          => $kilometer + ($d * rand(100, 500)),
                        'status'             => $statusDetail[($i + $d) % 2 === 0 ? 1 : 0],
                        'biaya'              => $detailBiaya,
                        'keterangan'         => $keluhanList[($i + $d) % count($keluhanList)],
                        'bukti'              => null,
                        'attachment'         => null,
                    ]);
                }

                // ── 3. SERVICE ASURANSI (setiap 3 record sekali) ──
                if ($i % 3 === 0) {
                    $periodeMulai   = $tanggal->copy()->startOfMonth();
                    $periodeSelesai = $periodeMulai->copy()->addYear()->subDay();
                    ServiceAsuransi::create([
                        'kendaraan_id'     => $kendaraanId,
                        'nama_asuransi'    => $namaAsuransi[array_rand($namaAsuransi)],
                        'jenis_asuransi_id'=> $jenisAsuransiIds[array_rand($jenisAsuransiIds)],
                        'tanggal_service'  => $tanggal->toDateString(),
                        'periode_mulai'    => $periodeMulai->toDateString(),
                        'periode_selesai'  => $periodeSelesai->toDateString(),
                        'kilometer'        => $kilometer,
                        'biaya'            => rand(5, 50) * 100000,
                        'keterangan'       => 'Perpanjangan asuransi kendaraan ' . $b['label'] . ' ' . $b['year'],
                        'bukti'            => null,
                        'attachment'       => null,
                        'status'           => $statusAsuransi[$i % 3 === 0 ? 1 : 0],
                    ]);
                }

                // ── 4. SERVICE PARTS (1–3 part per history) ──
                $jumlahPart = rand(1, 3);
                for ($p = 0; $p < $jumlahPart; $p++) {
                    $tglPasang      = $tanggal->copy();
                    $intervalNilai  = rand(3, 24);
                    $intervalSat    = ['bulan', 'tahun'][array_rand(['bulan', 'tahun'])];
                    $tanggalLimit   = match ($intervalSat) {
                        'bulan' => $tglPasang->copy()->addMonths($intervalNilai),
                        'tahun' => $tglPasang->copy()->addYears($intervalNilai),
                        default => $tglPasang->copy()->addMonths($intervalNilai),
                    };

                    $part = $partList[($i + $p) % count($partList)];

                    ServicePart::create([
                        'service_history_id' => $history->id,
                        'kendaraan_id'       => $kendaraanId,
                        'category_id'        => $catId($part['cat']),
                        'nama_part'          => $part['nama'],
                        'part_number'        => 'PN-' . strtoupper(substr(md5($i . $p . $b['month']), 0, 8)),
                        'serial_number'      => 'SN-' . rand(100000, 999999),
                        'posisi'             => ['Depan Kiri', 'Depan Kanan', 'Belakang Kiri', 'Belakang Kanan', 'Tengah'][($i + $p) % 5],
                        'tgl_pasang'         => $tglPasang->toDateString(),
                        'kilometer_pasang'   => $kilometer,
                        'kondisi'            => $kondisiPart[($i + $p) % count($kondisiPart)],
                        'status'             => ($tanggalLimit->isPast() ? 'Limit' : 'Terpasang'),
                        'interval_nilai'     => $intervalNilai,
                        'interval_satuan'    => $intervalSat,
                        'tanggal_limit'      => $tanggalLimit->toDateString(),
                        'biaya'              => rand(1, 30) * 50000,
                        'status_pengeluaran' => 'stabil',
                        'bukti'              => null,
                        'keterangan'         => $keteranganPart[($i + $p) % count($keteranganPart)],
                        'is_request'         => false,
                        'status_approval'    => 'approved',
                        'approval_by'        => 1,
                        'approval_at'        => $tanggal->copy()->addDay(),
                    ]);
                }

                $totalInserted++;
            }

            $this->command->info("  ✓ {$b['label']}: 50 service history selesai.");
        }

        $this->command->info("");
        $this->command->info("✅ ServiceDummySeeder selesai!");
        $this->command->info("   Total service_history : {$totalInserted}");
        $this->command->info("   Total bulan           : 3 (Juli, Agustus, September 2026)");
        $this->command->info("   service_detail        : ~{$totalInserted} – ~" . ($totalInserted * 2) . " records");
        $this->command->info("   service_asuransi      : ~" . round($totalInserted / 3) . " records");
        $this->command->info("   service_parts         : ~{$totalInserted} – ~" . ($totalInserted * 3) . " records");
    }
}
