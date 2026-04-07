<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pago_cuatrimestre', function (Blueprint $table) {
            $table->id('id_pago');
            $table->foreignId('id_alumno')->constrained('alumno', 'id_alumno')->cascadeOnDelete();
            $table->unsignedTinyInteger('cuatrimestre');
            $table->string('baucher_path');
            $table->timestamp('subido_en')->useCurrent();
            $table->unique(['id_alumno', 'cuatrimestre']);
        });
    }
    public function down(): void { Schema::dropIfExists('pago_cuatrimestre'); }
};
