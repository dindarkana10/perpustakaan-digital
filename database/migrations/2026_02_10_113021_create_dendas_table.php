<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dendas', function (Blueprint $table) {
            $table->id();
            $table->decimal('denda_per_hari', 10, 2)->default(1000);
            $table->integer('denda_rusak_ringan')->default(10);
            $table->integer('denda_rusak_berat')->default(50);
            $table->integer('persentase_penggantian_hilang')->default(100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dendas');
    }
};