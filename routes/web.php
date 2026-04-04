<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

// Redirigir raíz según rol del usuario
Route::get('/', function () {
    if (auth()->check()) {
        return redirect(auth()->user()->panelUrl());
    }
    return redirect()->route('login');
});

// Dashboard genérico - redirige al panel correcto
Route::get('/dashboard', function () {
    return redirect(auth()->user()->panelUrl());
})->middleware(['auth', 'verified'])->name('dashboard');

// Perfil (Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Cambiar contraseña (accesible para todos los roles)
    Route::get('/cambiar-password',  [\App\Http\Controllers\CambiarPasswordController::class, 'index'])->name('cambiar-password');
    Route::put('/cambiar-password',  [\App\Http\Controllers\CambiarPasswordController::class, 'update'])->name('cambiar-password.update');

    // ── Notificaciones API ──
    Route::prefix('notificaciones')->name('notificaciones.')->group(function () {
        Route::get('/',           [\App\Http\Controllers\NotificacionController::class, 'index'])->name('index');
        Route::post('/{notificacion}/leida', [\App\Http\Controllers\NotificacionController::class, 'marcarLeida'])->name('leida');
        Route::post('/marcar-todas', [\App\Http\Controllers\NotificacionController::class, 'marcarTodasLeidas'])->name('todas-leidas');
    });
});

// ============================================================
// AJAX SEARCH (compartido entre paneles)
// ============================================================
Route::middleware('auth')->prefix('ajax')->name('ajax.')->group(function () {
    Route::get('/alumnos',  [\App\Http\Controllers\AjaxSearchController::class, 'alumnos'])->name('alumnos');
    Route::get('/docentes', [\App\Http\Controllers\AjaxSearchController::class, 'docentes'])->name('docentes');
    Route::get('/grupos',   [\App\Http\Controllers\AjaxSearchController::class, 'grupos'])->name('grupos');
});

// ============================================================
// PANEL ALUMNO
// ============================================================
Route::prefix('alumno')->name('alumno.')->middleware(['auth', 'verified', 'role:alumno'])->group(function () {
    Route::get('/dashboard',        [\App\Http\Controllers\Alumno\DashboardController::class,          'index'])->name('dashboard');
    Route::get('/perfil',           [\App\Http\Controllers\Alumno\PerfilController::class,             'index'])->name('perfil');
    Route::get('/horario',          [\App\Http\Controllers\Alumno\HorarioController::class,            'index'])->name('horario');
    Route::get('/calificaciones',   [\App\Http\Controllers\Alumno\CalificacionesController::class,     'index'])->name('calificaciones');
    Route::get('/kardex',           [\App\Http\Controllers\Alumno\KardexController::class,             'index'])->name('kardex');
    Route::get('/kardex/pdf',       [\App\Http\Controllers\Alumno\KardexController::class,             'descargarPdf'])->name('kardex.pdf');
    Route::get('/historial',        [\App\Http\Controllers\Alumno\HistorialAcademicoController::class, 'index'])->name('historial');
    Route::get('/horas-culturales', [\App\Http\Controllers\Alumno\HrsCulturalesController::class,     'index'])->name('horas-culturales');
    Route::get('/servicio-social',  [\App\Http\Controllers\Alumno\ServicioSocialController::class,    'index'])->name('servicio-social');
    Route::get('/evaluacion-docente',  [\App\Http\Controllers\Alumno\EvaluacionDocenteController::class, 'index'])->name('evaluacion-docente');
    Route::post('/evaluacion-docente', [\App\Http\Controllers\Alumno\EvaluacionDocenteController::class, 'store'])->name('evaluacion-docente.store');
    Route::get('/mis-docentes',     [\App\Http\Controllers\Alumno\DocentesController::class,          'index'])->name('mis-docentes');
    Route::get('/noticias',         [\App\Http\Controllers\Alumno\NoticiasController::class,          'index'])->name('noticias');
    Route::post('/chatbot',         [\App\Http\Controllers\ChatbotController::class,                  'responder'])->name('chatbot');
});

// ============================================================
// PANEL DOCENTE
// ============================================================
Route::prefix('docente')->name('docente.')->middleware(['auth', 'verified', 'role:docente'])->group(function () {
    Route::get('/dashboard',          [\App\Http\Controllers\Docente\DashboardController::class,          'index'])->name('dashboard');
    Route::get('/perfil',             [\App\Http\Controllers\Docente\PerfilController::class,             'index'])->name('perfil');
    Route::get('/grupos',             [\App\Http\Controllers\Docente\GruposController::class,             'index'])->name('grupos');
    Route::get('/horario',            [\App\Http\Controllers\Docente\HorarioController::class,            'index'])->name('horario');
    Route::get('/tutorados',          [\App\Http\Controllers\Docente\TutoradosController::class,          'index'])->name('tutorados');

    // Asistencia
    Route::get('/asistencia',           [\App\Http\Controllers\Docente\AsistenciaController::class, 'index'])->name('asistencia');
    Route::get('/asistencia/{grupo}',   [\App\Http\Controllers\Docente\AsistenciaController::class, 'show'])->name('asistencia.show');
    Route::post('/asistencia/{grupo}',  [\App\Http\Controllers\Docente\AsistenciaController::class, 'store'])->name('asistencia.store');

    // Calificaciones
    Route::get('/calificaciones',           [\App\Http\Controllers\Docente\CalificacionesController::class, 'index'])->name('calificaciones');
    Route::get('/calificaciones/{grupo}',   [\App\Http\Controllers\Docente\CalificacionesController::class, 'show'])->name('calificaciones.show');
    Route::post('/calificaciones/{grupo}',  [\App\Http\Controllers\Docente\CalificacionesController::class, 'store'])->name('calificaciones.store');
    Route::put('/calificaciones/{calificacion}', [\App\Http\Controllers\Docente\CalificacionesController::class, 'update'])->name('calificaciones.update');

    // Reportes
    Route::get('/reporte-asistencia',   [\App\Http\Controllers\Docente\ReporteAsistenciaController::class,  'index'])->name('reporte-asistencia');
    Route::get('/reporte-rendimiento',  [\App\Http\Controllers\Docente\ReporteRendimientoController::class, 'index'])->name('reporte-rendimiento');

    // Horas ACUDE
    Route::resource('horas-culturales', \App\Http\Controllers\Docente\HrsCulturalesController::class);

    // Servicio Social
    Route::resource('servicio-social', \App\Http\Controllers\Docente\ServicioSocialController::class);

    // Evaluación y Noticias
    Route::get('/evaluacion-resultados', [\App\Http\Controllers\Docente\EvaluacionResultadosController::class, 'index'])->name('evaluacion-resultados');
    Route::get('/noticias',              [\App\Http\Controllers\Docente\NoticiasController::class,              'index'])->name('noticias');

    // Chatbot
    Route::post('/chatbot', [\App\Http\Controllers\ChatbotController::class, 'responder'])->name('chatbot');
});

// ============================================================
// PANEL DIRECTOR DE CARRERA
// ============================================================
Route::prefix('director')->name('director.')->middleware(['auth', 'verified', 'role:director_carrera'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Director\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/perfil',    [\App\Http\Controllers\Director\PerfilController::class,    'index'])->name('perfil');

    // Grupos (CRUD)
    Route::resource('grupos', \App\Http\Controllers\Director\GruposController::class);
    Route::post('grupos/{grupo}/inscribir', [\App\Http\Controllers\Director\GruposController::class, 'inscribir'])->name('grupos.inscribir');
    Route::delete('grupos/{grupo}/desinscribir/{alumno}', [\App\Http\Controllers\Director\GruposController::class, 'desinscribir'])->name('grupos.desinscribir');

    // Horarios (CRUD)
    Route::resource('horarios', \App\Http\Controllers\Director\HorariosController::class);

    // Docentes
    Route::get('/docentes', [\App\Http\Controllers\Director\DocentesController::class, 'index'])->name('docentes');

    // Alumnos
    Route::get('/alumnos',              [\App\Http\Controllers\Director\AlumnosController::class, 'index'])->name('alumnos');
    Route::get('/alumnos/{alumno}/historial', [\App\Http\Controllers\Director\AlumnosController::class, 'historial'])->name('alumnos.historial');

    // Asistencia
    Route::get('/asistencia', [\App\Http\Controllers\Director\AsistenciaController::class, 'index'])->name('asistencia');

    // Índice de aprobación
    Route::get('/indice-aprobacion', [\App\Http\Controllers\Director\IndiceAprobacionController::class, 'index'])->name('indice-aprobacion');

    // Evaluación docente
    Route::get('/evaluacion-docente', [\App\Http\Controllers\Director\EvaluacionDocenteController::class, 'index'])->name('evaluacion-docente');

    // Plan de estudios
    Route::get('/plan-estudios', [\App\Http\Controllers\Director\PlanEstudiosController::class, 'index'])->name('plan-estudios');

    // Noticias
    Route::get('/noticias', [\App\Http\Controllers\Director\NoticiasController::class, 'index'])->name('noticias');

    // Chatbot
    Route::post('/chatbot', [\App\Http\Controllers\ChatbotController::class, 'responder'])->name('chatbot');
});

// ============================================================
// PANEL SERVICIOS ESCOLARES
// ============================================================
Route::prefix('servicios')->name('servicios.')->middleware(['auth', 'verified', 'role:servicios_escolares'])->group(function () {

    Route::get('/dashboard', [\App\Http\Controllers\Servicios\DashboardController::class, 'index'])
        ->name('dashboard');

    Route::resource('alumnos', \App\Http\Controllers\Servicios\AlumnosController::class);
    Route::post('/alumnos/{alumno}/baja', [\App\Http\Controllers\Servicios\AlumnosController::class, 'registrarBaja'])
        ->name('alumnos.baja');
    Route::post('/alumnos/{alumno}/reingreso', [\App\Http\Controllers\Servicios\AlumnosController::class, 'registrarReingreso'])
        ->name('alumnos.reingreso');

    Route::resource('docentes', \App\Http\Controllers\Servicios\DocentesController::class);

    Route::resource('directores', \App\Http\Controllers\Servicios\DirectoresController::class);

    Route::resource('personal', \App\Http\Controllers\Servicios\PersonalController::class);

    Route::resource('carreras', \App\Http\Controllers\Servicios\CarrerasController::class);

    Route::resource('materias', \App\Http\Controllers\Servicios\MateriasController::class);

    Route::resource('ciclos', \App\Http\Controllers\Servicios\CiclosController::class);

    Route::get('/inscripciones', [\App\Http\Controllers\Servicios\InscripcionesController::class, 'index'])
        ->name('inscripciones');
    Route::post('/inscripciones', [\App\Http\Controllers\Servicios\InscripcionesController::class, 'store'])
        ->name('inscripciones.store');
    Route::delete('/inscripciones/{inscripcion}', [\App\Http\Controllers\Servicios\InscripcionesController::class, 'destroy'])
        ->name('inscripciones.destroy');

    Route::get('/constancias', [\App\Http\Controllers\Servicios\ConstanciasController::class, 'index'])
        ->name('constancias');
    Route::post('/constancias', [\App\Http\Controllers\Servicios\ConstanciasController::class, 'store'])
        ->name('constancias.store');
    Route::get('/constancias/{constancia}/pdf', [\App\Http\Controllers\Servicios\ConstanciasController::class, 'pdf'])
        ->name('constancias.pdf');

    Route::resource('noticias', \App\Http\Controllers\Servicios\NoticiasController::class);

    Route::resource('documentos', \App\Http\Controllers\Servicios\DocumentosController::class);

    Route::get('/reportes', [\App\Http\Controllers\Servicios\ReportesController::class, 'index'])
        ->name('reportes');

    // Chatbot
    Route::post('/chatbot', [\App\Http\Controllers\ChatbotController::class, 'responder'])
        ->name('chatbot');
});

require __DIR__.'/auth.php';
