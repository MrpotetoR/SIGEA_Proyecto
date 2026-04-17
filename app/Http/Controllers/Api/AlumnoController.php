<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AlumnoResource;
use App\Models\Alumno;
use Illuminate\Http\Request;

class AlumnoController extends Controller
{
    public function index(Request $request)
    {
        $query = Alumno::with('carrera')
            ->when($request->buscar, function ($q, $buscar) {
                $q->where(function ($q) use ($buscar) {
                    $q->where('matricula', 'like', "%{$buscar}%")
                        ->orWhere('nombre', 'like', "%{$buscar}%")
                        ->orWhere('apellidos', 'like', "%{$buscar}%");
                });
            })
            ->when($request->estatus, fn ($q, $e) => $q->where('estatus', $e))
            ->when($request->carrera, fn ($q, $c) => $q->where('id_carrera', $c))
            ->orderBy('apellidos');

        return AlumnoResource::collection($query->paginate($request->per_page ?? 15));
    }

    public function show(Alumno $alumno)
    {
        $alumno->load('carrera', 'user');

        return new AlumnoResource($alumno);
    }
}
