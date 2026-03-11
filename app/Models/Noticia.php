<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Noticia extends Model
{
    protected $table = 'noticia';
    protected $primaryKey = 'id_noticia';

    protected $fillable = ['user_id', 'titulo', 'contenido', 'fecha_publicacion', 'activa'];

    protected function casts(): array
    {
        return [
            'fecha_publicacion' => 'date',
            'activa' => 'boolean',
        ];
    }

    public function autor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeActivas($query)
    {
        return $query->where('activa', true)->orderByDesc('fecha_publicacion');
    }
}
