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
            $table->renameColumn('alamat_', 'alamat_penyetor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_transfer', function (Blueprint $table) {
            $table->renameColumn('alamat_penyetor', 'alamat_');
        });
    }
};
