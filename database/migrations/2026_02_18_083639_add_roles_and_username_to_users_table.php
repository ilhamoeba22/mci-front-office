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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'teller', 'cs'])->default('teller')->after('email');
            }
            if (!Schema::hasColumn('users', 'counter_no')) {
                $table->integer('counter_no')->nullable()->after('role');
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('counter_no');
            }
        });

        // Sinkronisasi data awal: Set username dari email untuk user yang sudah ada
        \Illuminate\Support\Facades\DB::table('users')->whereNull('username')->orWhere('username', '')->get()->each(function ($user) {
            $prefix = explode('@', $user->email)[0] ?? 'user_' . $user->id;
            $username = $prefix;
            $counter = 1;
            
            // Hindari duplikasi username jika ada email yang mirip
            while (\Illuminate\Support\Facades\DB::table('users')->where('username', $username)->exists()) {
                $username = $prefix . $counter++;
            }
            
            \Illuminate\Support\Facades\DB::table('users')->where('id', $user->id)->update(['username' => $username]);
        });

        // Sekarang tambahkan constraint unique setelah data sudah terisi
        Schema::table('users', function (Blueprint $table) {
            // Kita gunakan try-catch atau pengecekan index jika ingin lebih aman, 
            // tapi change() + unique() biasanya cukup jika data sudah bersih.
            $table->string('username')->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'role', 'counter_no', 'is_active']);
        });
    }
};
