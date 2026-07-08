<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KpiAppraisal;

class KpiAppraisalSeeder extends Seeder
{
    public function run(): void
    {
        $pegawai = [
            'Rini Apriani',    'Eko Prasetyo',    'Rizky Fadillah',
            'Yusuf Hidayat',   'Wahyu Nugroho',   'Fitri Handayani',
            'Teguh Santosa',   'Arif Budiman',    'Dewi Kusuma',
            'Linda Permata',   'Hendra Gunawan',  'Dody Kurniawan',
            'Siti Rahayu',     'Agus Wibowo',     'Budi Santoso',
        ];

        $periode = ['Q1 2025', 'Q2 2025', 'Q3 2025', 'Q4 2025', 'Q1 2026', 'Q2 2026'];

        $evaluator = 'Dewi Kusuma';

        foreach ($pegawai as $idx => $p) {
            foreach ($periode as $pIdx => $per) {
                $disiplin      = rand(65, 100);
                $kolaborasi    = rand(65, 100);
                $produktivitas = rand(65, 100);
                $nilaiAkhir    = round(($disiplin + $kolaborasi + $produktivitas) / 3, 2);

                KpiAppraisal::create([
                    'nama_pegawai'     => $p,
                    'periode_evaluasi' => $per,
                    'disiplin'         => $disiplin,
                    'kolaborasi'       => $kolaborasi,
                    'produktivitas'    => $produktivitas,
                    'nilai_akhir'      => $nilaiAkhir,
                    'evaluator'        => $evaluator,
                    'catatan'          => $nilaiAkhir >= 85
                        ? 'Performa sangat baik, pertahankan.'
                        : ($nilaiAkhir >= 70
                            ? 'Performa cukup, perlu peningkatan di beberapa aspek.'
                            : 'Perlu pembinaan dan evaluasi lanjutan.'),
                ]);
            }
        }
    }
}
