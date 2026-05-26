<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Tareas programadas
|--------------------------------------------------------------------------
|
| Para que se ejecuten en producción, configurar el cron del servidor:
|   * * * * * cd /ruta/al/proyecto && php artisan schedule:run >> /dev/null 2>&1
|
| En desarrollo se puede correr manualmente:
|   php artisan schedule:work     # mantiene corriendo el scheduler
|   php artisan caja-chica:revisar-saldo --force   # forzar la revisión hoy
*/

// Caja Chica: revisar saldo diariamente a las 09:00 hrs.
// El comando internamente decide si notifica (solo cuando faltan ≤ 3 días al fin de mes).
Schedule::command('caja-chica:revisar-saldo')
    ->dailyAt('09:00')
    ->name('caja-chica-saldo-diario')
    ->withoutOverlapping();
