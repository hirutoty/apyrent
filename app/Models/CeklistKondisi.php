<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CeklistKondisi extends Model
{
    use HasFactory;

    protected $table = 'ceklist_kondisi';

    protected $fillable = [
        'kendaraan_id',
        'nama',
        'perusahaan',
        'alamat',
        'no_telephone',
        'tanggal',
        'kilometer',
        'jam',
        'fuel',
        'pemeriksa',
        'status',
        'ratio',
        'foto_depan',
        'foto_atas',
        'foto_belakang',
        'foto_sampingkiri',
        'foto_sampingkanan',
        'foto_tambahan', // JSON array untuk multiple foto tambahan
        'ket_depan',
        'ket_atas',
        'ket_belakang',
        'ket_sampingkiri',
        'ket_sampingkanan',
        'accecoris_tambahan',
        'kondisi_mesin',
        'kondisi_rem',
        'kondisi_kopling',
        'kondisi_ban',
        'ban_depan_kanan',
        'ban_depan_kiri',
        'ban_belakang_kanan',
        'ban_belakang_kiri',
        'ban_ganda_depan_kanan',
        'ban_ganda_depan_kiri',
        'ban_ganda_belakang_kanan',
        'ban_ganda_belakang_kiri',
        'kondisi_ac',
        'kondisi_lampu',
        'lampu_sein',
        'lampu_bik_plat',
        'lampu_hazard',
        'lampu_kota',
        'lampu_besar_kecil',
        'lampu_mundur',
        'lampu_rem_belakang',
        'lampu_rem_depan',
        'accu',
        'airradiator',
        'olimesin_remkopling',
        'olipower_steering',
        'kebersihan_interior',
        'kerapihanbody_cat',
        'perlengkapan',
        'ban_cadang',
        'dongkrak',
        'kunciroda',
        'radio_tape',
        'merk',
        'karpet_lantai',
        'doproda',
        'pematikapi',
        'segitiga_pengaman',
        'kotakobat',
        'stnk',
        'kuncipintu',
        'spedoometer',
        // Section B: Pemeriksaan Khusus Truck & Keselamatan (28 items)
        'wiper_washer',
        'kaca_spion_kendaraan',
        'sabuk_pengaman',
        'ganjal_roda',
        'apar',
        'sistem_rem_angin',
        'tekanan_angin_tabung',
        'kompresor_angin_saluran',
        'brake_chamber_slack_adjuster',
        'rem_parkir_tangan',
        'transmisi_persneling',
        'gardan_kebocoran_oli',
        'kopel_propeller_universal',
        'suspensi_per_daun',
        'shock_absorber',
        'chassis_rangka_baut',
        'mur_baut_roda_velg',
        'bak_box_karoseri_lantai',
        'pintu_bak_sistem_pengunci',
        'engsel_pengait_pengaman',
        'terpal_tali_pengikat',
        // Section C: Kelengkapan Dokumen (5 items)
        'kir_berlaku',
        'sim_pengemudi',
        'sim_a',
        'sim_b1',
        'sim_b1_umum',
        'sim_b2',
        'sim_b2_umum',
        'dokumen_surat_jalan',
        'kesesuaian_muatan',
        'sopir',
        'ttd_supir',
        'mekanis',
        'ttd_mekanik',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'foto_tambahan' => 'array', // Cast JSON ke array
    ];

    /**
     * Relasi ke Kendaraan
     */
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }
}
