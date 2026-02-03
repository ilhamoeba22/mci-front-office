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
        if (!Schema::hasTable('tbl_bank')) {
            Schema::create('tbl_bank', function (Blueprint $table) {
                $table->id(); // Standard ID
                $table->string('nama_bank', 100);
                $table->decimal('biaya_trf', 15, 2);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_bank');
    }
};
