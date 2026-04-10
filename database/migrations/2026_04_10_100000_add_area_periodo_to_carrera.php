<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('carrera', function (Blueprint $table) {
            $table->string('area_academica', 50)->nullable()->after('nombre_carrera');
            $table->enum('tipo_periodo', ['cuatrimestre', 'semestre'])->default('cuatrimestre')->after('area_academica');
            $table->unsignedTinyInteger('duracion_periodos')->default(10)->after('tipo_periodo');
        });
    }
    public function down(): void
    {
        Schema::table('carrera', function (Blueprint $table) {
            $table->dropColumn(['area_academica', 'tipo_periodo', 'duracion_periodos']);
        });
    }
};
