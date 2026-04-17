<?php

use App\Http\Controllers\Api\AlumnoController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KardexController;
use App\Http\Controllers\Api\NoticiaController;
use App\Http\Controllers\Api\NotificacionController;
use App\Http\Controllers\Api\PerfilController;
use Illuminate\Support\Facades\Route;

// ── Pública ─────────────────────────────────────────────────
Route::post('/v1/login', [AuthController::class, 'login']);

// ── Protegidas (Sanctum) ────────────────────────────────────
Route::middleware('auth:sanctum')->prefix('v1')->name('api.v1.')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/me', [AuthController::class, 'me'])->name('me');

    // Perfil
    Route::get('/perfil', [PerfilController::class, 'show'])->name('perfil.show');
    Route::patch('/perfil', [PerfilController::class, 'update'])->name('perfil.update');
    Route::put('/perfil/password', [PerfilController::class, 'cambiarPassword'])->name('perfil.password');

    // Notificaciones
    Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');
    Route::post('/notificaciones/marcar-todas', [NotificacionController::class, 'marcarTodasLeidas'])->name('notificaciones.todas-leidas');
    Route::post('/notificaciones/{notificacion}/leida', [NotificacionController::class, 'marcarLeida'])->name('notificaciones.leida');

    // Noticias (CRUD completo)
    Route::apiResource('noticias', NoticiaController::class)
        ->parameters(['noticias' => 'noticia']);

    // Alumnos (solo lectura)
    Route::get('/alumnos', [AlumnoController::class, 'index'])->name('alumnos.index');
    Route::get('/alumnos/{alumno}', [AlumnoController::class, 'show'])->name('alumnos.show');

    // Kardex
    Route::get('/kardex', [KardexController::class, 'show'])->name('kardex.me');
    Route::get('/kardex/pdf', [KardexController::class, 'pdf'])->name('kardex.me.pdf');
});
