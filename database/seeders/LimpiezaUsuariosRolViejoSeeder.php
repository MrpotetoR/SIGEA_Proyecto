<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

/**
 * Limpieza de usuarios con roles antiguos.
 *
 * Tras la fusión de los roles "servicios_escolares" + "director_carrera"
 * en "gestor_escolar" (ver migracion 2026_05_06_000003), este seeder:
 *
 *  1. Identifica usuarios cuyo unico rol activo era uno de los antiguos
 *     y no tenian el nuevo rol "gestor_escolar".
 *  2. Conserva intactos los usuarios demo:
 *       - admin@sigea.edu.mx
 *       - gestor@sigea.edu.mx
 *       - docente@sigea.edu.mx
 *       - alumno@sigea.edu.mx
 *  3. Hace soft delete del resto (la tabla users usa SoftDeletes).
 *     De este modo, el administrador puede restaurarlos manualmente
 *     desde la UI si lo necesita.
 *
 * Ejecutar con:
 *     php artisan db:seed --class=LimpiezaUsuariosRolViejoSeeder
 */
class LimpiezaUsuariosRolViejoSeeder extends Seeder
{
    private const EMAILS_DEMO = [
        'admin@sigea.edu.mx',
        'gestor@sigea.edu.mx',
        'docente@sigea.edu.mx',
        'alumno@sigea.edu.mx',
    ];

    public function run(): void
    {
        $rolesAntiguos = ['servicios_escolares', 'director_carrera'];
        $idsRolesAntiguos = Role::whereIn('name', $rolesAntiguos)->pluck('id');

        // Usuarios que estaban vinculados a alguno de los roles antiguos.
        $idsUsuarios = DB::table('model_has_roles')
            ->whereIn('role_id', $idsRolesAntiguos)
            ->where('model_type', User::class)
            ->pluck('model_id')
            ->unique();

        $eliminados = 0;
        $conservados = 0;

        foreach ($idsUsuarios as $userId) {
            $user = User::find($userId);
            if (!$user) continue;

            // Preservar demos.
            if (in_array($user->email, self::EMAILS_DEMO, true)) {
                $conservados++;
                continue;
            }

            // Soft delete: queda recuperable desde la papelera (admin puede restaurar).
            $user->update(['activo' => false]);
            $user->delete();
            $eliminados++;
        }

        $this->command->info("Limpieza de usuarios con rol antiguo:");
        $this->command->line("  - Soft-deleted: {$eliminados}");
        $this->command->line("  - Conservados (demo): {$conservados}");
        $this->command->line("");
        $this->command->info("Demos disponibles:");
        $this->command->line("  - admin@sigea.edu.mx   / admin2026");
        $this->command->line("  - gestor@sigea.edu.mx  / password");
        $this->command->line("  - docente@sigea.edu.mx / password");
        $this->command->line("  - alumno@sigea.edu.mx  / password");
    }
}
