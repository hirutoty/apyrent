    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        public function up(): void
        {
            Schema::create('asuransi_kendaraan', function (Blueprint $table) {
                $table->id();

                $table->foreignId('kendaraan_id')
                    ->constrained('kendaraan')
                    ->cascadeOnDelete();

                $table->foreignId('asuransi_id')
                    ->constrained('asuransi')
                    ->cascadeOnDelete();

                $table->foreignId('jenis_asuransi_id')
                    ->constrained('jenis_asuransi')
                    ->cascadeOnDelete();

                $table->enum('status_kendaraan', [
                    'aktif',
                    'expired'
                ])->default('aktif');

                $table->date('tgl_mulai');
                $table->date('tgl_berakhir');

                // Tambahan
                $table->integer('durasi_bulan');
                $table->decimal('biaya', 15, 2);

                $table->string('bukti_bayar')->nullable();

                $table->timestamps();
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('asuransi_kendaraan');
        }
    };
