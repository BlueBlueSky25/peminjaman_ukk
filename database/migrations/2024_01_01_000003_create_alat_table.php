<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Buat ENUM type untuk kondisi_alat (abaikan jika sudah ada)
        DB::statement("DO $$ BEGIN CREATE TYPE kondisi_alat AS ENUM ('baik', 'rusak', 'hilang'); EXCEPTION WHEN duplicate_object THEN null; END $$");

        if (Schema::hasTable('alat')) return;

        Schema::create('alat', function (Blueprint $table) {
            $table->increments('alat_id');
            $table->unsignedInteger('kategori_id');
            $table->string('nama_alat', 100);
            $table->text('deskripsi')->nullable();
            $table->string('kode_alat', 50)->unique();
            $table->integer('stok_total');
            $table->integer('stok_tersedia');
            $table->string('lokasi', 100)->nullable();
            $table->integer('stok_rusak')->default(0);
            $table->integer('stok_hilang')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign('kategori_id')
                  ->references('kategori_id')->on('kategori')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            $table->index('kategori_id', 'idx_alat_kategori');
        });

        // Tambah kolom ENUM kondisi secara manual (PostgreSQL)
        DB::statement("ALTER TABLE alat ADD COLUMN kondisi kondisi_alat DEFAULT 'baik'::kondisi_alat");
    }

    public function down(): void
    {
        Schema::dropIfExists('alat');
        DB::statement("DROP TYPE IF EXISTS kondisi_alat");
    }
};
