<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProjectRisk;

class ProjectRiskSeeder extends Seeder
{
    public function run(): void
    {
        ProjectRisk::insert([
            ['proyek' => 'PRJ001', 'risiko' => 'Cuaca ekstrim hujan deras',       'dampak' => 'Sedang', 'kemungkinan' => 'Tinggi',   'mitigasi' => 'Sediakan terpal & pompa air',          'status' => 'Terkendali'],
            ['proyek' => 'PRJ001', 'risiko' => 'Kenaikan harga material',          'dampak' => 'Tinggi', 'kemungkinan' => 'Menengah', 'mitigasi' => 'Kontrak harga tetap dengan supplier',  'status' => 'Terkendali'],
            ['proyek' => 'PRJ002', 'risiko' => 'Keterlambatan pengiriman unit bus','dampak' => 'Tinggi', 'kemungkinan' => 'Rendah',   'mitigasi' => 'Klausul denda dalam perjanjian',       'status' => 'Diajukan'],
            ['proyek' => 'PRJ002', 'risiko' => 'Fluktuasi kurs impor',             'dampak' => 'Tinggi', 'kemungkinan' => 'Menengah', 'mitigasi' => 'Hedging mata uang',                    'status' => 'Diajukan'],
            ['proyek' => 'PRJ003', 'risiko' => 'Perangkat GPS tidak kompatibel',  'dampak' => 'Tinggi', 'kemungkinan' => 'Rendah',   'mitigasi' => 'Uji coba sebelum instalasi massal',    'status' => 'Terkendali'],
            ['proyek' => 'PRJ003', 'risiko' => 'Gangguan sinyal di area tertentu','dampak' => 'Sedang', 'kemungkinan' => 'Menengah', 'mitigasi' => 'Pasang booster sinyal di pool',        'status' => 'Diajukan'],
            ['proyek' => 'PRJ005', 'risiko' => 'Driver tidak hadir mendadak',     'dampak' => 'Tinggi', 'kemungkinan' => 'Menengah', 'mitigasi' => 'Siapkan driver cadangan on-call',      'status' => 'Terkendali'],
            ['proyek' => 'PRJ005', 'risiko' => 'Kemacetan rute utama',            'dampak' => 'Sedang', 'kemungkinan' => 'Tinggi',   'mitigasi' => 'Siapkan rute alternatif',              'status' => 'Terkendali'],
        ]);
    }
}
