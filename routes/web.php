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
});

// ============================================================
// PANEL ALUMNO (en desarrollo)
// ============================================================
Route::prefix('alumno')->name('alumno.')->middleware(['auth', 'verified', 'role:alumno'])->group(function () {
    Route::get('/dashboard', fn() => view('coming-soon', ['panel' => 'Panel Alumno']))->name('dashboard');
    Route::get('/{any}', fn() => redirect()->route('alumno.dashboard'))->where('any', '.*');
});

// ============================================================
// PANEL DOCENTE (en desarrollo)
// ============================================================
Route::prefix('docente')->name('docente.')->middleware(['auth', 'verified', 'role:docente'])->group(function () {
    Route::get('/dashboard', fn() => view('coming-soon', ['panel' => 'Panel Docente']))->name('dashboard');
    Route::get('/{any}', fn() => redirect()->route('docente.dashboard'))->where('any', '.*');
});

// ============================================================
// PANEL DIRECTOR DE CARRERA (en desarrollo)
// ============================================================
Route::prefix('director')->name('director.')->middleware(['auth', 'verified', 'role:director_carrera'])->group(function () {
    Route::get('/dashboard', fn() => view('coming-soon', ['panel' => 'Panel Director de Carrera']))->name('dashboard');
    Route::get('/{any}', fn() => redirect()->route('director.dashboard'))->where('any', '.*');
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
});

require __DIR__.'/auth.php';
