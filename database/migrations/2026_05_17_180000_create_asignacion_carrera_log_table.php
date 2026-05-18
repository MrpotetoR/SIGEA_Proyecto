<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Log de auditoría para asignaciones/desasignaciones/reasignaciones de
 * carreras a gestores escolares. Cada acción administrativa sensible
 * deja huella aquí (quién, cuándo, a quién, qué carrera, por qué motivo, IP).
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('asignacion_carrera_log', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Quién hizo la acción (admin o gestor con permiso especial).
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete()
                  ->comment('Usuario que ejecutó la acción');

            // A quién afectó la acción (gestor receptor / removido).
            $table->unsignedBigInteger('gestor_afectado_id')->nullable()
                  ->comment('id_personal del GestorEscolar afectado (null si fue "desasignar" sin destino)');
            $table->foreign('gestor_afectado_id')
                  ->references('id_personal')->on('gestores_escolares')
                  ->onUpdate('cascade')->onDelete('set null');

            // Carrera involucrada.
            $table->unsignedBigInteger('id_carrera');
            $table->foreign('id_carrera')
                  ->references('id_carrera')->on('carrera')
                  ->onUpdate('cascade')->onDelete('cascade');

            // Tipo de acción.
            $table->enum('accion', ['asignar', 'reasignar', 'desasignar'])
                  ->comment('asignar=primera asignación, reasignar=cambio de gestor, desasignar=quitar');

            // Motivo predefinido + texto libre opcional cuando es "otro".
            $table->enum('motivo', [
                'reorganizacion',
                'cambio_responsable',
                'alta_carrera',
                'cobertura_temporal',
                'redistribucion',
                'otro',
            ]);
            $table->string('motivo_personalizado', 32)->nullable()
                  ->comment('Texto libre cuando motivo=otro (máx 32 chars)');

            // Trazabilidad de sesión.
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 255)->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index('user_id');
            $table->index('gestor_afectado_id');
            $table->index('id_carrera');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignacion_carrera_log');
    }
};
