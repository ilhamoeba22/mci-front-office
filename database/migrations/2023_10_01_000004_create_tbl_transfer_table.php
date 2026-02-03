<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('tbl_transfer')) {
            Schema::create('tbl_transfer', function (Blueprint $table) {
                $table->integer('id_transfer')->autoIncrement();
                $table->string('token', 100)->unique(); // ON-xxxx
                $table->string('nama', 100);       // Pengirim
                $table->string('no_rek', 100);     // Pengirim
                $table->string('tgl', 100);
                $table->decimal('nominal', 15, 2);
                $table->text('terbilang');
                $table->string('tujuan', 255)->nullable();
                
                // Details Pengirim (Legacy variable mapping)
                $table->string('nama_penyetor', 100); // Di onlinekeun.php dimasukkan sebagai 'nama_penyetor'
                $table->string('hp_penyetor', 50);
                $table->text('alamat_')->nullable(); // Legacy column name 'alamat_'

                // Details Penerima
                $table->string('nama_tujuan', 100);
                $table->string('no_rek_tujuan', 100);
                $table->string('bank_tujuan', 100);
                $table->string('berita_tujuan', 255)->nullable();
                $table->text('alamat_tujuan')->nullable(); // Default '-'
                $table->string('kota_tujuan', 100)->nullable(); // Default '-'
                
                $table->decimal('biaya_trf', 15, 2)->default(0);
                $table->string('jenis_trf', 50); // 'ONLINE'
                $table->string('hp_penerima', 50)->nullable();

                $table->timestamp('created')->useCurrent();
                $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_transfer');
    }
};
