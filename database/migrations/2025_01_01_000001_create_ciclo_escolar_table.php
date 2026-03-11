<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ciclo_escolar', function (Blueprint $table) {
            $table->id('id_ciclo');
            $table->string('nombre', 50)->comment('Ej: 2026-1');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ciclo_escolar');
    }
};
