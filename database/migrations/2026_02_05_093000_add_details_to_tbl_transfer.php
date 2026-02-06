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
        Schema::table('tbl_transfer', function (Blueprint $table) {
            $table->string('negara_tujuan')->nullable()->after('berita_tujuan');
            $table->string('metode_transfer')->nullable()->after('negara_tujuan'); // SKN, RTGS, TT
            $table->string('mata_uang')->nullable()->after('metode_transfer'); // IDR, USD, etc
            $table->string('sumber_dana')->nullable()->after('mata_uang'); // Tunai, Debet, Kliring
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_transfer', function (Blueprint $table) {
            $table->dropColumn(['negara_tujuan', 'metode_transfer', 'mata_uang', 'sumber_dana']);
        });
    }
};
