<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItTechnologySeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. IT Asset Management ─────────────────────────────────────────
        DB::table('itasset_management')->insert([
            ['kode_aset' => 'AST-001', 'nama_aset' => 'Laptop Dell XPS 15', 'jenis' => 'Laptop', 'lokasi' => 'Ruang IT Lt.2', 'pengguna' => 'Budi Santoso', 'merek' => 'Dell', 'tahun_beli' => 2022, 'status' => 'Aktif', 'catatan' => 'Unit utama developer', 'created_at' => now(), 'updated_at' => now()],
            ['kode_aset' => 'AST-002', 'nama_aset' => 'HP LaserJet Pro M404', 'jenis' => 'Printer', 'lokasi' => 'Ruang Admin', 'pengguna' => 'Sari Dewi', 'merek' => 'HP', 'tahun_beli' => 2021, 'status' => 'Aktif', 'catatan' => null, 'created_at' => now(), 'updated_at' => now()],
            ['kode_aset' => 'AST-003', 'nama_aset' => 'MacBook Pro M2', 'jenis' => 'Laptop', 'lokasi' => 'Ruang Desain', 'pengguna' => 'Andi Wijaya', 'merek' => 'Apple', 'tahun_beli' => 2023, 'status' => 'Aktif', 'catatan' => 'Untuk tim desain grafis', 'created_at' => now(), 'updated_at' => now()],
            ['kode_aset' => 'AST-004', 'nama_aset' => 'Monitor LG 27 Inch 4K', 'jenis' => 'Monitor', 'lokasi' => 'Ruang IT Lt.2', 'pengguna' => 'Rudi Hermawan', 'merek' => 'LG', 'tahun_beli' => 2022, 'status' => 'Aktif', 'catatan' => null, 'created_at' => now(), 'updated_at' => now()],
            ['kode_aset' => 'AST-005', 'nama_aset' => 'Cisco Switch 48 Port', 'jenis' => 'Network', 'lokasi' => 'Ruang Server', 'pengguna' => 'Tim IT', 'merek' => 'Cisco', 'tahun_beli' => 2020, 'status' => 'Rusak', 'catatan' => 'Port 12-15 tidak berfungsi', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── 2. Software License ────────────────────────────────────────────
        DB::table('software_licenses')->insert([
            ['nama_software' => 'Microsoft Office 365', 'jenis_lisensi' => 'Subscription', 'jumlah_lisensi' => 25, 'provider' => 'Microsoft', 'masa_berlaku' => '2025-12-31', 'status' => 'Aktif', 'tanggal_perpanjangan' => '2024-12-01', 'created_at' => now(), 'updated_at' => now()],
            ['nama_software' => 'Adobe Creative Cloud', 'jenis_lisensi' => 'Subscription', 'jumlah_lisensi' => 5, 'provider' => 'Adobe', 'masa_berlaku' => '2025-06-30', 'status' => 'Aktif', 'tanggal_perpanjangan' => null, 'created_at' => now(), 'updated_at' => now()],
            ['nama_software' => 'Kaspersky Endpoint Security', 'jenis_lisensi' => 'Perpetual', 'jumlah_lisensi' => 50, 'provider' => 'Kaspersky', 'masa_berlaku' => '2024-03-31', 'status' => 'Expired', 'tanggal_perpanjangan' => '2024-04-01', 'created_at' => now(), 'updated_at' => now()],
            ['nama_software' => 'Zoom Pro', 'jenis_lisensi' => 'Subscription', 'jumlah_lisensi' => 10, 'provider' => 'Zoom', 'masa_berlaku' => '2025-09-30', 'status' => 'Aktif', 'tanggal_perpanjangan' => null, 'created_at' => now(), 'updated_at' => now()],
            ['nama_software' => 'AutoCAD 2024', 'jenis_lisensi' => 'Perpetual', 'jumlah_lisensi' => 3, 'provider' => 'Autodesk', 'masa_berlaku' => '2026-01-01', 'status' => 'Aktif', 'tanggal_perpanjangan' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── 3. Helpdesk Support ────────────────────────────────────────────
        DB::table('helpdesk_supports')->insert([
            ['no_tiket' => 'TKT-001', 'tanggal' => '2025-01-10', 'departemen' => 'Finance', 'masalah' => 'Laptop tidak bisa menyala setelah update Windows', 'prioritas' => 'High', 'status' => 'Resolved', 'teknisi' => 'Doni Prasetyo', 'waktu_respon' => '2 jam', 'created_at' => now(), 'updated_at' => now()],
            ['no_tiket' => 'TKT-002', 'tanggal' => '2025-01-15', 'departemen' => 'HR', 'masalah' => 'Email tidak bisa terkirim ke luar domain', 'prioritas' => 'Medium', 'status' => 'Open', 'teknisi' => 'Siti Rahayu', 'waktu_respon' => '4 jam', 'created_at' => now(), 'updated_at' => now()],
            ['no_tiket' => 'TKT-003', 'tanggal' => '2025-01-20', 'departemen' => 'Sales', 'masalah' => 'Koneksi VPN terputus saat WFH', 'prioritas' => 'Critical', 'status' => 'In Progress', 'teknisi' => 'Doni Prasetyo', 'waktu_respon' => '30 menit', 'created_at' => now(), 'updated_at' => now()],
            ['no_tiket' => 'TKT-004', 'tanggal' => '2025-02-01', 'departemen' => 'IT', 'masalah' => 'Printer di lantai 3 tidak terdeteksi oleh komputer', 'prioritas' => 'Low', 'status' => 'Closed', 'teknisi' => 'Siti Rahayu', 'waktu_respon' => '1 hari', 'created_at' => now(), 'updated_at' => now()],
            ['no_tiket' => 'TKT-005', 'tanggal' => '2025-02-10', 'departemen' => 'Operasional', 'masalah' => 'Sistem ERP lambat saat jam kerja puncak', 'prioritas' => 'High', 'status' => 'In Progress', 'teknisi' => 'Doni Prasetyo', 'waktu_respon' => '1 jam', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── 4. User Access ─────────────────────────────────────────────────
        DB::table('user_accesses')->insert([
            ['nama_pengguna' => 'Budi Santoso', 'divisi' => 'IT', 'role_akses' => 'Administrator', 'sistem' => 'ERP Sistem', 'status_akses' => 'Aktif', 'catatan' => 'Admin utama sistem', 'created_at' => now(), 'updated_at' => now()],
            ['nama_pengguna' => 'Sari Dewi', 'divisi' => 'Finance', 'role_akses' => 'Read-Write', 'sistem' => 'Accounting Software', 'status_akses' => 'Aktif', 'catatan' => null, 'created_at' => now(), 'updated_at' => now()],
            ['nama_pengguna' => 'Rudi Hermawan', 'divisi' => 'HR', 'role_akses' => 'Read Only', 'sistem' => 'HRIS', 'status_akses' => 'Aktif', 'catatan' => 'Akses terbatas laporan', 'created_at' => now(), 'updated_at' => now()],
            ['nama_pengguna' => 'Dewi Cahyani', 'divisi' => 'Sales', 'role_akses' => 'User', 'sistem' => 'CRM', 'status_akses' => 'Nonaktif', 'catatan' => 'Karyawan resign Desember 2024', 'created_at' => now(), 'updated_at' => now()],
            ['nama_pengguna' => 'Anto Nugroho', 'divisi' => 'Operasional', 'role_akses' => 'Read-Write', 'sistem' => 'ERP Sistem', 'status_akses' => 'Suspended', 'catatan' => 'Akses ditangguhkan sementara', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── 5. Network Monitoring ──────────────────────────────────────────
        DB::table('network_monitorings')->insert([
            ['lokasi' => 'Kantor Pusat Jakarta', 'ip_public' => '103.12.45.67', 'status_koneksi' => 'Online', 'bandwidth' => '500 Mbps', 'downtime' => '0 jam/bulan', 'catatan' => 'Koneksi utama Indihome Business', 'created_at' => now(), 'updated_at' => now()],
            ['lokasi' => 'Cabang Surabaya', 'ip_public' => '202.67.88.12', 'status_koneksi' => 'Online', 'bandwidth' => '100 Mbps', 'downtime' => '2 jam/bulan', 'catatan' => null, 'created_at' => now(), 'updated_at' => now()],
            ['lokasi' => 'Gudang Bekasi', 'ip_public' => '180.244.33.91', 'status_koneksi' => 'Warning', 'bandwidth' => '50 Mbps', 'downtime' => '5 jam/bulan', 'catatan' => 'Sering gangguan sore hari', 'created_at' => now(), 'updated_at' => now()],
            ['lokasi' => 'Data Center Cibitung', 'ip_public' => '103.88.12.200', 'status_koneksi' => 'Online', 'bandwidth' => '1 Gbps', 'downtime' => '0 jam/bulan', 'catatan' => 'Tier 3 data center', 'created_at' => now(), 'updated_at' => now()],
            ['lokasi' => 'Cabang Bandung', 'ip_public' => '36.91.44.111', 'status_koneksi' => 'Offline', 'bandwidth' => '100 Mbps', 'downtime' => '8 jam/bulan', 'catatan' => 'Sedang dalam perbaikan jalur fiber', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── 6. Cybersecurity ───────────────────────────────────────────────
        DB::table('cybersecurities')->insert([
            ['tanggal_audit' => '2025-01-05', 'area_diaudit' => 'Web Application', 'temuan_risiko' => 'SQL Injection pada form login admin panel', 'level_risiko' => 'Critical', 'tindakan_perbaikan' => 'Input sanitasi dan prepared statement diterapkan', 'status' => 'Resolved', 'created_at' => now(), 'updated_at' => now()],
            ['tanggal_audit' => '2025-01-20', 'area_diaudit' => 'Jaringan Internal', 'temuan_risiko' => 'Port 23 (Telnet) masih terbuka di beberapa switch', 'level_risiko' => 'High', 'tindakan_perbaikan' => 'Disable Telnet dan aktifkan SSH pada semua perangkat jaringan', 'status' => 'In Progress', 'created_at' => now(), 'updated_at' => now()],
            ['tanggal_audit' => '2025-02-10', 'area_diaudit' => 'Email Server', 'temuan_risiko' => 'Tidak ada SPF dan DMARC record, rentan email spoofing', 'level_risiko' => 'Medium', 'tindakan_perbaikan' => 'Konfigurasi SPF, DKIM, dan DMARC pada DNS', 'status' => 'Open', 'created_at' => now(), 'updated_at' => now()],
            ['tanggal_audit' => '2025-02-25', 'area_diaudit' => 'Endpoint Security', 'temuan_risiko' => '12 komputer belum update antivirus selama 3 bulan', 'level_risiko' => 'Low', 'tindakan_perbaikan' => 'Update antivirus terpusat via console management', 'status' => 'Resolved', 'created_at' => now(), 'updated_at' => now()],
            ['tanggal_audit' => '2025-03-01', 'area_diaudit' => 'Database Server', 'temuan_risiko' => 'Akun root database dapat diakses dari remote tanpa restriksi IP', 'level_risiko' => 'Critical', 'tindakan_perbaikan' => 'Batasi akses root hanya dari localhost, buat user terbatas', 'status' => 'Open', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── 7. Email & Domain ──────────────────────────────────────────────
        DB::table('email_domains')->insert([
            ['nama_domain' => 'perusahaan.com', 'provider' => 'GoDaddy', 'status' => 'aktif', 'expired_date' => '2026-08-15', 'email_aktif' => 120, 'dns_terkelola' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_domain' => 'perusahaan.co.id', 'provider' => 'Rumahweb', 'status' => 'aktif', 'expired_date' => '2025-11-30', 'email_aktif' => 45, 'dns_terkelola' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_domain' => 'old-brand.com', 'provider' => 'Namecheap', 'status' => 'nonaktif', 'expired_date' => '2024-03-10', 'email_aktif' => 0, 'dns_terkelola' => false, 'created_at' => now(), 'updated_at' => now()],
            ['nama_domain' => 'app.perusahaan.com', 'provider' => 'Cloudflare', 'status' => 'aktif', 'expired_date' => '2027-01-01', 'email_aktif' => 0, 'dns_terkelola' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── 8. Server & Cloud ──────────────────────────────────────────────
        DB::table('server_clouds')->insert([
            ['nama_server' => 'web-server-01', 'jenis_server' => 'Cloud', 'lokasi' => 'AWS ap-southeast-1', 'os' => 'Ubuntu 22.04 LTS', 'provider_cloud' => 'AWS', 'status' => 'Aktif', 'backup_aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_server' => 'db-server-01', 'jenis_server' => 'Cloud', 'lokasi' => 'AWS ap-southeast-1', 'os' => 'Amazon Linux 2', 'provider_cloud' => 'AWS', 'status' => 'Aktif', 'backup_aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_server' => 'file-server-local', 'jenis_server' => 'Physical', 'lokasi' => 'Data Center Cibitung', 'os' => 'Windows Server 2022', 'provider_cloud' => null, 'status' => 'Aktif', 'backup_aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_server' => 'dev-server-01', 'jenis_server' => 'VPS', 'lokasi' => 'Niagahoster VPS', 'os' => 'Ubuntu 20.04 LTS', 'provider_cloud' => 'Niagahoster', 'status' => 'Aktif', 'backup_aktif' => false, 'created_at' => now(), 'updated_at' => now()],
            ['nama_server' => 'backup-server-01', 'jenis_server' => 'Physical', 'lokasi' => 'Ruang Server Jakarta', 'os' => 'CentOS 7', 'provider_cloud' => null, 'status' => 'Maintenance', 'backup_aktif' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── 9. System Backup ───────────────────────────────────────────────
        DB::table('system_backups')->insert([
            ['sistem' => 'Database ERP Production', 'metode_backup' => 'Full', 'frekuensi' => 'Harian', 'lokasi_backup' => 'AWS S3 Bucket', 'status_backup' => 'Aktif', 'uji_restore_terakhir' => '2025-01-15', 'created_at' => now(), 'updated_at' => now()],
            ['sistem' => 'File Server Dokumen', 'metode_backup' => 'Incremental', 'frekuensi' => 'Mingguan', 'lokasi_backup' => 'NAS Lokal + Cloud', 'status_backup' => 'Aktif', 'uji_restore_terakhir' => '2024-12-01', 'created_at' => now(), 'updated_at' => now()],
            ['sistem' => 'Email Server', 'metode_backup' => 'Full', 'frekuensi' => 'Mingguan', 'lokasi_backup' => 'Tape Drive', 'status_backup' => 'Aktif', 'uji_restore_terakhir' => '2025-02-01', 'created_at' => now(), 'updated_at' => now()],
            ['sistem' => 'Aplikasi HRIS', 'metode_backup' => 'Differential', 'frekuensi' => 'Harian', 'lokasi_backup' => 'GCP Storage', 'status_backup' => 'Gagal', 'uji_restore_terakhir' => null, 'created_at' => now(), 'updated_at' => now()],
            ['sistem' => 'Website Company Profile', 'metode_backup' => 'Full', 'frekuensi' => 'Bulanan', 'lokasi_backup' => 'Hosting cPanel', 'status_backup' => 'Nonaktif', 'uji_restore_terakhir' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── 10. Project Management ─────────────────────────────────────────
        DB::table('project_management')->insert([
            ['nama_proyek' => 'Migrasi ERP ke Cloud', 'pic_proyek' => 'Budi Santoso', 'tujuan' => 'Memindahkan seluruh infrastruktur ERP dari on-premise ke cloud AWS untuk meningkatkan skalabilitas', 'estimasi_waktu' => '6 bulan', 'status' => 'In Progress', 'progres' => 45, 'created_at' => now(), 'updated_at' => now()],
            ['nama_proyek' => 'Implementasi SSO Perusahaan', 'pic_proyek' => 'Doni Prasetyo', 'tujuan' => 'Implementasi Single Sign-On untuk semua sistem internal menggunakan Keycloak', 'estimasi_waktu' => '3 bulan', 'status' => 'Selesai', 'progres' => 100, 'created_at' => now(), 'updated_at' => now()],
            ['nama_proyek' => 'Pengembangan Mobile App Driver', 'pic_proyek' => 'Andi Wijaya', 'tujuan' => 'Membuat aplikasi mobile untuk monitoring dan tracking kendaraan operasional', 'estimasi_waktu' => '4 bulan', 'status' => 'In Progress', 'progres' => 30, 'created_at' => now(), 'updated_at' => now()],
            ['nama_proyek' => 'Upgrade Infrastruktur Jaringan', 'pic_proyek' => 'Rudi Hermawan', 'tujuan' => 'Upgrade seluruh perangkat jaringan kantor pusat ke standar 10 Gbps', 'estimasi_waktu' => '2 bulan', 'status' => 'Pending', 'progres' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['nama_proyek' => 'Implementasi WAF', 'pic_proyek' => 'Budi Santoso', 'tujuan' => 'Pemasangan Web Application Firewall untuk semua endpoint API publik', 'estimasi_waktu' => '1 bulan', 'status' => 'Selesai', 'progres' => 100, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── 11. DevOps ─────────────────────────────────────────────────────
        DB::table('devops')->insert([
            ['aplikasi' => 'API Backend ERP', 'tools' => 'GitHub Actions', 'deployment_otomatis' => 'Ya', 'jadwal_build' => 'Setiap push ke branch main', 'status' => 'Aktif', 'created_at' => now(), 'updated_at' => now()],
            ['aplikasi' => 'Frontend Dashboard', 'tools' => 'GitLab CI', 'deployment_otomatis' => 'Ya', 'jadwal_build' => 'Setiap merge request approved', 'status' => 'Aktif', 'created_at' => now(), 'updated_at' => now()],
            ['aplikasi' => 'Mobile App Driver', 'tools' => 'Bitrise', 'deployment_otomatis' => 'Tidak', 'jadwal_build' => 'Manual oleh tim mobile', 'status' => 'Aktif', 'created_at' => now(), 'updated_at' => now()],
            ['aplikasi' => 'Laporan Keuangan', 'tools' => 'Jenkins', 'deployment_otomatis' => 'Ya', 'jadwal_build' => 'Setiap hari pukul 02.00 WIB', 'status' => 'Nonaktif', 'created_at' => now(), 'updated_at' => now()],
            ['aplikasi' => 'Website Company Profile', 'tools' => 'GitHub Actions', 'deployment_otomatis' => 'Ya', 'jadwal_build' => 'Setiap push ke branch production', 'status' => 'Aktif', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── 12. Policy & Compliance ────────────────────────────────────────
        DB::table('policy_compliances')->insert([
            ['nama_dokumen' => 'Kebijakan Keamanan Informasi', 'versi' => 'v2.1', 'tanggal_berlaku' => '2024-01-01', 'tanggung_jawab' => 'IT Manager', 'status' => 'Aktif', 'sertifikasi_terkait' => 'ISO 27001', 'created_at' => now(), 'updated_at' => now()],
            ['nama_dokumen' => 'Prosedur Backup & Recovery', 'versi' => 'v1.3', 'tanggal_berlaku' => '2024-03-01', 'tanggung_jawab' => 'System Administrator', 'status' => 'Aktif', 'sertifikasi_terkait' => null, 'created_at' => now(), 'updated_at' => now()],
            ['nama_dokumen' => 'Kebijakan Penggunaan Aset IT', 'versi' => 'v1.0', 'tanggal_berlaku' => '2024-06-01', 'tanggung_jawab' => 'HR & IT Manager', 'status' => 'Draft', 'sertifikasi_terkait' => null, 'created_at' => now(), 'updated_at' => now()],
            ['nama_dokumen' => 'Disaster Recovery Plan', 'versi' => 'v3.0', 'tanggal_berlaku' => '2023-07-01', 'tanggung_jawab' => 'CTO', 'status' => 'Review', 'sertifikasi_terkait' => 'ISO 22301', 'created_at' => now(), 'updated_at' => now()],
            ['nama_dokumen' => 'Kebijakan Password & Akses', 'versi' => 'v2.0', 'tanggal_berlaku' => '2024-01-01', 'tanggung_jawab' => 'IT Security Officer', 'status' => 'Aktif', 'sertifikasi_terkait' => 'ISO 27001', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
