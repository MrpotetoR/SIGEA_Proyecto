<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarreraResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_carrera,
            'nombre_carrera' => $this->nombre_carrera,
            'clave_carrera' => $this->clave_carrera,
            'area_academica' => $this->area_academica,
            'tipo_periodo' => $this->tipo_periodo,
            'duracion_periodos' => $this->duracion_periodos,
            'duracion_estimada' => $this->duracion_estimada,
            'alumnos_count' => $this->when(isset($this->alumnos_count), $this->alumnos_count),
        ];
    }
}
