<?php

use Illuminate\Support\Facades\Route;

// ─── Rutas públicas ────────────────────────────────
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

// ─── Rutas protegidas con Sanctum ──────────────────
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::get('/session', [\App\Http\Controllers\Api\AuthController::class, 'session']);

    // Alumnos
    Route::get('/alumnos/{matricula}', [\App\Http\Controllers\Api\AlumnoApiController::class, 'show']);
    Route::get('/alumnos/{matricula}/kardex', [\App\Http\Controllers\Api\AlumnoApiController::class, 'kardex']);
    Route::get('/alumnos/{matricula}/horario', [\App\Http\Controllers\Api\AlumnoApiController::class, 'horario']);
    Route::get('/alumnos/{matricula}/calificaciones', [\App\Http\Controllers\Api\AlumnoApiController::class, 'calificaciones']);

    // Docentes
    Route::get('/docentes/{id}', [\App\Http\Controllers\Api\DocenteApiController::class, 'show']);
    Route::get('/docentes/{id}/grupos', [\App\Http\Controllers\Api\DocenteApiController::class, 'grupos']);
    Route::get('/docentes/{id}/horario', [\App\Http\Controllers\Api\DocenteApiController::class, 'horario']);

    // Calificaciones
    Route::post('/calificaciones', [\App\Http\Controllers\Api\CalificacionApiController::class, 'store']);
    Route::put('/calificaciones/{id}', [\App\Http\Controllers\Api\CalificacionApiController::class, 'update']);

    // Asistencia
    Route::get('/asistencia/{grupo}', [\App\Http\Controllers\Api\AsistenciaApiController::class, 'porGrupo']);
    Route::post('/asistencia', [\App\Http\Controllers\Api\AsistenciaApiController::class, 'store']);

    // Chatbot
    Route::post('/chatbot/mensaje', [\App\Http\Controllers\Api\ChatbotApiController::class, 'enviar']);
    Route::get('/chatbot/historial', [\App\Http\Controllers\Api\ChatbotApiController::class, 'historial']);
});
