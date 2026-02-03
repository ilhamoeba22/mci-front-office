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
        // Maps to legacy 'tbl_set' used for Running Text & Video
        if (!Schema::hasTable('tbl_set')) {
            Schema::create('tbl_set', function (Blueprint $table) {
                $table->id('id_set');
                $table->string('jenis_set', 100)->index(); // 'Video', 'Text'
                $table->text('value'); // Filename or Text content
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_set');
    }
};
