<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('value');
            $table->string('label')->nullable(); // label tampil di UI
            $table->timestamps();
        });

        // Seed langsung saat migrate
        DB::table('pengaturan')->insert([
            'key'        => 'tarif_denda',
            'value'      => '5000',
            'label'      => 'Tarif Denda Per Hari (Rp)',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan');
    }
};
