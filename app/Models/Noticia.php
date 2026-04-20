<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Noticia extends Model
{
    protected $table = 'noticia';
    protected $primaryKey = 'id_noticia';

    protected $fillable = [
        'user_id', 'titulo', 'contenido', 'imagen_url',
        'fecha_publicacion', 'activa', 'notificado', 'destinatarios',
    ];

    protected function casts(): array
    {
        return [
            'fecha_publicacion' => 'datetime',
            'activa'            => 'boolean',
            'notificado'        => 'boolean',
            'destinatarios'     => 'array',
        ];
    }

    public function autor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Visible al público: activa y cuya fecha/hora ya llegó. */
    public function scopeActivas($query)
    {
        return $query->where('activa', true)
                     ->where('fecha_publicacion', '<=', now())
                     ->orderByDesc('fecha_publicacion');
    }

    /** Programadas cuyo momento de publicación ya se cumplió pero aún no se notifica. */
    public function scopePendientesNotificacion($query)
    {
        return $query->where('activa', true)
                     ->where('notificado', false)
                     ->where('fecha_publicacion', '<=', now());
    }

    public function estaProgramada(): bool
    {
        return $this->activa && $this->fecha_publicacion && $this->fecha_publicacion->isFuture();
    }
}
