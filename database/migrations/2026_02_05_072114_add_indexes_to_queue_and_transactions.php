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
        // Add Index to 'kode' string in tbl_antrian
        Schema::table('tbl_antrian', function (Blueprint $table) {
            $table->index('kode'); 
        });

        // Add Index to 'token' in transaction tables
        Schema::table('tbl_setor', function (Blueprint $table) {
            $table->index('token');
        });
        Schema::table('tbl_tarik', function (Blueprint $table) {
            $table->index('token');
        });
        Schema::table('tbl_transfer', function (Blueprint $table) {
            $table->index('token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_antrian', function (Blueprint $table) {
            $table->dropIndex(['kode']);
        });
        Schema::table('tbl_setor', function (Blueprint $table) {
            $table->dropIndex(['token']);
        });
        Schema::table('tbl_tarik', function (Blueprint $table) {
            $table->dropIndex(['token']);
        });
        Schema::table('tbl_transfer', function (Blueprint $table) {
            $table->dropIndex(['token']);
        });
    }
};
