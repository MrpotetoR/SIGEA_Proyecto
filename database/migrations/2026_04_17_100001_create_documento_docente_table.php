<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documento_docente', function (Blueprint $table) {
            $table->id('id_documento');
            $table->unsignedBigInteger('id_docente');
            $table->string('tipo', 40);
            $table->string('archivo_path');
            $table->timestamp('subido_en')->useCurrent();

            $table->foreign('id_docente')
                  ->references('id_docente')->on('docente')
                  ->onDelete('cascade');

            $table->unique(['id_docente', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documento_docente');
    }
};
