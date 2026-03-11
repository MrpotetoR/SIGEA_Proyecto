<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuario', function (Blueprint $table) {
            $table->id('id_usuario');
            $table->string('nombre_usuario', 60)->unique();
            $table->string('contrasena_hash', 255)->comment('bcrypt / argon2');
            $table->enum('rol', ['alumno', 'docente', 'director', 'servicios']);
            $table->string('correo', 120)->unique();
            $table->boolean('activo')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario');
    }
};
