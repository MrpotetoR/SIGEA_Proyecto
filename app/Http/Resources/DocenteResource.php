<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocenteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_docente,
            'nombre' => $this->nombre,
            'apellidos' => $this->apellidos,
            'nombre_completo' => $this->nombre_completo,
            'especialidad' => $this->especialidad,
            'horas_contrato' => $this->horas_contrato,
            'es_tutor' => $this->es_tutor,
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
