<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PenawaranSales;
use Carbon\Carbon;

class PenawaranSeeder extends Seeder
{
    public function run(): void
    {
        $pelanggan = [
            'PT Maju Jaya Abadi',    'CV Berkah Mandiri',      'PT Teknologi Nusantara',
            'UD Sumber Rejeki',      'PT Logistik Andalan',    'CV Karya Utama',
            'PT Solusi Transportasi','PT Global Rentcar',      'CV Perdana Sejahtera',
            'PT Aneka Niaga',        'PT Bintang Timur',       'CV Mitra Sejati',
        ];

        $produk = [
            'Sewa Kendaraan Operasional',
            'Layanan Transportasi Proyek',
            'Sewa Armada Angkutan Barang',
            'Sewa Kendaraan Jangka Panjang',
            'Layanan Shuttle Karyawan',
            'Sewa Minibus Pariwisata',
        ];

        $statusList = ['Draft', 'Terkirim', 'Disetujui', 'Ditolak'];

        for ($i = 1; $i <= 30; $i++) {
            $tgl       = Carbon::now()->subDays(rand(1, 180));
            $validSampai = (clone $tgl)->addDays(rand(14, 60));
            $jumlah    = rand(1, 10);
            $harga     = rand(500000, 5000000);
            $total     = $jumlah * $harga;
            $status    = $statusList[($i - 1) % count($statusList)];

            PenawaranSales::updateOrCreate(
                ['no_quotation' => 'QUO-' . str_pad($i, 3, '0', STR_PAD_LEFT)],
                [
                    'no_quotation'  => 'QUO-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'tanggal'       => $tgl->toDateString(),
                    'pelanggan'     => $pelanggan[($i - 1) % count($pelanggan)],
                    'produk_jasa'   => $produk[($i - 1) % count($produk)],
                    'jumlah'        => $jumlah,
                    'harga_satuan'  => $harga,
                    'total_harga'   => $total,
                    'status'        => $status,
                    'valid_sampai'  => $validSampai->toDateString(),
                ]
            );
        }
    }
}
