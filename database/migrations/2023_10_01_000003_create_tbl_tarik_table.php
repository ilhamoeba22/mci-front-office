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
        // Check if table exists to prevent errors when importing legacy SQL
        if (!Schema::hasTable('tbl_tarik')) {
            Schema::create('tbl_tarik', function (Blueprint $table) {
                $table->integer('id_tarik')->autoIncrement();
                $table->string('token', 100)->unique(); // TT-xxxx
                $table->string('nama', 100);
                $table->string('no_rek', 100);
                $table->string('tgl', 100);
                $table->decimal('nominal', 15, 2);
                $table->text('terbilang');
                $table->string('tujuan', 255)->nullable();
                
                // Matches tarikeun.php column names
                $table->string('nama_penarik', 100); 
                $table->string('hp_penarik', 50);
                $table->string('noid_penarik', 50)->nullable();
                $table->text('alamat_penarik')->nullable();
                
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
        Schema::dropIfExists('tbl_tarik');
    }
};
