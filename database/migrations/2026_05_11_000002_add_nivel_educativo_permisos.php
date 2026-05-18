<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Agrega dos permisos finos para que un Gestor Escolar pueda tener acceso
 * a una o ambas areas operativas (Universidad / Bachillerato).
 *
 * Por defecto, el rol "gestor_escolar" recibe AMBOS permisos. El admin
 * puede revocar uno si crea un gestor que solo atendera un area.
 */
return new class extends Migration
{
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $nuevos = ['gestor.universidad', 'gestor.bachillerato'];
        foreach ($nuevos as $nombre) {
            Permission::firstOrCreate(['name' => $nombre]);
        }

        // Asignar ambos al rol gestor_escolar (acceso pleno por defecto).
        $rolGestor = Role::where('name', 'gestor_escolar')->first();
        if ($rolGestor) {
            $rolGestor->givePermissionTo($nuevos);
        }

        // Admin tambien tiene todos los permisos.
        $rolAdmin = Role::where('name', 'admin')->first();
        if ($rolAdmin) {
            $rolAdmin->givePermissionTo($nuevos);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (['gestor.universidad', 'gestor.bachillerato'] as $nombre) {
            Permission::where('name', $nombre)->delete();
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
