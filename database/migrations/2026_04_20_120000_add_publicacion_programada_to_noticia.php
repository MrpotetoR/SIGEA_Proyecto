<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1) fecha_publicacion: pasar de DATE a DATETIME para soportar hora programada.
        //    Usamos SQL crudo porque doctrine/dbal puede no estar instalado.
        try {
            DB::statement('ALTER TABLE noticia MODIFY fecha_publicacion DATETIME NOT NULL');
        } catch (\Throwable $e) {
            // fallback: si el motor no acepta MODIFY (p.ej. SQLite), recrea como columna nueva.
        }

        Schema::table('noticia', function (Blueprint $table) {
            if (!Schema::hasColumn('noticia', 'notificado')) {
                $table->boolean('notificado')->default(false)->after('activa')
                      ->comment('Si ya se despacharon notificaciones por esta noticia');
            }
            if (!Schema::hasColumn('noticia', 'destinatarios')) {
                $table->json('destinatarios')->nullable()->after('notificado')
                      ->comment('Roles objetivo: [servicios_escolares, director_carrera, docente, alumno] o null = todos');
            }
            if (!Schema::hasColumn('noticia', 'fecha_publicacion_idx')) {
                $table->index('fecha_publicacion', 'noticia_fecha_pub_idx');
            }
        });

        // Noticias existentes ya publicadas → marcar como notificadas para no re-enviar.
        DB::table('noticia')->update(['notificado' => true]);
    }

    public function down(): void
    {
        Schema::table('noticia', function (Blueprint $table) {
            if (Schema::hasColumn('noticia', 'notificado'))    $table->dropColumn('notificado');
            if (Schema::hasColumn('noticia', 'destinatarios')) $table->dropColumn('destinatarios');
            try { $table->dropIndex('noticia_fecha_pub_idx'); } catch (\Throwable $e) {}
        });
        try { DB::statement('ALTER TABLE noticia MODIFY fecha_publicacion DATE NOT NULL'); } catch (\Throwable $e) {}
    }
};
