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
        if (!Schema::hasTable('tbl_antrian')) {
            Schema::create('tbl_antrian', function (Blueprint $table) {
                $table->integer('id_antrian')->autoIncrement();
                $table->string('nama_antrian', 100);
                $table->date('tgl_antri');
                $table->string('type', 100);
                $table->string('antrian', 100);
                $table->string('kode', 100);
                $table->string('st_antrian', 10)->default('0');
                $table->text('tujuan_datang')->nullable();
                $table->text('solusi')->nullable();
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
        Schema::dropIfExists('tbl_antrian');
    }
};
