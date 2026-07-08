<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IndukAsset;
use Carbon\Carbon;

class IndukAssetSeeder extends Seeder
{
    public function run(): void
    {
        $kategori  = ['Kendaraan', 'Elektronik', 'Perabot', 'Mesin', 'Bangunan', 'Peralatan Kantor'];
        $lokasi    = ['Kantor Pusat', 'Gudang A', 'Gudang B', 'Workshop', 'Cabang Surabaya', 'Cabang Bandung'];
        $pic       = ['Andi Pratama', 'Budi Santoso', 'Citra Dewi', 'Denny Wijaya', 'Eka Putri'];
        $metode    = ['Garis Lurus', 'Saldo Menurun', 'Unit Produksi'];
        $namaAsset = [
            'Mobil Toyota Avanza', 'Laptop Dell Latitude', 'Kursi Direktur', 'Mesin Fotocopy',
            'Gedung Operasional', 'Printer HP LaserJet', 'AC Split Daikin', 'Motor Honda Vario',
            'Server Dell PowerEdge', 'Meja Rapat', 'Proyektor Epson', 'Forklift Toyota',
            'Komputer Desktop Lenovo', 'Kulkas Samsung', 'Genset Perkins',
        ];

        foreach ($namaAsset as $i => $nama) {
            $kat = $kategori[$i % count($kategori)];
            IndukAsset::updateOrCreate(
                ['kode_aset' => 'AST-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT)],
                [
                    'nama_aset'         => $nama,
                    'kategori'          => $kat,
                    'lokasi'            => $lokasi[$i % count($lokasi)],
                    'tanggal_perolehan' => Carbon::now()->subYears(rand(1, 8))->subMonths(rand(0, 11)),
                    'harga_perolehan'   => rand(5, 500) * 1000000,
                    'status'            => $i % 5 === 0 ? 'Nonaktif' : 'Aktif',
                    'pic'               => $pic[$i % count($pic)],
                    'umur_ekonomis'     => [3, 4, 5, 8, 10, 20][$i % 6],
                    'metode_penyusutan' => $metode[$i % count($metode)],
                ]
            );
        }
    }
}
