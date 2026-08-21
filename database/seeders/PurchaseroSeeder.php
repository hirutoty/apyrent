<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Purchasero;
use App\Models\PurchaseroItem;
use Carbon\Carbon;

class PurchaseroSeeder extends Seeder
{
    public function run(): void
    {
        $year       = 2026;
        $startDate  = Carbon::create($year, 1, 1);
        $endDate    = Carbon::create($year, 12, 30);

        $departemen = ['Keuangan', 'Produksi', 'HRD', 'Purchase', 'Sales', 'Marketing', 'IT'];
        $statusPool = ['Pending', 'Diajukan', 'Disetujui', 'Ditolak'];
        $statusWeight = [
            'Pending'   => 15,
            'Diajukan'  => 25,
            'Disetujui' => 45,
            'Ditolak'   => 15,
        ];

        $barangList = [
            ['nama' => 'Kertas HVS A4', 'kategori' => 'ATK', 'satuan' => 'Rim', 'harga' => 55000],
            ['nama' => 'Tinta Printer', 'kategori' => 'ATK', 'satuan' => 'Botol', 'harga' => 85000],
            ['nama' => 'Laptop Dell', 'kategori' => 'Elektronik', 'satuan' => 'Unit', 'harga' => 12500000],
            ['nama' => 'Monitor 24"', 'kategori' => 'Elektronik', 'satuan' => 'Unit', 'harga' => 2800000],
            ['nama' => 'Kursi Kantor', 'kategori' => 'Furnitur', 'satuan' => 'Unit', 'harga' => 1500000],
            ['nama' => 'Meja Kerja', 'kategori' => 'Furnitur', 'satuan' => 'Unit', 'harga' => 2200000],
            ['nama' => 'Oli Mesin', 'kategori' => 'Spare Part', 'satuan' => 'Liter', 'harga' => 75000],
            ['nama' => 'Filter Udara', 'kategori' => 'Spare Part', 'satuan' => 'Pcs', 'harga' => 125000],
            ['nama' => 'Seragam Karyawan', 'kategori' => 'Perlengkapan', 'satuan' => 'Pcs', 'harga' => 180000],
            ['nama' => 'Helm Safety', 'kategori' => 'K3', 'satuan' => 'Pcs', 'harga' => 95000],
            ['nama' => 'Sarung Tangan', 'kategori' => 'K3', 'satuan' => 'Pasang', 'harga' => 35000],
            ['nama' => 'Mouse Wireless', 'kategori' => 'Elektronik', 'satuan' => 'Unit', 'harga' => 280000],
            ['nama' => 'Keyboard', 'kategori' => 'Elektronik', 'satuan' => 'Unit', 'harga' => 320000],
            ['nama' => 'Flash Disk 64GB', 'kategori' => 'Elektronik', 'satuan' => 'Unit', 'harga' => 150000],
            ['nama' => 'Rak Arsip', 'kategori' => 'Furnitur', 'satuan' => 'Unit', 'harga' => 850000],
            ['nama' => 'Whiteboard', 'kategori' => 'ATK', 'satuan' => 'Unit', 'harga' => 650000],
            ['nama' => 'Proyektor', 'kategori' => 'Elektronik', 'satuan' => 'Unit', 'harga' => 7500000],
            ['nama' => 'Kabel LAN', 'kategori' => 'Jaringan', 'satuan' => 'Meter', 'harga' => 8000],
            ['nama' => 'Switch Network', 'kategori' => 'Jaringan', 'satuan' => 'Unit', 'harga' => 1200000],
            ['nama' => 'UPS 1000VA', 'kategori' => 'Elektronik', 'satuan' => 'Unit', 'harga' => 1800000],
        ];

        $alasanList = [
            'Penggantian peralatan yang rusak',
            'Kebutuhan operasional bulanan',
            'Pengadaan untuk proyek baru',
            'Stok habis perlu restock',
            'Upgrade peralatan lama',
            'Kebutuhan karyawan baru',
            'Mendukung kegiatan operasional',
            'Permintaan dari divisi terkait',
        ];

        $pemohonList = [
            'Budi Santoso', 'Siti Rahayu', 'Ahmad Fauzi', 'Dewi Kusuma',
            'Rudi Hartono', 'Rina Marlina', 'Hendra Gunawan', 'Nadia Putri',
        ];

        $prCounter = 1;
        $current   = $startDate->copy();

        while ($current->lte($endDate)) {
            // 2–5 pengadaan per minggu, acak
            if ($current->dayOfWeek === Carbon::MONDAY) {
                $jumlahPR = rand(2, 5);

                for ($i = 0; $i < $jumlahPR; $i++) {
                    // Tanggal acak dalam minggu ini (Sen–Jum)
                    $hariOffset = rand(0, 4);
                    $tgl = $current->copy()->addDays($hariOffset);
                    if ($tgl->gt($endDate)) break;

                    $dept   = $departemen[array_rand($departemen)];
                    $status = $this->weightedRandom($statusWeight);
                    $noPr   = 'PR-' . $year . '-' . str_pad($prCounter, 4, '0', STR_PAD_LEFT);

                    // 1–3 item per PR
                    $jumlahItem = rand(1, 3);
                    $totalNominal = 0;
                    $items = [];

                    for ($j = 0; $j < $jumlahItem; $j++) {
                        $barang  = $barangList[array_rand($barangList)];
                        $qty     = rand(1, 10);
                        $subtotal = $barang['harga'] * $qty;
                        $totalNominal += $subtotal;

                        $items[] = [
                            'nama_barang'  => $barang['nama'],
                            'kategori'     => $barang['kategori'],
                            'qty'          => $qty,
                            'satuan'       => $barang['satuan'],
                            'harga_satuan' => $barang['harga'],
                            'subtotal'     => $subtotal,
                        ];
                    }

                    $pr = Purchasero::create([
                        'no_pr'              => $noPr,
                        'tanggal'            => $tgl->toDateString(),
                        'departemen'         => $dept,
                        'pemohon'            => $pemohonList[array_rand($pemohonList)],
                        'barang_jasa'        => $items[0]['nama_barang'] . ($jumlahItem > 1 ? ' +' . ($jumlahItem - 1) . ' lainnya' : ''),
                        'kode_barang'        => 'KB-' . strtoupper(substr($dept, 0, 3)) . '-' . str_pad($prCounter, 3, '0', STR_PAD_LEFT),
                        'qty'                => array_sum(array_column($items, 'qty')),
                        'satuan'             => $items[0]['satuan'],
                        'alasan_permintaan'  => $alasanList[array_rand($alasanList)],
                        'nominal'            => $totalNominal,
                        'status'             => $status,
                        'disetujui_oleh'     => in_array($status, ['Disetujui', 'Ditolak']) ? 'Admin' : null,
                        'tanggal_persetujuan'=> in_array($status, ['Disetujui', 'Ditolak']) ? $tgl->copy()->addDays(rand(1, 3))->toDateString() : null,
                        'catatan'            => $status === 'Ditolak' ? 'Tidak sesuai kebutuhan saat ini' : null,
                        'terakhir_diajukan'  => in_array($status, ['Diajukan', 'Disetujui', 'Ditolak']) ? $tgl->copy()->addDay() : null,
                    ]);

                    foreach ($items as $item) {
                        PurchaseroItem::create(array_merge($item, ['purchasero_id' => $pr->id]));
                    }

                    $prCounter++;
                }
            }

            $current->addDay();
        }

        $this->command->info("Seeder selesai: {$prCounter} pengadaan dibuat dari 1 Jan – 30 Des {$year}.");
    }

    /**
     * Pilih status berdasarkan bobot
     */
    private function weightedRandom(array $weights): string
    {
        $total = array_sum($weights);
        $rand  = rand(1, $total);
        $cumulative = 0;

        foreach ($weights as $item => $weight) {
            $cumulative += $weight;
            if ($rand <= $cumulative) {
                return $item;
            }
        }

        return array_key_first($weights);
    }
}
