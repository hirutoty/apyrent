<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Drop semua tabel lama ──────────────────────────────────────────
        Schema::dropIfExists('itasset_management');
        Schema::dropIfExists('software_licenses');
        Schema::dropIfExists('helpdesk_supports');
        Schema::dropIfExists('user_accesses');
        Schema::dropIfExists('network_monitorings');
        Schema::dropIfExists('cybersecurities');
        Schema::dropIfExists('email_domains');
        Schema::dropIfExists('server_clouds');
        Schema::dropIfExists('system_backups');
        Schema::dropIfExists('project_management');
        Schema::dropIfExists('devops');
        Schema::dropIfExists('policy_compliances');

        // ── 1. IT Asset Management ─────────────────────────────────────────
        Schema::create('itasset_management', function (Blueprint $table) {
            $table->id();
            $table->string('kode_aset');
            $table->string('nama_aset');
            $table->string('jenis');
            $table->string('lokasi');
            $table->string('pengguna');
            $table->string('merek');
            $table->year('tahun_beli');
            $table->string('status');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // ── 2. Software License ────────────────────────────────────────────
        Schema::create('software_licenses', function (Blueprint $table) {
            $table->id();
            $table->string('nama_software');
            $table->string('jenis_lisensi');
            $table->integer('jumlah_lisensi');
            $table->string('provider');
            $table->date('masa_berlaku');
            $table->string('status');
            $table->date('tanggal_perpanjangan')->nullable();
            $table->timestamps();
        });

        // ── 3. Helpdesk Support ────────────────────────────────────────────
        Schema::create('helpdesk_supports', function (Blueprint $table) {
            $table->id();
            $table->string('no_tiket');
            $table->date('tanggal');
            $table->string('departemen');
            $table->text('masalah');
            $table->string('prioritas');
            $table->string('status');
            $table->string('teknisi');
            $table->string('waktu_respon');
            $table->timestamps();
        });

        // ── 4. User Access ─────────────────────────────────────────────────
        Schema::create('user_accesses', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pengguna');
            $table->string('divisi');
            $table->string('role_akses');
            $table->string('sistem');
            $table->string('status_akses');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // ── 5. Network Monitoring ──────────────────────────────────────────
        Schema::create('network_monitorings', function (Blueprint $table) {
            $table->id();
            $table->string('lokasi');
            $table->string('ip_public');
            $table->string('status_koneksi');
            $table->string('bandwidth');
            $table->string('downtime');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // ── 6. Cybersecurity ───────────────────────────────────────────────
        Schema::create('cybersecurities', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_audit');
            $table->string('area_diaudit');
            $table->text('temuan_risiko');
            $table->string('level_risiko');
            $table->text('tindakan_perbaikan');
            $table->string('status');
            $table->timestamps();
        });

        // ── 7. Email & Domain ──────────────────────────────────────────────
        Schema::create('email_domains', function (Blueprint $table) {
            $table->id();
            $table->string('nama_domain');
            $table->string('provider');
            $table->string('status');
            $table->date('expired_date')->nullable();
            $table->integer('email_aktif')->default(0);
            $table->boolean('dns_terkelola')->default(false);
            $table->timestamps();
        });

        // ── 8. Server & Cloud ──────────────────────────────────────────────
        Schema::create('server_clouds', function (Blueprint $table) {
            $table->id();
            $table->string('nama_server');
            $table->string('jenis_server');
            $table->string('lokasi');
            $table->string('os');
            $table->string('provider_cloud')->nullable();
            $table->string('status');
            $table->boolean('backup_aktif')->default(false);
            $table->timestamps();
        });

        // ── 9. System Backup ───────────────────────────────────────────────
        Schema::create('system_backups', function (Blueprint $table) {
            $table->id();
            $table->string('sistem');
            $table->string('metode_backup');
            $table->string('frekuensi');
            $table->string('lokasi_backup');
            $table->string('status_backup');
            $table->date('uji_restore_terakhir')->nullable();
            $table->timestamps();
        });

        // ── 10. Project Management ─────────────────────────────────────────
        Schema::create('project_management', function (Blueprint $table) {
            $table->id();
            $table->string('nama_proyek');
            $table->string('pic_proyek');
            $table->text('tujuan');
            $table->string('estimasi_waktu');
            $table->string('status');
            $table->integer('progres')->default(0);
            $table->timestamps();
        });

        // ── 11. DevOps ─────────────────────────────────────────────────────
        Schema::create('devops', function (Blueprint $table) {
            $table->id();
            $table->string('aplikasi');
            $table->string('tools');
            $table->string('deployment_otomatis'); // Ya / Tidak
            $table->string('jadwal_build');
            $table->string('status'); // Aktif / Nonaktif
            $table->timestamps();
        });

        // ── 12. Policy & Compliance ────────────────────────────────────────
        Schema::create('policy_compliances', function (Blueprint $table) {
            $table->id();
            $table->string('nama_dokumen');
            $table->string('versi');
            $table->date('tanggal_berlaku');
            $table->string('tanggung_jawab');
            $table->string('status');
            $table->string('sertifikasi_terkait')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itasset_management');
        Schema::dropIfExists('software_licenses');
        Schema::dropIfExists('helpdesk_supports');
        Schema::dropIfExists('user_accesses');
        Schema::dropIfExists('network_monitorings');
        Schema::dropIfExists('cybersecurities');
        Schema::dropIfExists('email_domains');
        Schema::dropIfExists('server_clouds');
        Schema::dropIfExists('system_backups');
        Schema::dropIfExists('project_management');
        Schema::dropIfExists('devops');
        Schema::dropIfExists('policy_compliances');
    }
};
