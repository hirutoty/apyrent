<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SkillMatrix;
use Carbon\Carbon;

class SkillMatrixSeeder extends Seeder
{
    public function run(): void
    {
        $pegawai = [
            'Rizky Fadillah', 'Yusuf Hidayat', 'Hendra Gunawan',
            'Wahyu Nugroho',  'Fitri Handayani', 'Linda Permata',
            'Rini Apriani',   'Eko Prasetyo',    'Dewi Kusuma',
            'Teguh Santosa',  'Arif Budiman',    'Dody Kurniawan',
        ];

        $skills = [
            ['Laravel',          'Hendra Gunawan'],
            ['Vue.js',           'Hendra Gunawan'],
            ['MySQL',            'Hendra Gunawan'],
            ['PHP',              'Hendra Gunawan'],
            ['Microsoft Excel',  'Linda Permata'],
            ['Akuntansi',        'Linda Permata'],
            ['Perpajakan',       'Linda Permata'],
            ['SAP',              'Linda Permata'],
            ['Rekrutmen',        'Dewi Kusuma'],
            ['Payroll',          'Dewi Kusuma'],
            ['K3',               'Dewi Kusuma'],
            ['Negosiasi',        'Dody Kurniawan'],
            ['AutoCAD',          'Teguh Santosa'],
            ['Troubleshooting',  'Yusuf Hidayat'],
        ];

        foreach ($skills as $i => [$skill, $evaluator]) {
            $idx       = $i % count($pegawai);
            $tglEval   = Carbon::now()->subDays(rand(30, 365));

            SkillMatrix::create([
                'nama_pegawai'     => $pegawai[$idx],
                'skill'            => $skill,
                'level'            => rand(1, 5),
                'sertifikasi'      => $i % 3 === 0 ? 'Y' : 'T',
                'evaluator'        => $evaluator,
                'tanggal_evaluasi' => $tglEval,
            ]);
        }
    }
}
