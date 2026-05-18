<?php

namespace Database\Seeders;

use App\Models\Alumno;
use App\Models\BachilleratoPlan;
use App\Models\CicloEscolar;
use App\Models\Docente;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeder de demostracion para el nivel BACHILLERATO.
 *
 * Crea:
 *   - 1 plan: "Bachillerato General Unitario 2026" (6 semestres)
 *   - 18 materias (3 por semestre x 6 semestres)
 *   - 4 grupos (1°A, 2°A, 3°A, 4°A) en el ciclo activo
 *   - 3 docentes de bachillerato
 *   - 6 alumnos demo distribuidos en los grupos
 *
 * Ejecutar con:
 *     php artisan db:seed --class=BachilleratoDemoSeeder
 */
class BachilleratoDemoSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Plan de Bachillerato ────────────────────────────────────
        $plan = BachilleratoPlan::firstOrCreate(
            ['clave_plan' => 'BGU-2026'],
            [
                'nombre_plan'   => 'Bachillerato General Unitario 2026',
                'num_semestres' => 6,
                'vigente'       => true,
                'descripcion'   => 'Plan de 3 anos divididos en 6 semestres.',
            ]
        );

        // ── 2. Ciclo escolar (reutilizamos el universitario o creamos uno) ──
        $ciclo = CicloEscolar::orderByDesc('id_ciclo')->first();
        if (!$ciclo) {
            $this->command->warn('No hay ciclo escolar activo. Saltando seeder de bachillerato.');
            return;
        }

        // ── 3. Materias por semestre ───────────────────────────────────
        $materiasPorSemestre = [
            1 => ['Matematicas I', 'Lengua y Literatura I', 'Quimica I'],
            2 => ['Matematicas II', 'Lengua y Literatura II', 'Biologia I'],
            3 => ['Matematicas III', 'Historia de Mexico I', 'Fisica I'],
            4 => ['Matematicas IV', 'Historia de Mexico II', 'Fisica II'],
            5 => ['Calculo Diferencial', 'Etica y Valores', 'Geografia'],
            6 => ['Calculo Integral', 'Filosofia', 'Economia'],
        ];

        foreach ($materiasPorSemestre as $semestre => $nombres) {
            foreach ($nombres as $nombre) {
                Materia::firstOrCreate(
                    [
                        'nombre_materia'        => $nombre,
                        'nivel_educativo'       => 'bachillerato',
                        'id_plan_bachillerato'  => $plan->id_plan_bachillerato,
                    ],
                    [
                        'cuatrimestre'  => $semestre,
                        'horas_semana'  => 4,
                        'id_carrera'    => null,
                    ]
                );
            }
        }

        // ── 4. Docentes de bachillerato (3) ────────────────────────────
        $docentesData = [
            ['email' => 'profe.matematicas@sigea.edu.mx', 'nombre' => 'Roberto',  'apellidos' => 'Hernandez Lopez', 'especialidad' => 'Matematicas'],
            ['email' => 'profe.lengua@sigea.edu.mx',      'nombre' => 'Lucia',    'apellidos' => 'Martinez Garcia', 'especialidad' => 'Lengua y Literatura'],
            ['email' => 'profe.ciencias@sigea.edu.mx',    'nombre' => 'Fernando', 'apellidos' => 'Ruiz Sanchez',    'especialidad' => 'Ciencias Naturales'],
        ];

        $docentes = [];
        foreach ($docentesData as $d) {
            $user = User::firstOrCreate(
                ['email' => $d['email']],
                [
                    'name'     => "{$d['nombre']} {$d['apellidos']}",
                    'password' => bcrypt('password'),
                    'activo'   => true,
                ]
            );
            if (!$user->hasRole('docente')) $user->assignRole('docente');

            $docente = Docente::sinFiltroDeCarreras()->sinFiltroNivel()
                ->firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nombre'         => $d['nombre'],
                        'apellidos'      => $d['apellidos'],
                        'especialidad'   => $d['especialidad'],
                        'horas_contrato' => 20,
                        'es_tutor'       => true,
                        'nivel_educativo'=> 'bachillerato',
                    ]
                );
            $docentes[] = $docente;
        }

        // ── 5. Grupos (1°A, 2°A, 3°A, 4°A) ────────────────────────────
        $gruposClaves = ['1A-BACH', '2A-BACH', '3A-BACH', '4A-BACH'];
        $grupos = [];
        foreach ($gruposClaves as $i => $clave) {
            $grupo = Grupo::sinFiltroDeCarreras()->sinFiltroNivel()
                ->firstOrCreate(
                    ['clave_grupo' => $clave],
                    [
                        'id_carrera'            => null,
                        'id_plan_bachillerato'  => $plan->id_plan_bachillerato,
                        'id_ciclo'              => $ciclo->id_ciclo,
                        'id_tutor'              => $docentes[$i % count($docentes)]->id_docente,
                        'cuatrimestre'          => $i + 1,
                        'nivel_educativo'       => 'bachillerato',
                    ]
                );
            $grupos[] = $grupo;
        }

        // ── 6. Alumnos demo (6) ────────────────────────────────────────
        $alumnosData = [
            ['email' => 'bachi.uno@sigea.edu.mx',    'nombre' => 'Sofia',   'apellidos' => 'Vazquez Cruz',    'grupo' => 0, 'sem' => 1],
            ['email' => 'bachi.dos@sigea.edu.mx',    'nombre' => 'Diego',   'apellidos' => 'Mendoza Reyes',   'grupo' => 0, 'sem' => 1],
            ['email' => 'bachi.tres@sigea.edu.mx',   'nombre' => 'Valeria', 'apellidos' => 'Salazar Ortiz',   'grupo' => 1, 'sem' => 2],
            ['email' => 'bachi.cuatro@sigea.edu.mx', 'nombre' => 'Andres',  'apellidos' => 'Castillo Romero', 'grupo' => 2, 'sem' => 3],
            ['email' => 'bachi.cinco@sigea.edu.mx',  'nombre' => 'Camila',  'apellidos' => 'Navarro Gomez',   'grupo' => 3, 'sem' => 4],
            ['email' => 'bachi.seis@sigea.edu.mx',   'nombre' => 'Mateo',   'apellidos' => 'Pena Aguilar',    'grupo' => 3, 'sem' => 4],
        ];

        foreach ($alumnosData as $i => $a) {
            $user = User::firstOrCreate(
                ['email' => $a['email']],
                [
                    'name'     => "{$a['nombre']} {$a['apellidos']}",
                    'password' => bcrypt('password'),
                    'activo'   => true,
                ]
            );
            if (!$user->hasRole('alumno')) $user->assignRole('alumno');

            $idPublico = 'BACH-2026-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT);

            Alumno::sinFiltroNivel()->firstOrCreate(
                ['user_id' => $user->id],
                [
                    'id_carrera'           => null,
                    'id_plan_bachillerato' => $plan->id_plan_bachillerato,
                    'id_tutor'             => $grupos[$a['grupo']]->id_tutor,
                    'id_alumno_publico'    => $idPublico,
                    'nombre'               => $a['nombre'],
                    'apellidos'            => $a['apellidos'],
                    'cuatrimestre_actual'  => $a['sem'],
                    'estatus'              => 'activo',
                    'nivel_educativo'      => 'bachillerato',
                ]
            );
        }

        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════');
        $this->command->info('  Bachillerato cargado con datos demo');
        $this->command->info('═══════════════════════════════════════════════════');
        $this->command->line('  • Plan: BGU-2026 (6 semestres)');
        $this->command->line('  • 18 materias (3 por semestre)');
        $this->command->line('  • 4 grupos: 1A-BACH ... 4A-BACH');
        $this->command->line('  • 3 docentes de bachillerato');
        $this->command->line('  • 6 alumnos (BACH-2026-001 .. 006)');
        $this->command->info('═══════════════════════════════════════════════════');
    }
}
