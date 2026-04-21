<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Reescribe las URLs de notificaciones de tipo 'noticia' que apuntaban a la ruta
 * protegida /servicios/noticias/{id} (que devolvía 403 a roles no-servicios)
 * hacia la nueva ruta universal /noticias/{id}.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('notificaciones')
            ->where('tipo', 'noticia')
            ->where('url', 'like', '%/servicios/noticias/%')
            ->update([
                'url' => DB::raw("REPLACE(url, '/servicios/noticias/', '/noticias/')"),
            ]);
    }

    public function down(): void
    {
        DB::table('notificaciones')
            ->where('tipo', 'noticia')
            ->where('url', 'regexp', '/noticias/[0-9]+$')
            ->update([
                'url' => DB::raw("REPLACE(url, '/noticias/', '/servicios/noticias/')"),
            ]);
    }
};
