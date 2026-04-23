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
        Schema::table('detail_pengembalians', function (Blueprint $table) {
            $table->decimal('denda_kerusakan_buku', 10, 2)->default(0)->after('kondisi_kembali');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_pengembalians', function (Blueprint $table) {
            $table->dropColumn('denda_kerusakan_buku');
        });
    }
};
