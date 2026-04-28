<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personal_servicios_escolares', function (Blueprint $table) {
            $table->bigIncrements('id_personal');
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('nombre', 80);
            $table->string('apellidos', 100);
            $table->string('num_cedula', 30)->nullable();
            $table->string('rfc', 20)->nullable();
            $table->string('especialidad', 150);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_servicios_escolares');
    }
};
