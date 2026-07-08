<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PenanggungJawab;
use Carbon\Carbon;

class PenanggungJawabSeeder extends Seeder
{
    public function run(): void
    {
        $namaAsset = ['Mobil Toyota Avanza', 'Laptop Dell Latitude', 'Kursi Direktur', 'Mesin Fotocopy',
                      'Gedung Operasional', 'Printer HP LaserJet', 'AC Split Daikin', 'Motor Honda Vario',
                      'Server Dell PowerEdge', 'Meja Rapat', 'Proyektor Epson', 'Forklift Toyota'];
        $pic       = ['Andi Pratama', 'Budi Santoso', 'Citra Dewi', 'Denny Wijaya', 'Eka Putri', 'Fajar Nugroho'];
        $divisi    = ['IT', 'Finance', 'Operasional', 'HRD', 'Marketing', 'Produksi'];

        foreach ($namaAsset as $i => $nama) {
            PenanggungJawab::create([
                'kode_aset'          => 'AST-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'nama_aset'          => $nama,
                'pic'                => $pic[$i % count($pic)],
                'tanggal_penempatan' => Carbon::now()->subYears(rand(0, 3))->subMonths(rand(0, 11)),
                'divisi'             => $divisi[$i % count($divisi)],
                'status'             => $i % 6 === 0 ? 'Nonaktif' : 'Aktif',
            ]);
        }
    }
}
