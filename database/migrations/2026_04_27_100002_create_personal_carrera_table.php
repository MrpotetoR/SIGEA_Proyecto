<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personal_carrera', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_personal');
            $table->unsignedBigInteger('id_carrera');
            $table->timestamps();

            $table->foreign('id_personal')
                ->references('id_personal')->on('personal_servicios_escolares')
                ->cascadeOnDelete();

            $table->foreign('id_carrera')
                ->references('id_carrera')->on('carrera')
                ->cascadeOnDelete();

            // Una carrera SOLO puede ser administrada por UN personal de SE.
            $table->unique('id_carrera');
            $table->index('id_personal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_carrera');
    }
};
