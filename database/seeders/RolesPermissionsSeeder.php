<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Catalogo de roles y permisos del sistema.
 *
 * Roles vigentes:
 *  - admin           : superusuario
 *  - gestor_escolar  : fusion del antiguo Servicios Escolares + Director de Carrera
 *  - docente         : docente / profesor
 *  - alumno          : alumno / estudiante
 *
 * Los roles antiguos "servicios_escolares" y "director_carrera" fueron
 * fusionados en "gestor_escolar" mediante la migracion
 * 2026_05_06_000003_create_gestor_escolar_role.php.
 */
class RolesPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permisos = [
            // Alumno
            'perfil.ver_propio',
            'horario.ver_propio',
            'calificaciones.ver_propio',
            'kardex.generar_propio',
            'historial.ver_propio',
            'hrs_culturales.ver_propio',
            'hrs_servicio_social.ver_propio',
            'noticias.ver',
            'evaluacion_docente.crear',
            'evaluacion_docente.ver_propio',
            'encuestas.contestar',
            'tutor.ver_propio',

            // Docente
            'grupos.ver_asignados',
            'asistencia.gestionar',
            'calificaciones.gestionar',
            'reportes_asistencia.generar',
            'reportes_rendimiento.generar',
            'hrs_culturales.gestionar',
            'hrs_servicio_social.gestionar',
            'evaluacion_docente.ver_resultados_propio',
            'lista_asistencia_pdf.generar',

            // Gestor Escolar — heredados del antiguo Director
            'docentes.ver_carrera',
            'grupos.ver_carrera',
            'grupos.gestionar',
            'horarios.gestionar',
            'horarios.ver_carrera',
            'alumnos.ver_carrera',
            'indice_aprobacion.ver',
            'plan_estudios.ver',
            'materias.ver_carrera',
            'historial_alumno.ver',
            'asistencia.ver_carrera',
            'evaluacion_docente.ver_promedios',
            'tutores.gestionar',

            // Gestor Escolar — heredados del antiguo Servicios Escolares
            'alumnos.gestionar',
            'docentes.gestionar',
            'carreras.gestionar',
            'ciclos.gestionar',
            'constancias.gestionar',
            'bajas.gestionar',
            'noticias.gestionar',
            'documentos.gestionar',
            'inscripciones.gestionar',
            'materias.gestionar',
            'grupos.crear',
            'reportes.ver_todos',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        // ── Roles y asignacion de permisos ────────────────────────────

        $rolAlumno = Role::firstOrCreate(['name' => 'alumno']);
        $rolAlumno->syncPermissions([
            'perfil.ver_propio', 'horario.ver_propio', 'calificaciones.ver_propio',
            'kardex.generar_propio', 'historial.ver_propio', 'hrs_culturales.ver_propio',
            'hrs_servicio_social.ver_propio', 'noticias.ver', 'evaluacion_docente.crear',
            'evaluacion_docente.ver_propio', 'encuestas.contestar', 'tutor.ver_propio',
        ]);

        $rolDocente = Role::firstOrCreate(['name' => 'docente']);
        $rolDocente->syncPermissions([
            'perfil.ver_propio', 'grupos.ver_asignados', 'horario.ver_propio',
            'asistencia.gestionar', 'calificaciones.gestionar', 'reportes_asistencia.generar',
            'reportes_rendimiento.generar', 'hrs_culturales.gestionar', 'hrs_servicio_social.gestionar',
            'evaluacion_docente.ver_resultados_propio', 'noticias.ver', 'lista_asistencia_pdf.generar',
        ]);

        // Gestor Escolar = todos los permisos academicos y operativos.
        $rolGestor = Role::firstOrCreate(['name' => 'gestor_escolar']);
        $rolGestor->syncPermissions(Permission::all());

        // Admin — superusuario.
        $rolAdmin = Role::firstOrCreate(['name' => 'admin']);
        $rolAdmin->syncPermissions(Permission::all());
    }
}
