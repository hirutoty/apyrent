<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bukubesar;
use Carbon\Carbon;

class BukubesarSeeder extends Seeder
{
    public function run(): void
    {
        // Nilai enum yang valid: Pendapatan, Beban, Aktiva, Modal, Kewajiban
        $entries = [
            // Pendapatan
            ['transaksi' => 'Pemasukan Rental Harian',       'kategori' => 'Pendapatan', 'aktivitas' => 'rental',    'debit' => 1500000,  'kredit' => 0,       'keterangan' => 'Pembayaran rental harian dari customer'],
            ['transaksi' => 'Pemasukan Rental Mingguan',     'kategori' => 'Pendapatan', 'aktivitas' => 'rental',    'debit' => 3500000,  'kredit' => 0,       'keterangan' => 'Pembayaran rental mingguan'],
            ['transaksi' => 'Penerimaan DP Rental',          'kategori' => 'Pendapatan', 'aktivitas' => 'rental',    'debit' => 1000000,  'kredit' => 0,       'keterangan' => 'DP rental kendaraan'],
            ['transaksi' => 'Pelunasan Rental',              'kategori' => 'Pendapatan', 'aktivitas' => 'rental',    'debit' => 2000000,  'kredit' => 0,       'keterangan' => 'Pelunasan biaya rental'],
            ['transaksi' => 'Penerimaan Denda Keterlambatan','kategori' => 'Pendapatan', 'aktivitas' => 'denda',     'debit' => 250000,   'kredit' => 0,       'keterangan' => 'Denda pengembalian terlambat'],
            ['transaksi' => 'Penerimaan Deposit Customer',  'kategori' => 'Pendapatan', 'aktivitas' => 'deposit',   'debit' => 500000,   'kredit' => 0,       'keterangan' => 'Deposit jaminan kendaraan'],
            ['transaksi' => 'Pendapatan Biaya Tambahan',    'kategori' => 'Pendapatan', 'aktivitas' => 'rental',    'debit' => 200000,   'kredit' => 0,       'keterangan' => 'Biaya supir tambahan'],
            ['transaksi' => 'Penerimaan Sewa Jangka Panjang','kategori' => 'Pendapatan','aktivitas' => 'rental',    'debit' => 15000000, 'kredit' => 0,       'keterangan' => 'Kontrak sewa bulanan'],
            ['transaksi' => 'Pendapatan Lain-lain',         'kategori' => 'Pendapatan', 'aktivitas' => 'lain',      'debit' => 350000,   'kredit' => 0,       'keterangan' => 'Pendapatan di luar operasional utama'],
            ['transaksi' => 'Penerimaan Invoice Kontrak',   'kategori' => 'Pendapatan', 'aktivitas' => 'invoice',   'debit' => 8000000,  'kredit' => 0,       'keterangan' => 'Pembayaran invoice kontrak korporat'],

            // Beban
            ['transaksi' => 'Biaya Servis Berkala',         'kategori' => 'Beban',      'aktivitas' => 'service',   'debit' => 0, 'kredit' => 500000,  'keterangan' => 'Servis rutin kendaraan'],
            ['transaksi' => 'Biaya Ganti Oli',              'kategori' => 'Beban',      'aktivitas' => 'service',   'debit' => 0, 'kredit' => 150000,  'keterangan' => 'Penggantian oli mesin'],
            ['transaksi' => 'Pembayaran Pajak Kendaraan',   'kategori' => 'Beban',      'aktivitas' => 'pajak',     'debit' => 0, 'kredit' => 3500000, 'keterangan' => 'Pajak tahunan kendaraan'],
            ['transaksi' => 'Premi Asuransi Kendaraan',     'kategori' => 'Beban',      'aktivitas' => 'asuransi',  'debit' => 0, 'kredit' => 5000000, 'keterangan' => 'Pembayaran premi asuransi'],
            ['transaksi' => 'Biaya Sewa GPS',               'kategori' => 'Beban',      'aktivitas' => 'gps',       'debit' => 0, 'kredit' => 300000,  'keterangan' => 'Biaya langganan GPS tracker'],
            ['transaksi' => 'Biaya Bahan Bakar',            'kategori' => 'Beban',      'aktivitas' => 'operasional','debit' => 0,'kredit' => 800000,  'keterangan' => 'Pembelian bahan bakar kendaraan'],
            ['transaksi' => 'Biaya KIR Kendaraan',          'kategori' => 'Beban',      'aktivitas' => 'kir',       'debit' => 0, 'kredit' => 200000,  'keterangan' => 'Biaya uji KIR kendaraan'],
            ['transaksi' => 'Biaya Gaji Karyawan',          'kategori' => 'Beban',      'aktivitas' => 'gaji',      'debit' => 0, 'kredit' => 5000000, 'keterangan' => 'Gaji karyawan bulan ini'],
            ['transaksi' => 'Biaya Pembelian Spare Part',   'kategori' => 'Beban',      'aktivitas' => 'service',   'debit' => 0, 'kredit' => 1200000, 'keterangan' => 'Pembelian ban dan kampas rem'],
            ['transaksi' => 'Biaya Listrik dan Air',        'kategori' => 'Beban',      'aktivitas' => 'operasional','debit' => 0,'kredit' => 450000,  'keterangan' => 'Tagihan utilitas kantor'],

            // Aktiva
            ['transaksi' => 'Pembelian Kendaraan Baru',     'kategori' => 'Aktiva',     'aktivitas' => 'pembelian', 'debit' => 250000000,'kredit' => 0,      'keterangan' => 'Penambahan aset kendaraan baru'],
            ['transaksi' => 'Kas di Tangan',                'kategori' => 'Aktiva',     'aktivitas' => 'kas',       'debit' => 10000000, 'kredit' => 0,      'keterangan' => 'Saldo kas operasional'],
            ['transaksi' => 'Kas di Bank',                  'kategori' => 'Aktiva',     'aktivitas' => 'kas',       'debit' => 50000000, 'kredit' => 0,      'keterangan' => 'Saldo rekening bank perusahaan'],
            ['transaksi' => 'Piutang Rental',               'kategori' => 'Aktiva',     'aktivitas' => 'rental',    'debit' => 7500000,  'kredit' => 0,      'keterangan' => 'Tagihan belum dibayar customer'],
            ['transaksi' => 'Perlengkapan Kantor',          'kategori' => 'Aktiva',     'aktivitas' => 'operasional','debit' => 2500000, 'kredit' => 0,      'keterangan' => 'Inventaris perlengkapan kantor'],
            ['transaksi' => 'Peralatan Workshop',           'kategori' => 'Aktiva',     'aktivitas' => 'service',   'debit' => 15000000, 'kredit' => 0,      'keterangan' => 'Alat bengkel dan servis kendaraan'],
            ['transaksi' => 'Deposit GPS Provider',         'kategori' => 'Aktiva',     'aktivitas' => 'gps',       'debit' => 1000000,  'kredit' => 0,      'keterangan' => 'Deposit ke penyedia GPS'],
            ['transaksi' => 'Persediaan Sparepart',         'kategori' => 'Aktiva',     'aktivitas' => 'service',   'debit' => 3000000,  'kredit' => 0,      'keterangan' => 'Stok sparepart di gudang'],
            ['transaksi' => 'Gedung Kantor',                'kategori' => 'Aktiva',     'aktivitas' => 'aset',      'debit' => 500000000,'kredit' => 0,      'keterangan' => 'Nilai gedung kantor operasional'],
            ['transaksi' => 'Kendaraan Operasional',        'kategori' => 'Aktiva',     'aktivitas' => 'aset',      'debit' => 180000000,'kredit' => 0,      'keterangan' => 'Nilai armada kendaraan sewa'],

            // Modal
            ['transaksi' => 'Modal Awal Pemilik',           'kategori' => 'Modal',      'aktivitas' => 'modal',     'debit' => 0, 'kredit' => 500000000,'keterangan' => 'Setoran modal awal perusahaan'],
            ['transaksi' => 'Tambahan Modal Investasi',     'kategori' => 'Modal',      'aktivitas' => 'modal',     'debit' => 0, 'kredit' => 100000000,'keterangan' => 'Investasi tambahan dari pemilik'],
            ['transaksi' => 'Laba Ditahan Tahun Lalu',      'kategori' => 'Modal',      'aktivitas' => 'modal',     'debit' => 0, 'kredit' => 75000000, 'keterangan' => 'Akumulasi laba yang tidak dibagikan'],
            ['transaksi' => 'Dividen Dibayarkan',           'kategori' => 'Modal',      'aktivitas' => 'modal',     'debit' => 25000000,'kredit' => 0,      'keterangan' => 'Pembagian dividen kepada pemilik'],
            ['transaksi' => 'Laba Bersih Periode Berjalan', 'kategori' => 'Modal',      'aktivitas' => 'modal',     'debit' => 0, 'kredit' => 45000000, 'keterangan' => 'Laba bersih periode ini'],
            ['transaksi' => 'Cadangan Umum',                'kategori' => 'Modal',      'aktivitas' => 'modal',     'debit' => 0, 'kredit' => 10000000, 'keterangan' => 'Cadangan dana untuk ekspansi'],
            ['transaksi' => 'Prive Pemilik',                'kategori' => 'Modal',      'aktivitas' => 'modal',     'debit' => 5000000, 'kredit' => 0,      'keterangan' => 'Pengambilan pribadi pemilik'],
            ['transaksi' => 'Revaluasi Aset Kendaraan',    'kategori' => 'Modal',      'aktivitas' => 'aset',      'debit' => 0, 'kredit' => 20000000, 'keterangan' => 'Kenaikan nilai aset kendaraan'],
            ['transaksi' => 'Modal Kerja Tambahan',         'kategori' => 'Modal',      'aktivitas' => 'modal',     'debit' => 0, 'kredit' => 30000000, 'keterangan' => 'Penambahan modal kerja operasional'],
            ['transaksi' => 'Saldo Modal Berjalan',         'kategori' => 'Modal',      'aktivitas' => 'modal',     'debit' => 0, 'kredit' => 15000000, 'keterangan' => 'Saldo modal per periode ini'],

            // Kewajiban
            ['transaksi' => 'Hutang Bank Jangka Panjang',   'kategori' => 'Kewajiban',  'aktivitas' => 'hutang',    'debit' => 0, 'kredit' => 200000000,'keterangan' => 'Pinjaman bank untuk pembelian kendaraan'],
            ['transaksi' => 'Hutang Leasing Kendaraan',     'kategori' => 'Kewajiban',  'aktivitas' => 'hutang',    'debit' => 0, 'kredit' => 120000000,'keterangan' => 'Cicilan leasing kendaraan baru'],
            ['transaksi' => 'Hutang Vendor Sparepart',      'kategori' => 'Kewajiban',  'aktivitas' => 'hutang',    'debit' => 0, 'kredit' => 8000000,  'keterangan' => 'Tagihan belum dibayar ke vendor'],
            ['transaksi' => 'Hutang Pajak',                 'kategori' => 'Kewajiban',  'aktivitas' => 'pajak',     'debit' => 0, 'kredit' => 5000000,  'keterangan' => 'Kewajiban pajak yang belum dibayar'],
            ['transaksi' => 'Hutang Gaji Karyawan',         'kategori' => 'Kewajiban',  'aktivitas' => 'gaji',      'debit' => 0, 'kredit' => 15000000, 'keterangan' => 'Gaji bulan lalu yang belum dibayar'],
            ['transaksi' => 'Hutang GPS Provider',          'kategori' => 'Kewajiban',  'aktivitas' => 'gps',       'debit' => 0, 'kredit' => 900000,   'keterangan' => 'Tagihan langganan GPS yang tertunda'],
            ['transaksi' => 'Hutang Asuransi',              'kategori' => 'Kewajiban',  'aktivitas' => 'asuransi',  'debit' => 0, 'kredit' => 3000000,  'keterangan' => 'Premi asuransi yang belum dibayar'],
            ['transaksi' => 'Deposit Customer Diterima',    'kategori' => 'Kewajiban',  'aktivitas' => 'deposit',   'debit' => 0, 'kredit' => 4500000,  'keterangan' => 'Deposit yang harus dikembalikan'],
            ['transaksi' => 'Hutang Listrik dan Utilitas',  'kategori' => 'Kewajiban',  'aktivitas' => 'operasional','debit' => 0,'kredit' => 750000,   'keterangan' => 'Tagihan utilitas yang belum dibayar'],
            ['transaksi' => 'Hutang Jangka Pendek Lainnya', 'kategori' => 'Kewajiban',  'aktivitas' => 'hutang',    'debit' => 0, 'kredit' => 2000000,  'keterangan' => 'Kewajiban jangka pendek lain-lain'],
        ];

        $saldo = 0;
        foreach ($entries as $idx => $entry) {
            $saldo += $entry['debit'] - $entry['kredit'];

            Bukubesar::create([
                'kode_jurnal' => 'JRNL-' . str_pad($idx + 1, 3, '0', STR_PAD_LEFT),
                'transaksi'   => $entry['transaksi'],
                'kategori'    => $entry['kategori'],
                'tanggal'     => Carbon::now()->subDays(rand(1, 180)),
                'debit'       => $entry['debit'],
                'kredit'      => $entry['kredit'],
                'saldo'       => $saldo,
                'aktivitas'   => $entry['aktivitas'],
                'keterangan'  => $entry['keterangan'],
            ]);
        }
    }
}
