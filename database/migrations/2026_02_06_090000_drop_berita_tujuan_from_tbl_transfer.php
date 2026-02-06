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
            if (Schema::hasColumn('tbl_transfer', 'berita_tujuan')) {
                $table->dropColumn('berita_tujuan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_transfer', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_transfer', 'berita_tujuan')) {
                $table->string('berita_tujuan', 255)->nullable()->after('bank_tujuan');
            }
        });
    }
};
