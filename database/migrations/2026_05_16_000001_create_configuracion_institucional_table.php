<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Configuración institucional editable por el Administrador.
 *
 * Formato clave/valor para permitir agregar nuevas configuraciones
 * sin migraciones futuras. Se usa principalmente para:
 *   - Cuenta bancaria institucional (banco, CLABE, titular)
 *   - Ubicación y horario de entrega de pedidos
 *   - Instrucciones de pago que verá el alumno
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracion_institucional', function (Blueprint $t) {
            $t->id('id_configuracion');
            $t->string('clave', 80)->unique()->comment('Ej: tienda.cuenta_banco, tienda.cuenta_clabe');
            $t->text('valor')->nullable();
            $t->string('descripcion', 200)->nullable()->comment('Texto que se muestra al admin para entender el campo');
            $t->string('grupo', 50)->default('general')->comment('Para agrupar visualmente: tienda, general, etc.');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion_institucional');
    }
};
