<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssetDihapuskan;
use Carbon\Carbon;

class AssetDihapuskanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['AST-016', 'Komputer Lama IBM', 'Dijual', 'Sudah tidak layak pakai', 500000],
            ['AST-017', 'Meja Kayu Tua', 'Dimusnahkan', 'Rusak berat akibat rayap', 0],
            ['AST-018', 'Printer Dot Matrix', 'Disumbangkan', 'Disumbangkan ke sekolah', 200000],
            ['AST-019', 'Kamera CCTV Analog', 'Dijual', 'Diganti sistem digital', 750000],
            ['AST-020', 'AC Window Lawas', 'Dimusnahkan', 'Tidak bisa diperbaiki lagi', 0],
            ['AST-021', 'Kursi Plastik Rusak', 'Dimusnahkan', 'Seluruh kaki patah', 0],
            ['AST-022', 'Telepon Fax Canon', 'Dijual', 'Sudah tidak digunakan', 300000],
            ['AST-023', 'Motor Dinas Tua', 'Dijual', 'Pensiun dinas', 2500000],
        ];

        foreach ($data as $i => [$kode, $nama, $status, $alasan, $nilai]) {
            AssetDihapuskan::create([
                'kode_aset'    => $kode,
                'nama_aset'    => $nama,
                'tanggal_hapus'=> Carbon::now()->subDays(rand(30, 365)),
                'alasan'       => $alasan,
                'nilai_buku'   => $nilai,
                'status_akhir' => $status,
            ]);
        }
    }
}
