<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alat', function (Blueprint $table) {
            $table->bigInteger('harga_alat')->default(0)->after('stok_hilang');
        });

        Schema::table('pengembalian', function (Blueprint $table) {
            $table->integer('persen_kerusakan')->default(0)->after('kondisi_alat');
            $table->bigInteger('denda_kerusakan')->default(0)->after('total_denda');
            $table->bigInteger('denda_kehilangan')->default(0)->after('denda_kerusakan');
            $table->bigInteger('denda_keterlambatan')->default(0)->after('denda_kehilangan');
        });
    }

    public function down(): void
    {
        Schema::table('alat', function (Blueprint $table) {
            $table->dropColumn('harga_alat');
        });

        Schema::table('pengembalian', function (Blueprint $table) {
            $table->dropColumn(['persen_kerusakan', 'denda_kerusakan', 'denda_kehilangan', 'denda_keterlambatan']);
        });
    }
};
