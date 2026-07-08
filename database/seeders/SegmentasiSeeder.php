<?php

namespace Database\Seeders;

use App\Models\Segmentasi;
use Illuminate\Database\Seeder;

class SegmentasiSeeder extends Seeder
{
    public function run(): void
    {
        Segmentasi::create([
            'segment_code' => 'SEG001',
            'segment_name' => 'Corporate Client',
            'segmentation_criteria' => 'Perusahaan dengan kontrak bulanan',
            'customer_count' => 15,
            'campaign_goal' => 'Retain & Upsell',
            'status' => 'Aktif',
        ]);

        Segmentasi::create([
            'segment_code' => 'SEG002',
            'segment_name' => 'Individual Frequent',
            'segmentation_criteria' => 'Individu rental >3x dalam 6 bulan',
            'customer_count' => 48,
            'campaign_goal' => 'Loyalty Program',
            'status' => 'Aktif',
        ]);
    }
}
