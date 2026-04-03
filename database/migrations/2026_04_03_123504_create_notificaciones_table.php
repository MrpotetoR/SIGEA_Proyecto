<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('tipo');          // noticia, calificacion, inscripcion, etc.
            $table->string('titulo');
            $table->text('mensaje');
            $table->string('icono')->default('bell');  // bell, newspaper, academic-cap, etc.
            $table->string('color')->default('blue');   // blue, green, amber, red
            $table->string('url')->nullable();          // link al recurso
            $table->timestamp('leida_en')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'leida_en']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
