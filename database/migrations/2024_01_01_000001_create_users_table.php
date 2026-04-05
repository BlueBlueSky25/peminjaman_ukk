<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Buat ENUM type untuk user_level
        DB::statement("CREATE TYPE user_level AS ENUM ('admin', 'petugas', 'peminjam')");

        Schema::create('users', function (Blueprint $table) {
            $table->increments('user_id');
            $table->string('username', 50)->unique();
            $table->string('password', 255);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });

        // Tambah kolom ENUM secara manual (PostgreSQL)
        DB::statement("ALTER TABLE users ADD COLUMN level user_level NOT NULL");
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        DB::statement("DROP TYPE IF EXISTS user_level");
    }
};
