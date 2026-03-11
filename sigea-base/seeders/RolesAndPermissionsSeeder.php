<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ─── Permisos ──────────────────────────────

        $permisos = [
            // Perfil
            'perfil.ver_propio',

            // Alumno — consultas
            'horario.ver_propio',
            'calificaciones.ver_propio',
            'kardex.generar_propio',
            'historial.ver_propio',
            'hrs_culturales.ver_propio',
            'hrs_servicio_social.ver_propio',
            'tutor.ver_propio',
            'noticias.ver',

            // Alumno — acciones
            'evaluacion_docente.crear',
            'evaluacion_docente.ver_propio',
            'foros.acceder',
            'encuestas.contestar',

            // Docente — consultas
            'grupos.ver_asignados',
            'evaluacion_docente.ver_resultados_propio',

            // Docente — CRUD
            'asistencia.gestionar',
            'calificaciones.gestionar',
            'reportes_asistencia.generar',
            'reportes_rendimiento.generar',
            'hrs_culturales.gestionar',
            'hrs_servicio_social.gestionar',
            'foros.gestionar',
            'lista_asistencia_pdf.generar',

            // Director — consultas
            'docentes.ver_carrera',
            'grupos.ver_carrera',
            'alumnos.ver_carrera',
            'horarios.ver_carrera',
            'indice_aprobacion.ver',
            'plan_estudios.ver',
            'materias.ver_carrera',
            'historial_alumno.ver',
            'asistencia.ver_carrera',
            'evaluacion_docente.ver_promedios',

            // Director — CRUD
            'grupos.gestionar',
            'horarios.gestionar',
            'tutores_grupo.gestionar',

            // Servicios Escolares — CRUD total
            'alumnos.gestionar',
            'docentes.gestionar',
            'directores.gestionar',
            'carreras.gestionar',
            'materias.gestionar',
            'ciclos.gestionar',
            'noticias.gestionar',
            'constancias.gestionar',
            'bajas.gestionar',
            'documentos.gestionar',
            'evaluacion_docente.gestionar',
            'matriculas.generar',
            'tutores.gestionar',
        ];

        foreach ($permisos as $permiso) {
            Permission::findOrCreate($permiso);
        }

        // ─── Roles ─────────────────────────────────

        $alumno = Role::findOrCreate('alumno');
        $alumno->syncPermissions([
            'perfil.ver_propio',
            'horario.ver_propio',
            'calificaciones.ver_propio',
            'kardex.generar_propio',
            'historial.ver_propio',
            'hrs_culturales.ver_propio',
            'hrs_servicio_social.ver_propio',
            'tutor.ver_propio',
            'noticias.ver',
            'evaluacion_docente.crear',
            'evaluacion_docente.ver_propio',
            'foros.acceder',
            'encuestas.contestar',
        ]);

        $docente = Role::findOrCreate('docente');
        $docente->syncPermissions([
            'perfil.ver_propio',
            'horario.ver_propio',
            'noticias.ver',
            'grupos.ver_asignados',
            'asistencia.gestionar',
            'calificaciones.gestionar',
            'reportes_asistencia.generar',
            'reportes_rendimiento.generar',
            'hrs_culturales.gestionar',
            'hrs_servicio_social.gestionar',
            'foros.gestionar',
            'lista_asistencia_pdf.generar',
            'evaluacion_docente.ver_resultados_propio',
        ]);

        $director = Role::findOrCreate('director_carrera');
        $director->syncPermissions([
            'perfil.ver_propio',
            'noticias.ver',
            'docentes.ver_carrera',
            'grupos.ver_carrera',
            'grupos.gestionar',
            'alumnos.ver_carrera',
            'horarios.ver_carrera',
            'horarios.gestionar',
            'indice_aprobacion.ver',
            'plan_estudios.ver',
            'materias.ver_carrera',
            'historial_alumno.ver',
            'asistencia.ver_carrera',
            'evaluacion_docente.ver_promedios',
            'tutores_grupo.gestionar',
        ]);

        $admin = Role::findOrCreate('servicios_escolares');
        $admin->syncPermissions(Permission::all());
    }
}
