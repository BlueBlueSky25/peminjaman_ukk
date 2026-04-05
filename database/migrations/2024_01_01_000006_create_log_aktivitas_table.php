<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('log_aktivitas')) return;

        // Deteksi kolom primary key tabel users (bisa user_id atau id)
        $userPK = Schema::hasColumn('users', 'user_id') ? 'user_id' : 'id';

        Schema::create('log_aktivitas', function (Blueprint $table) use ($userPK) {
            $table->increments('log_id');
            $table->unsignedInteger('user_id')->nullable();
            $table->string('aktivitas', 255);
            $table->string('modul', 50);
            $table->timestamp('timestamp')->useCurrent();

            $table->foreign('user_id')
                  ->references($userPK)->on('users')
                  ->onUpdate('cascade')
                  ->onDelete('set null');

            $table->index('user_id', 'idx_log_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_aktivitas');
    }
};
