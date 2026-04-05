<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Buat ENUM type untuk status_peminjaman
        DB::statement("CREATE TYPE status_peminjaman AS ENUM ('menunggu', 'disetujui', 'ditolak', 'dikembalikan', 'sebagian_kembali')");

        Schema::create('peminjaman', function (Blueprint $table) {
            $table->increments('peminjaman_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('alat_id');
            $table->integer('jumlah');
            $table->date('tanggal_peminjaman');
            $table->date('tanggal_kembali_rencana');
            $table->text('tujuan_peminjaman')->nullable();
            $table->unsignedInteger('disetujui_oleh')->nullable();
            $table->timestamp('tanggal_disetujui')->nullable();
            $table->string('kode_peminjaman', 50)->nullable()->unique();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign('user_id')
                  ->references('user_id')->on('users')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            $table->foreign('alat_id')
                  ->references('alat_id')->on('alat')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            $table->foreign('disetujui_oleh')
                  ->references('user_id')->on('users')
                  ->onUpdate('cascade')
                  ->onDelete('set null');

            $table->index('user_id', 'idx_peminjaman_user');
            $table->index('alat_id', 'idx_peminjaman_alat');
        });

        // Tambah kolom ENUM status secara manual (PostgreSQL)
        DB::statement("ALTER TABLE peminjaman ADD COLUMN status status_peminjaman DEFAULT 'menunggu'::status_peminjaman");

        // Tambah index untuk status
        DB::statement("CREATE INDEX idx_peminjaman_status ON peminjaman USING btree (status)");
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
        DB::statement("DROP TYPE IF EXISTS status_peminjaman");
    }
};
