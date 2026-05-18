<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlumnoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_alumno,
            'id_alumno_publico' => $this->id_alumno_publico,
            'nombre' => $this->nombre,
            'apellidos' => $this->apellidos,
            'nombre_completo' => $this->nombre_completo,
            'cuatrimestre_actual' => $this->cuatrimestre_actual,
            'estatus' => $this->estatus,
            'carrera' => new CarreraResource($this->whenLoaded('carrera')),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
