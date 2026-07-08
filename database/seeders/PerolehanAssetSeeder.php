<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PerolehanAsset;
use Carbon\Carbon;

class PerolehanAssetSeeder extends Seeder
{
    public function run(): void
    {
        $namaList      = ['Mobil Toyota Avanza', 'Laptop Dell Latitude', 'Kursi Direktur', 'Mesin Fotocopy', 'Server Dell PowerEdge',
                          'Printer HP LaserJet', 'AC Split Daikin', 'Motor Honda Vario', 'Proyektor Epson', 'Forklift Toyota'];
        $vendor        = ['PT Aneka Mitra', 'CV Teknologi Nusantara', 'UD Sumber Jaya', 'PT Indo Mesin', 'Toko IT Center'];
        $metodeBeli    = ['Pembelian Langsung', 'Leasing', 'Tender', 'Hibah'];
        $statusList    = ['Aktif', 'Nonaktif'];
        $pembayaranList= ['Lunas', 'Cicilan'];

        foreach ($namaList as $i => $nama) {
            PerolehanAsset::create([
                'tanggal_perolehan' => Carbon::now()->subYears(rand(1, 5))->subMonths(rand(0, 11)),
                'kode_aset'         => 'AST-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'nama_aset'         => $nama,
                'vendor'            => $vendor[$i % count($vendor)],
                'metode_pembelian'  => $metodeBeli[$i % count($metodeBeli)],
                'harga'             => rand(5, 500) * 1000000,
                'status'            => $statusList[$i % 2],
                'pembayaran'        => $pembayaranList[$i % 2],
            ]);
        }
    }
}
