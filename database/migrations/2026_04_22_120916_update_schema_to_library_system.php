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
        // 1. Update Users Table
        Schema::table('users', function (Blueprint $blueprint) {
            $blueprint->string('NISN')->nullable()->unique()->after('password');
            $blueprint->string('kelas_jurusan')->nullable()->after('NISN');
            // Role update is tricky with enum. We might need to drop and recreate or use string.
        });
        
        // Change enum role (using raw DB for better compatibility)
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'peminjam') NOT NULL DEFAULT 'peminjam'");

        // 2. Rename Kategoris to KategoriBukus
        Schema::rename('kategoris', 'kategori_bukus');

        // 3. Rename Alats to Bukus
        Schema::rename('alats', 'bukus');

        // 4. Update Bukus Table
        Schema::table('bukus', function (Blueprint $table) {
            $table->renameColumn('nama_alat', 'judul_buku');
            $table->renameColumn('stok_total', 'stok');
            $table->renameColumn('harga_beli', 'harga_buku');
            $table->renameColumn('kategori_id', 'kategori_buku_id');
        });

        Schema::table('bukus', function (Blueprint $table) {
            $table->string('penulis')->after('judul_buku');
            $table->string('penerbit')->after('penulis');
            $table->year('tahun_terbit')->after('penerbit');
            $table->string('ISBN')->nullable()->after('tahun_terbit');
        });

        // 5. Update Foreign Keys in other tables
        Schema::table('peminjamen', function (Blueprint $table) {
            // No column rename needed for peminjamen if it doesn't have alat_id directly (it uses details)
        });

        Schema::table('detail_peminjamen', function (Blueprint $table) {
            $table->renameColumn('alat_id', 'buku_id');
        });

        Schema::table('detail_pengembalians', function (Blueprint $table) {
            $table->renameColumn('alat_id', 'buku_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_pengembalians', function (Blueprint $table) {
            $table->renameColumn('buku_id', 'alat_id');
        });

        Schema::table('detail_peminjamen', function (Blueprint $table) {
            $table->renameColumn('buku_id', 'alat_id');
        });

        Schema::table('bukus', function (Blueprint $table) {
            $table->renameColumn('kategori_buku_id', 'kategori_id');
            $table->renameColumn('harga_buku', 'harga_beli');
            $table->renameColumn('stok', 'stok_total');
            $table->dropColumn(['ISBN', 'tahun_terbit', 'penerbit', 'penulis']);
            $table->renameColumn('judul_buku', 'nama_alat');
        });

        Schema::rename('bukus', 'alats');
        Schema::rename('kategori_bukus', 'kategoris');

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'petugas', 'peminjam') NOT NULL");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['kelas_jurusan', 'NISN']);
        });
    }
};
