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
        Schema::table('tbl_antrian', function (Blueprint $table) {
            $table->string('nama', 100)->nullable()->after('nama_antrian'); // Optional Name
            $table->string('no_kontak', 20)->nullable()->after('nama'); // Optional Contact
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_antrian', function (Blueprint $table) {
            $table->dropColumn(['nama', 'no_kontak']);
        });
    }
};
