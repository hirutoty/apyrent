<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DokumentasiAsset;

class DokumentasiAssetSeeder extends Seeder
{
    public function run(): void
    {
        $namaList = ['Mobil Toyota Avanza', 'Laptop Dell Latitude', 'Kursi Direktur', 'Mesin Fotocopy',
                     'Gedung Operasional', 'Printer HP LaserJet', 'AC Split Daikin', 'Motor Honda Vario',
                     'Server Dell PowerEdge', 'Meja Rapat', 'Proyektor Epson', 'Forklift Toyota'];

        foreach ($namaList as $i => $nama) {
            $adaFoto = $i % 3 !== 0;
            DokumentasiAsset::create([
                'kode_aset'      => 'AST-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'nama_aset'      => $nama,
                'foto_tersimpan' => $adaFoto,
                'lokasi_file'    => $adaFoto ? '/storage/assets/ast_' . str_pad($i + 1, 3, '0', STR_PAD_LEFT) . '.jpg' : null,
                'catatan'        => $adaFoto ? 'Foto diambil saat inventarisasi ' . date('Y') : 'Belum ada foto, perlu didokumentasikan',
            ]);
        }
    }
}
