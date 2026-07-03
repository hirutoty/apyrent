<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [

            [
                'nama_member'   => 'Budi Santoso',
                'kontak_member' => '081234567890',
                'alamat'        => 'Wonosobo, Jawa Tengah',
            ],

            [
                'nama_member'   => 'Rina Permata',
                'kontak_member' => '082145678901',
                'alamat'        => 'Magelang, Jawa Tengah',
            ],

            [
                'nama_member'   => 'Agus Setiawan',
                'kontak_member' => '083156789012',
                'alamat'        => 'Temanggung, Jawa Tengah',
            ],

            [
                'nama_member'   => 'Dewi Lestari',
                'kontak_member' => '085267890123',
                'alamat'        => 'Banjarnegara, Jawa Tengah',
            ],

            [
                'nama_member'   => 'Fajar Hidayat',
                'kontak_member' => '087878901234',
                'alamat'        => 'Purwokerto, Jawa Tengah',
            ],

        ];

        foreach ($data as $item) {
            Member::create($item);
        }
    }
}