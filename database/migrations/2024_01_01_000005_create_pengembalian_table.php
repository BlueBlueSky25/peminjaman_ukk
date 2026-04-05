<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Buat ENUM type untuk status_denda (abaikan jika sudah ada)
        DB::statement("DO $$ BEGIN CREATE TYPE status_denda AS ENUM ('lunas', 'belum_lunas'); EXCEPTION WHEN duplicate_object THEN null; END $$");

        if (Schema::hasTable('pengembalian')) return;

        Schema::create('pengembalian', function (Blueprint $table) {
            $table->increments('pengembalian_id');
            $table->unsignedInteger('peminjaman_id');
            $table->date('tanggal_kembali_aktual');
            $table->integer('keterlambatan_hari')->default(0);
            $table->decimal('tarif_denda_per_hari', 10, 2)->nullable();
            $table->decimal('total_denda', 10, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->date('tanggal_bayar_denda')->nullable();
            $table->unsignedInteger('diverifikasi_oleh')->nullable();
            $table->text('keterangan_pembayaran')->nullable();
            $table->integer('jumlah_dikembalikan')->default(1);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign('peminjaman_id')
                  ->references('peminjaman_id')->on('peminjaman')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            $table->foreign('diverifikasi_oleh')
                  ->references('user_id')->on('users');

            $table->index('peminjaman_id', 'idx_pengembalian_peminjaman');
        });

        // Tambah kolom ENUM secara manual (PostgreSQL)
        DB::statement("ALTER TABLE pengembalian ADD COLUMN kondisi_alat kondisi_alat NOT NULL");
        DB::statement("ALTER TABLE pengembalian ADD COLUMN status_denda status_denda DEFAULT 'belum_lunas'::status_denda");
    }

    public function down(): void
    {
        Schema::dropIfExists('pengembalian');
        DB::statement("DROP TYPE IF EXISTS status_denda");
    }
};
