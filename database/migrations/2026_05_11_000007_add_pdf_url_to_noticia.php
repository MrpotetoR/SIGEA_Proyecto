<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Agrega soporte de PDF adjunto a las noticias.
 *
 * Permite al Gestor Escolar compartir documentos oficiales
 * (reglamentos, calendarios, oficios) directamente en una noticia,
 * complementando la imagen/URL existente.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('noticia', function (Blueprint $t) {
            $t->string('pdf_url', 500)
                ->nullable()
                ->after('imagen_url')
                ->comment('Ruta al PDF adjunto en storage o URL externa.');
            $t->string('pdf_nombre', 150)
                ->nullable()
                ->after('pdf_url')
                ->comment('Nombre legible para mostrar al descargar.');
        });
    }

    public function down(): void
    {
        Schema::table('noticia', function (Blueprint $t) {
            $t->dropColumn(['pdf_url', 'pdf_nombre']);
        });
    }
};
