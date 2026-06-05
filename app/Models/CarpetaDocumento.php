<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CarpetaDocumento extends Model
{
    protected $table = 'carpeta_documento';
    protected $primaryKey = 'id_carpeta';

    protected $fillable = ['nombre', 'parent_id', 'visibilidad', 'user_id'];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id', 'id_carpeta');
    }

    public function subcarpetas()
    {
        return $this->hasMany(self::class, 'parent_id', 'id_carpeta');
    }

    public function documentos()
    {
        return $this->hasMany(DocumentoInstitucional::class, 'carpeta_id', 'id_carpeta');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeVisiblesPara(Builder $q, ?int $userId): Builder
    {
        return $q->where(function ($q) use ($userId) {
            $q->where('visibilidad', 'publica')
              ->orWhere('user_id', $userId);
        });
    }

    public function esVacia(): bool
    {
        return !$this->subcarpetas()->exists() && !$this->documentos()->exists();
    }

    public function esPrivada(): bool
    {
        return $this->visibilidad === 'privada';
    }

    /** Construye la ruta de breadcrumb desde la raíz hasta esta carpeta. */
    public function breadcrumb(): array
    {
        $ruta = [];
        $actual = $this;
        while ($actual) {
            array_unshift($ruta, $actual);
            $actual = $actual->parent;
        }
        return $ruta;
    }
}
