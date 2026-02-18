<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change table collation for tbl_set to support emojis (utf8mb4)
        // Fix for Error 3988: Conversion from collation utf8mb4_unicode_ci into utf8mb3_general_ci impossible
        
        DB::statement('ALTER TABLE tbl_set CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        
        // Ensure the value column is also explicitly set to TEXT with correct collation
        DB::statement('ALTER TABLE tbl_set MODIFY value TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE tbl_set CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');
        DB::statement('ALTER TABLE tbl_set MODIFY value TEXT CHARACTER SET utf8 COLLATE utf8_general_ci');
    }
};
