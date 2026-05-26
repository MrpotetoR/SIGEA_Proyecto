<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Correos adicionales de notificación por administrador.
 *
 * Cada admin puede registrar hasta 3 correos extras (además del suyo
 * principal) que reciben copia de las notificaciones críticas — por ahora
 * Caja Chica (saldo bajo, reposición pendiente).
 *
 * Restricción de "máx 3 por admin" validada en código (AdminCorreoNotificacion
 * model + UI).
 *
 * Configurado desde: Panel Admin → Mi Perfil → Correos adicionales.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('admin_correos_notificacion', function (Blueprint $table) {
            $table->id();

            $table->foreignId('admin_user_id')
                  ->constrained('users', 'id')
                  ->cascadeOnDelete()
                  ->comment('Admin propietario del correo adicional');

            $table->string('email', 150);
            $table->string('nombre_destinatario', 100)->nullable()
                  ->comment('Para personalizar saludo del correo');

            $table->boolean('activo')->default(true)
                  ->comment('Permite pausar el envío sin borrar el registro');

            $table->timestamps();

            $table->unique(['admin_user_id', 'email']);
            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_correos_notificacion');
    }
};
