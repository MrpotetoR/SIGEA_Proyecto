<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Correo adicional de notificación registrado por un administrador.
 *
 * Cada admin puede registrar hasta 3 correos extras (constante MAX_POR_ADMIN)
 * que reciben copia de las notificaciones críticas — por ahora Caja Chica.
 *
 * La validación de máx 3 vive en el form request / controller (AdminPerfilController
 * o similar). No se enfuerza a nivel DB porque un UNIQUE no expresa el límite.
 *
 * Para el envío: ver Mail\SaldoCajaChicaBajo y CajaChicaService::correosDestino().
 */
class AdminCorreoNotificacion extends Model
{
    protected $table = 'admin_correos_notificacion';

    protected $fillable = [
        'admin_user_id',
        'email',
        'nombre_destinatario',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /** Máximo de correos adicionales que un admin puede registrar. */
    public const MAX_POR_ADMIN = 3;

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    // ─── Scopes ─────────────────────────────────────────────────

    public function scopeActivos($q)
    {
        return $q->where('activo', true);
    }

    public function scopeDeAdmin($q, int $adminId)
    {
        return $q->where('admin_user_id', $adminId);
    }
}
