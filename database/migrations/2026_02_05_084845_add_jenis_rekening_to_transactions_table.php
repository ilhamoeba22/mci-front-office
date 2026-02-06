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
        Schema::table('tbl_setor', function (Blueprint $table) {
            $table->string('jenis_rekening')->nullable()->after('no_rek');
        });

        Schema::table('tbl_tarik', function (Blueprint $table) {
            $table->string('jenis_rekening')->nullable()->after('no_rek');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_setor', function (Blueprint $table) {
            $table->dropColumn('jenis_rekening');
        });

        Schema::table('tbl_tarik', function (Blueprint $table) {
            $table->dropColumn('jenis_rekening');
        });
    }
};
