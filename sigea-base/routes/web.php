<?php

use Illuminate\Support\Facades\Route;

// ─── Público ───────────────────────────────────────

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route(auth()->user()->dashboardRoute())
        : redirect()->route('login');
});

// ─── Auth (Tú lo implementas) ──────────────────────

Route::get('/login', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// ─── Rutas protegidas ──────────────────────────────

Route::middleware(['auth'])->group(function () {

    // ═══════════════════════════════════════════
    // SERVICIOS ESCOLARES (Tú lo implementas)
    // ═══════════════════════════════════════════
    Route::middleware(['check.role:servicios_escolares'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

            // TODO: Tus rutas de CRUD aquí
            // Route::resource('alumnos', ...);
            // Route::resource('docentes', ...);
            // Route::resource('carreras', ...);
            // etc.
        });

    // ═══════════════════════════════════════════
    // ALUMNO
    // ═══════════════════════════════════════════
    Route::middleware(['check.role:alumno'])
        ->prefix('alumno')
        ->name('alumno.')
        ->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\Alumno\DashboardController::class, 'index'])->name('dashboard');
            Route::get('/perfil', [\App\Http\Controllers\Alumno\PerfilController::class, 'index'])->name('perfil');
            Route::get('/horario', [\App\Http\Controllers\Alumno\HorarioController::class, 'index'])->name('horario');
            Route::get('/calificaciones', [\App\Http\Controllers\Alumno\CalificacionesController::class, 'index'])->name('calificaciones');
            Route::get('/kardex', [\App\Http\Controllers\Alumno\KardexController::class, 'index'])->name('kardex');
            Route::get('/kardex/pdf', [\App\Http\Controllers\Alumno\KardexController::class, 'descargarPDF'])->name('kardex.pdf');
            Route::get('/historial', [\App\Http\Controllers\Alumno\HistorialController::class, 'index'])->name('historial');
            Route::get('/hrs-culturales', [\App\Http\Controllers\Alumno\HrsCulturalesController::class, 'index'])->name('hrs-culturales');
            Route::get('/servicio-social', [\App\Http\Controllers\Alumno\ServicioSocialController::class, 'index'])->name('servicio-social');
            Route::get('/noticias', [\App\Http\Controllers\Alumno\NoticiasController::class, 'index'])->name('noticias');
            Route::get('/evaluacion-docente', [\App\Http\Controllers\Alumno\EvaluacionDocenteController::class, 'index'])->name('evaluacion-docente');
            Route::post('/evaluacion-docente', [\App\Http\Controllers\Alumno\EvaluacionDocenteController::class, 'store'])->name('evaluacion-docente.store');
        });

    // ═══════════════════════════════════════════
    // DOCENTE
    // ═══════════════════════════════════════════
    Route::middleware(['check.role:docente'])
        ->prefix('docente')
        ->name('docente.')
        ->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\Docente\DashboardController::class, 'index'])->name('dashboard');
            Route::get('/perfil', [\App\Http\Controllers\Docente\PerfilController::class, 'index'])->name('perfil');
            Route::get('/horario', [\App\Http\Controllers\Docente\HorarioController::class, 'index'])->name('horario');
            Route::get('/grupos', [\App\Http\Controllers\Docente\GruposController::class, 'index'])->name('grupos');

            // Asistencia
            Route::get('/asistencia/{grupo}', [\App\Http\Controllers\Docente\AsistenciaController::class, 'index'])->name('asistencia');
            Route::post('/asistencia/{grupo}', [\App\Http\Controllers\Docente\AsistenciaController::class, 'store'])->name('asistencia.store');

            // Calificaciones
            Route::get('/calificaciones/{grupo}/{materia}', [\App\Http\Controllers\Docente\CalificacionesController::class, 'index'])->name('calificaciones');
            Route::post('/calificaciones/{grupo}/{materia}', [\App\Http\Controllers\Docente\CalificacionesController::class, 'store'])->name('calificaciones.store');

            // Reportes
            Route::get('/reportes/asistencia/{grupo}', [\App\Http\Controllers\Docente\ReportesController::class, 'asistencia'])->name('reportes.asistencia');
            Route::get('/reportes/rendimiento/{grupo}', [\App\Http\Controllers\Docente\ReportesController::class, 'rendimiento'])->name('reportes.rendimiento');
            Route::get('/reportes/lista-pdf/{grupo}/{materia}', [\App\Http\Controllers\Docente\ReportesController::class, 'listaPDF'])->name('reportes.lista-pdf');

            // Horas ACUDE
            Route::get('/hrs-culturales', [\App\Http\Controllers\Docente\HrsCulturalesController::class, 'index'])->name('hrs-culturales');
            Route::post('/hrs-culturales', [\App\Http\Controllers\Docente\HrsCulturalesController::class, 'store'])->name('hrs-culturales.store');
            Route::patch('/hrs-culturales/{id}/validar', [\App\Http\Controllers\Docente\HrsCulturalesController::class, 'validar'])->name('hrs-culturales.validar');

            // Evaluación docente (solo ver)
            Route::get('/mi-evaluacion', [\App\Http\Controllers\Docente\EvaluacionController::class, 'index'])->name('mi-evaluacion');

            Route::get('/noticias', [\App\Http\Controllers\Docente\NoticiasController::class, 'index'])->name('noticias');
        });

    // ═══════════════════════════════════════════
    // DIRECTOR DE CARRERA
    // ═══════════════════════════════════════════
    Route::middleware(['check.role:director_carrera'])
        ->prefix('director')
        ->name('director.')
        ->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\Director\DashboardController::class, 'index'])->name('dashboard');
            Route::get('/perfil', [\App\Http\Controllers\Director\PerfilController::class, 'index'])->name('perfil');

            // Consultas
            Route::get('/docentes', [\App\Http\Controllers\Director\DocentesController::class, 'index'])->name('docentes');
            Route::get('/alumnos', [\App\Http\Controllers\Director\AlumnosController::class, 'index'])->name('alumnos');
            Route::get('/alumnos/{alumno}/historial', [\App\Http\Controllers\Director\AlumnosController::class, 'historial'])->name('alumnos.historial');

            // CRUD Grupos y Horarios
            Route::resource('grupos', \App\Http\Controllers\Director\GruposController::class);
            Route::resource('horarios', \App\Http\Controllers\Director\HorariosController::class);

            // Tutores de grupo
            Route::get('/tutores-grupo', [\App\Http\Controllers\Director\TutoresGrupoController::class, 'index'])->name('tutores-grupo');
            Route::patch('/tutores-grupo/{grupo}', [\App\Http\Controllers\Director\TutoresGrupoController::class, 'update'])->name('tutores-grupo.update');

            // Estadísticas
            Route::get('/estadisticas/aprobacion', [\App\Http\Controllers\Director\EstadisticasController::class, 'aprobacion'])->name('estadisticas.aprobacion');
            Route::get('/estadisticas/evaluacion-docente', [\App\Http\Controllers\Director\EstadisticasController::class, 'evaluacionDocente'])->name('estadisticas.evaluacion-docente');
            Route::get('/plan-estudios', [\App\Http\Controllers\Director\PlanEstudiosController::class, 'index'])->name('plan-estudios');

            Route::get('/noticias', [\App\Http\Controllers\Director\NoticiasController::class, 'index'])->name('noticias');
        });
});
