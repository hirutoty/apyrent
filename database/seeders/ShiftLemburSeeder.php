<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShiftLembur;

class ShiftLemburSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            //  nama_pegawai          shift    masuk      pulang    lembur   total   keterangan
            ['Teguh Santosa',        'Pagi',  '07:00:00','15:00:00','2',    '10',  'Lembur pengiriman barang'],
            ['Arif Budiman',         'Pagi',  '07:00:00','15:00:00',null,  '8',   'Shift reguler'],
            ['Dody Kurniawan',       'Siang', '15:00:00','23:00:00','1',   '9',   'Lembur rapat koordinasi'],
            ['Rizky Fadillah',       'Pagi',  '08:00:00','17:00:00','3',   '12',  'Lembur deploy sistem'],
            ['Yusuf Hidayat',        'Pagi',  '08:00:00','17:00:00',null,  '8',   'Shift reguler IT'],
            ['Hendra Gunawan',       'Pagi',  '08:00:00','17:00:00','2',   '11',  'Lembur maintenance server'],
            ['Wahyu Nugroho',        'Pagi',  '08:00:00','17:00:00',null,  '8',   'Shift reguler'],
            ['Fitri Handayani',      'Pagi',  '08:00:00','17:00:00','1.5', '9.5', 'Lembur laporan pajak'],
            ['Linda Permata',        'Pagi',  '08:00:00','17:00:00','2',   '10',  'Lembur audit internal'],
            ['Rini Apriani',         'Pagi',  '08:00:00','17:00:00',null,  '8',   'Shift reguler HRD'],
            ['Eko Prasetyo',         'Malam', '23:00:00','07:00:00','1',   '9',   'Shift malam + lembur'],
            ['Dewi Kusuma',          'Pagi',  '08:00:00','17:00:00',null,  '8',   'Shift reguler'],
            ['Teguh Santosa',        'Malam', '23:00:00','07:00:00','2',   '10',  'Lembur pengawasan malam'],
            ['Arif Budiman',         'Siang', '15:00:00','23:00:00',null,  '8',   'Rotasi shift siang'],
            ['Rizky Fadillah',       'Siang', '12:00:00','21:00:00','2',   '11',  'Lembur perbaikan bug produksi'],
            ['Yusuf Hidayat',        'Malam', '23:00:00','07:00:00',null,  '8',   'Shift malam on-call'],
            ['Wahyu Nugroho',        'Pagi',  '07:30:00','16:30:00','1',   '9',   'Lembur tutup buku'],
            ['Dody Kurniawan',       'Pagi',  '07:00:00','16:00:00',null,  '8',   'Shift reguler operasional'],
            ['Fitri Handayani',      'Pagi',  '08:00:00','17:00:00','2',   '10',  'Lembur SPT tahunan'],
            ['Hendra Gunawan',       'Siang', '12:00:00','21:00:00','1',   '9',   'Lembur migrasi data'],
        ];

        foreach ($data as [$nama, $shift, $masuk, $pulang, $lembur, $total, $ket]) {
            ShiftLembur::create([
                'nama_pegawai' => $nama,
                'shift'        => $shift,
                'jam_masuk'    => $masuk,
                'jam_pulang'   => $pulang,
                'jam_lembur'   => $lembur,
                'total_jam'    => $total,
                'keterangan'   => $ket,
            ]);
        }
    }
}
