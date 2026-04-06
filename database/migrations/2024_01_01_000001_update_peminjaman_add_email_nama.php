<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            // Jadikan user_id nullable (tidak wajib login)
            $table->unsignedBigInteger('user_id')->nullable()->change();

            // Tambah kolom baru
            $table->string('nama_peminjam')->nullable()->after('user_id');
            $table->string('email')->nullable()->after('nama_peminjam');
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->dropColumn(['nama_peminjam', 'email']);
        });
    }
};
