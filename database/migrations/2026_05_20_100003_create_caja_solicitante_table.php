<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Catálogo de solicitantes para autocompletado.
 *
 * Se popula automáticamente al crear un vale con un nombre nuevo.
 * El endpoint /gestor/caja-chica/solicitantes/buscar?q=... consulta esta
 * tabla con LIKE %q% y devuelve top 5 ordenados por veces_usado DESC,
 * ultimo_uso_en DESC.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caja_solicitante', function (Blueprint $t) {
            $t->id();
            $t->string('nombre', 150)->unique();
            $t->unsignedInteger('veces_usado')->default(1);
            $t->timestamp('ultimo_uso_en')->useCurrent();
            $t->timestamps();

            $t->index('veces_usado');
            $t->index('ultimo_uso_en');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caja_solicitante');
    }
};
