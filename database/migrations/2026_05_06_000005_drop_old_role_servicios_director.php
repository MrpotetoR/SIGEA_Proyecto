<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

/**
 * Elimina los roles antiguos "servicios_escolares" y "director_carrera".
 *
 * Esta migración SOLO debe ejecutarse después de:
 *   1) 2026_05_06_000003_create_gestor_escolar_role.php (que migra usuarios).
 *   2) Verificación manual de que ningún usuario sigue con el rol antiguo.
 */
return new class extends Migration
{
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (['servicios_escolares', 'director_carrera'] as $nombre) {
            $rol = Role::where('name', $nombre)->first();
            if ($rol) $rol->delete();
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Recrea los roles vacíos para que la migración previa pueda revertirse.
        // Los permisos se reasignan ejecutando RolesPermissionsSeeder.
        Role::firstOrCreate(['name' => 'servicios_escolares']);
        Role::firstOrCreate(['name' => 'director_carrera']);
    }
};
