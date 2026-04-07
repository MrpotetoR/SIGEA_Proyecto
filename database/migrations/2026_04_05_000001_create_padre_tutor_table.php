<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('padre_tutor', function (Blueprint $table) {
            $table->id('id_padre_tutor');
            $table->foreignId('id_alumno')->unique()->constrained('alumno', 'id_alumno')->cascadeOnDelete();
            $table->string('nombre', 80);
            $table->string('apellidos', 100);
            $table->string('email', 150)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('telefono_emergencia', 20)->nullable();
            $table->string('ine_path')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('padre_tutor'); }
};
