<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Elimina los documentos de tipo "numero_seguridad_social"
     * de las tablas de documentos del docente, alumno y personal de servicios escolares,
     * y borra los archivos físicos asociados del storage.
     */
    public function up(): void
    {
        $tablas = ['documento_docente', 'documento_alumno', 'documento_personal_se'];

        foreach ($tablas as $tabla) {
            if (!Schema::hasTable($tabla)) {
                continue;
            }

            $registros = DB::table($tabla)->where('tipo', 'numero_seguridad_social')->get();

            foreach ($registros as $r) {
                if (!empty($r->archivo_path) && Storage::disk('public')->exists($r->archivo_path)) {
                    Storage::disk('public')->delete($r->archivo_path);
                }
            }

            DB::table($tabla)->where('tipo', 'numero_seguridad_social')->delete();
        }
    }

    /**
     * No es posible recuperar los archivos eliminados; el down() queda intencionalmente vacío.
     */
    public function down(): void
    {
        // No-op: no se restauran los registros eliminados.
    }
};
