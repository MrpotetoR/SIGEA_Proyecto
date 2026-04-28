<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documento_personal_se', function (Blueprint $table) {
            $table->bigIncrements('id_documento');
            $table->unsignedBigInteger('id_personal');
            $table->string('tipo', 50);
            $table->string('archivo_path', 255);
            $table->timestamp('subido_en')->useCurrent();

            $table->foreign('id_personal')
                ->references('id_personal')->on('personal_servicios_escolares')
                ->cascadeOnDelete();

            $table->unique(['id_personal', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documento_personal_se');
    }
};
