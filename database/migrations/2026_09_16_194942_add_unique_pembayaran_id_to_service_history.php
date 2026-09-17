<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus duplikat lebih dulu — simpan hanya ID terkecil per pembayaran_id
        DB::statement("
            DELETE sh FROM service_history sh
            INNER JOIN (
                SELECT MIN(id) AS keep_id, pembayaran_id
                FROM service_history
                WHERE pembayaran_id IS NOT NULL
                GROUP BY pembayaran_id
                HAVING COUNT(*) > 1
            ) dup ON sh.pembayaran_id = dup.pembayaran_id
            WHERE sh.id != dup.keep_id
        ");

        // Juga hapus ServiceParts yang orphan akibat penghapusan SH di atas
        DB::statement("
            DELETE FROM service_parts
            WHERE service_history_id NOT IN (SELECT id FROM service_history)
        ");

        Schema::table('service_history', function (Blueprint $table) {
            $table->unique('pembayaran_id', 'sh_pembayaran_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('service_history', function (Blueprint $table) {
            $table->dropUnique('sh_pembayaran_id_unique');
        });
    }
};
