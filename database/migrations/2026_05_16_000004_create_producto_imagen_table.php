<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Galería de imágenes adicionales del producto.
 *
 * `producto.imagen_principal` cubre la portada (lo que se muestra en el
 * catálogo). Esta tabla almacena las fotos extra que se muestran en el
 * detalle del producto (frontal, trasera, detalle, etc.).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('producto_imagen', function (Blueprint $t) {
            $t->id('id_imagen');
            $t->foreignId('id_producto')
                ->constrained('producto', 'id_producto')
                ->cascadeOnDelete();
            $t->string('archivo_path', 500);
            $t->string('alt', 150)->nullable();
            $t->unsignedTinyInteger('orden')->default(0)->comment('1=primera, 2=segunda...');
            $t->timestamps();

            $t->index(['id_producto', 'orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('producto_imagen');
    }
};
