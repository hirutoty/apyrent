<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PergerakanAsset;
use Carbon\Carbon;

class PergerakanAssetSeeder extends Seeder
{
    public function run(): void
    {
        $kodeAsset  = array_map(fn($i) => 'AST-' . str_pad($i, 3, '0', STR_PAD_LEFT), range(1, 15));
        $jenis      = ['Mutasi', 'Peminjaman', 'Pengembalian'];
        $lokasi     = ['Kantor Pusat', 'Gudang A', 'Gudang B', 'Workshop', 'Cabang Surabaya', 'Cabang Bandung'];
        $pegawai    = ['Andi Pratama', 'Budi Santoso', 'Citra Dewi', 'Denny Wijaya', 'Eka Putri'];
        $manajer    = ['Manajer IT', 'Manajer Operasional', 'Manajer Keuangan', 'Direktur Umum'];

        for ($i = 1; $i <= 30; $i++) {
            PergerakanAsset::create([
                'kode_aset'        => $kodeAsset[($i - 1) % count($kodeAsset)],
                'tanggal'          => Carbon::now()->subDays(rand(1, 180)),
                'jenis_pergerakan' => $jenis[($i - 1) % count($jenis)],
                'dari_lokasi'      => $lokasi[($i - 1) % count($lokasi)],
                'ke_lokasi'        => $lokasi[$i % count($lokasi)],
                'dilakukan_oleh'   => $pegawai[($i - 1) % count($pegawai)],
                'disetujui_oleh'   => $manajer[($i - 1) % count($manajer)],
                'catatan'          => $i % 3 === 0 ? 'Pemindahan rutin triwulan ke-' . ceil($i / 3) : null,
            ]);
        }
    }
}
