<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Crea el rol "gestor_escolar" como fusión de "servicios_escolares" y
 * "director_carrera". Migra a todos los usuarios existentes con cualquiera
 * de los dos roles antiguos al nuevo rol.
 *
 * NOTA: los roles antiguos NO se eliminan en esta migración. Se eliminan en
 * 2026_05_06_000005_drop_old_role_servicios_director.php para mantener cada
 * migración con un único propósito y permitir rollback granular.
 */
return new class extends Migration
{
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Reunir todos los permisos de los dos roles antiguos.
        $rolServicios = Role::where('name', 'servicios_escolares')->first();
        $rolDirector  = Role::where('name', 'director_carrera')->first();

        $permisos = collect();
        if ($rolServicios) $permisos = $permisos->merge($rolServicios->permissions);
        if ($rolDirector)  $permisos = $permisos->merge($rolDirector->permissions);
        $permisos = $permisos->unique('id');

        // 2. Crear o reutilizar el rol "gestor_escolar".
        $rolGestor = Role::firstOrCreate(['name' => 'gestor_escolar']);
        $rolGestor->syncPermissions($permisos);

        // 3. Migrar usuarios existentes con los roles antiguos.
        $rolesAntiguos = ['servicios_escolares', 'director_carrera'];
        $idsRolesAntiguos = Role::whereIn('name', $rolesAntiguos)->pluck('id');

        if ($idsRolesAntiguos->isNotEmpty()) {
            $usuarios = DB::table('model_has_roles')
                ->whereIn('role_id', $idsRolesAntiguos)
                ->where('model_type', \App\Models\User::class)
                ->pluck('model_id')
                ->unique();

            foreach ($usuarios as $userId) {
                // Quitar roles antiguos del usuario.
                DB::table('model_has_roles')
                    ->where('model_id', $userId)
                    ->where('model_type', \App\Models\User::class)
                    ->whereIn('role_id', $idsRolesAntiguos)
                    ->delete();

                // Asignar el nuevo rol (si no lo tiene ya).
                $yaTiene = DB::table('model_has_roles')
                    ->where('model_id', $userId)
                    ->where('model_type', \App\Models\User::class)
                    ->where('role_id', $rolGestor->id)
                    ->exists();

                if (!$yaTiene) {
                    DB::table('model_has_roles')->insert([
                        'role_id'    => $rolGestor->id,
                        'model_type' => \App\Models\User::class,
                        'model_id'   => $userId,
                    ]);
                }
            }
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $rolGestor = Role::where('name', 'gestor_escolar')->first();
        if (!$rolGestor) return;

        // Asegurar que existan los roles antiguos para devolver los usuarios.
        $rolServicios = Role::firstOrCreate(['name' => 'servicios_escolares']);
        $rolDirector  = Role::firstOrCreate(['name' => 'director_carrera']);

        // Devolver a los usuarios del rol "gestor_escolar" al rol "servicios_escolares"
        // (no podemos saber con certeza quién era director vs servicios tras la fusión,
        // por eso se elige el de mayor alcance histórico como destino por defecto).
        $usuariosGestor = DB::table('model_has_roles')
            ->where('role_id', $rolGestor->id)
            ->where('model_type', \App\Models\User::class)
            ->pluck('model_id');

        foreach ($usuariosGestor as $userId) {
            DB::table('model_has_roles')
                ->where('model_id', $userId)
                ->where('model_type', \App\Models\User::class)
                ->where('role_id', $rolGestor->id)
                ->delete();

            DB::table('model_has_roles')->insert([
                'role_id'    => $rolServicios->id,
                'model_type' => \App\Models\User::class,
                'model_id'   => $userId,
            ]);
        }

        $rolGestor->delete();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
