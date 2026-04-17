<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificacionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tipo' => $this->tipo,
            'titulo' => $this->titulo,
            'mensaje' => $this->mensaje,
            'icono' => $this->icono,
            'color' => $this->color,
            'url' => $this->url,
            'leida' => $this->estaLeida(),
            'leida_en' => $this->leida_en?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
