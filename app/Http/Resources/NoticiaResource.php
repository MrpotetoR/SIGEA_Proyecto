<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NoticiaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_noticia,
            'titulo' => $this->titulo,
            'contenido' => $this->contenido,
            'imagen_url' => $this->imagen_url,
            'fecha_publicacion' => $this->fecha_publicacion?->toISOString(),
            'activa' => $this->activa,
            'autor' => new UserResource($this->whenLoaded('autor')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
