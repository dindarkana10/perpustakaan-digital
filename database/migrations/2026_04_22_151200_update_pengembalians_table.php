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
        Schema::table('pengembalians', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade')->after('peminjaman_id');
        });

        // Update status_pembayaran enum
        DB::statement("ALTER TABLE pengembalians MODIFY COLUMN status_pembayaran ENUM('belum_lunas', 'lunas', 'tidak_ada_denda') NOT NULL DEFAULT 'belum_lunas'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengembalians', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });

        DB::statement("ALTER TABLE pengembalians MODIFY COLUMN status_pembayaran ENUM('lunas', 'belum_lunas') NOT NULL DEFAULT 'belum_lunas'");
    }
};
