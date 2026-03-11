<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoInstitucional extends Model
{
    protected $table = 'documento_institucional';
    protected $primaryKey = 'id_documento';

    protected $fillable = ['user_id', 'titulo', 'tipo', 'archivo_url', 'fecha_publicacion', 'activo'];

    protected function casts(): array
    {
        return [
            'fecha_publicacion' => 'date',
            'activo' => 'boolean',
        ];
    }

    public function autor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true)->orderByDesc('fecha_publicacion');
    }
}
