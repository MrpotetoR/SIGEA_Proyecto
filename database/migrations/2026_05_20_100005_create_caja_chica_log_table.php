<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Log de auditoría del módulo Caja Chica.
 *
 * Cada acción sensible (otorgar permiso, configurar tope, autorizar/rechazar
 * vale, subir factura, cerrar vale, cancelar vale, reponer fondo) deja huella
 * aquí con: quién (user_id + ip + user_agent), cuándo (created_at), qué
 * (acción + monto_antes/después), por qué (motivo + motivo_personalizado) y
 * con qué soporte (evidencia_path = imagen adjunta opcional).
 *
 * Patrón replicado de asignacion_carrera_log con extensiones:
 *   - monto_antes/monto_despues  → para snapshots de saldo del fondo
 *   - evidencia_path             → imagen JPG/PNG/PDF adjunta al motivo
 *   - vale_id / fondo_id         → al menos uno debe estar presente
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('caja_chica_log', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Quién hizo la acción (admin o gestor con permiso)
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete()
                  ->comment('Usuario que ejecutó la acción');

            // Objeto afectado (al menos uno de los dos debe estar)
            $table->unsignedBigInteger('vale_id')->nullable();
            $table->foreign('vale_id')
                  ->references('id_vale')->on('vale_caja_chica')
                  ->onUpdate('cascade')->onDelete('set null');

            $table->unsignedBigInteger('fondo_id')->nullable();
            $table->foreign('fondo_id')
                  ->references('id_fondo')->on('fondo_caja_chica')
                  ->onUpdate('cascade')->onDelete('set null');

            // Si la acción afecta a otro gestor (otorgar/revocar permiso)
            $table->unsignedBigInteger('gestor_afectado_id')->nullable();
            $table->foreign('gestor_afectado_id')
                  ->references('id_personal')->on('gestores_escolares')
                  ->onUpdate('cascade')->onDelete('set null');

            // Tipo de acción
            $table->enum('accion', [
                'otorgar_permiso',
                'revocar_permiso',
                'configurar_tope',
                'configurar_umbrales',
                'crear_vale',
                'editar_vale',
                'autorizar_vale',
                'rechazar_vale',
                'subir_factura',
                'cerrar_vale',
                'cancelar_vale',
                'reponer_fondo',
            ]);

            // Motivo predefinido + texto libre opcional
            $table->enum('motivo', [
                'emergencia',
                'gasto_operativo',
                'transporte',
                'reparacion_menor',
                'tramite_urgente',
                'reposicion_mensual',
                'ajuste_configuracion',
                'reorganizacion',
                'otro',
            ]);
            $table->string('motivo_personalizado', 100)->nullable()
                  ->comment('Texto libre cuando motivo=otro');

            // Snapshots de monto involucrado
            $table->decimal('monto_antes', 10, 2)->nullable();
            $table->decimal('monto_despues', 10, 2)->nullable();

            // Evidencia adjunta (imagen JPG/PNG o PDF)
            $table->string('evidencia_path', 500)->nullable()
                  ->comment('Imagen/PDF de soporte al motivo (opcional)');

            // Trazabilidad de sesión
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 255)->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index('user_id');
            $table->index('vale_id');
            $table->index('fondo_id');
            $table->index('gestor_afectado_id');
            $table->index('accion');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caja_chica_log');
    }
};
