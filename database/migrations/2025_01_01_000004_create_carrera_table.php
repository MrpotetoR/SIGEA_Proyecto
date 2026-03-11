<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carrera', function (Blueprint $table) {
            $table->id('id_carrera');
            $table->unsignedBigInteger('id_director')->nullable()->comment('FK a docente que dirige la carrera');
            $table->string('nombre_carrera', 120);
            $table->string('clave_carrera', 20)->unique();

            $table->foreign('id_director')
                  ->references('id_docente')->on('docente')
                  ->onUpdate('cascade')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carrera');
    }
};
