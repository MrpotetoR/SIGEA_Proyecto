<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AlumnoResource;
use App\Models\Alumno;
use Illuminate\Http\Request;

class AlumnoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/alumnos",
     *     tags={"Alumnos"},
     *     summary="Listar alumnos con filtros",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="buscar", in="query", description="Texto a buscar en matrícula, nombre o apellidos", @OA\Schema(type="string")),
     *     @OA\Parameter(name="estatus", in="query", @OA\Schema(type="string", enum={"activo","baja_temporal","baja_definitiva","egresado"})),
     *     @OA\Parameter(name="carrera", in="query", description="ID de carrera", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=15)),
     *
     *     @OA\Response(response=200, description="Listado paginado")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/v1/alumnos/{id}",
     *     tags={"Alumnos"},
     *     summary="Detalle de un alumno",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Alumno encontrado"),
     *     @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function show(Alumno $alumno)
    {
        $alumno->load('carrera', 'user');

        return new AlumnoResource($alumno);
    }
}
