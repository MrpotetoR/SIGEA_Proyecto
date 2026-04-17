<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AlumnoResource;
use App\Http\Resources\DocenteResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PerfilController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/perfil",
     *     tags={"Perfil"},
     *     summary="Perfil del usuario autenticado (con alumno/docente asociado)",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(response=200, description="Datos del perfil")
     * )
     */
    public function show(Request $request)
    {
        $user = $request->user()->load('alumno.carrera', 'docente');

        return response()->json([
            'user' => new UserResource($user),
            'alumno' => $user->alumno ? new AlumnoResource($user->alumno) : null,
            'docente' => $user->docente ? new DocenteResource($user->docente) : null,
        ]);
    }

    /**
     * @OA\Patch(
     *     path="/api/v1/perfil",
     *     tags={"Perfil"},
     *     summary="Actualizar nombre o email del usuario",
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="name", type="string", example="Juan Pérez"),
     *             @OA\Property(property="email", type="string", format="email", example="juan@sigea.local")
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="Perfil actualizado"),
     *     @OA\Response(response=422, description="Validación fallida")
     * )
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => "sometimes|email|max:255|unique:users,email,{$user->id}",
        ]);

        $user->update($data);

        return new UserResource($user);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/perfil/password",
     *     tags={"Perfil"},
     *     summary="Cambiar la contraseña",
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"current_password","password","password_confirmation"},
     *
     *             @OA\Property(property="current_password", type="string", format="password"),
     *             @OA\Property(property="password", type="string", format="password", minLength=8),
     *             @OA\Property(property="password_confirmation", type="string", format="password")
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="Contraseña actualizada"),
     *     @OA\Response(response=422, description="Contraseña actual incorrecta o no coincide")
     * )
     */
    public function cambiarPassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'La contraseña actual es obligatoria.',
            'current_password.current_password' => 'La contraseña actual no es correcta.',
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        $request->user()->update(['password' => Hash::make($request->password)]);

        return response()->json(['message' => 'Contraseña actualizada correctamente.']);
    }
}
