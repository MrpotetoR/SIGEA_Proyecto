<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('documento_alumno', function (Blueprint $table) {
            $table->id('id_documento');
            $table->foreignId('id_alumno')->constrained('alumno', 'id_alumno')->cascadeOnDelete();
            $table->string('tipo', 50); // acta_nacimiento, curp, comprobante_domicilio, constancia_media_superior, constancia_basica
            $table->string('archivo_path');
            $table->timestamp('subido_en')->useCurrent();
            $table->unique(['id_alumno', 'tipo']);
        });
    }
    public function down(): void { Schema::dropIfExists('documento_alumno'); }
};
