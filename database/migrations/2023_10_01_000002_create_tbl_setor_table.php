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
        if (!Schema::hasTable('tbl_setor')) {
            Schema::create('tbl_setor', function (Blueprint $table) {
                $table->integer('id_setor')->autoIncrement(); // Inferred PK
                $table->string('token', 500); // inferred length from similar tables
                $table->string('nama', 100);
                $table->string('no_rek', 100);
                $table->string('tgl', 100); // Bound as string in legacy code
                $table->decimal('nominal', 15, 2); // Bound as string but likely decimal in DB
                $table->text('terbilang');
                $table->text('berita')->nullable();
                $table->string('tujuan', 500)->nullable();
                $table->string('nama_penyetor', 100);
                $table->string('hp_penyetor', 100);
                $table->string('noid_penyetor', 100)->nullable();
                $table->text('alamat_penyetor')->nullable();
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
        Schema::dropIfExists('tbl_setor');
    }
};
