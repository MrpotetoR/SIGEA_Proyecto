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
    public function show(Request $request)
    {
        $user = $request->user()->load('alumno.carrera', 'docente');

        return response()->json([
            'user' => new UserResource($user),
            'alumno' => $user->alumno ? new AlumnoResource($user->alumno) : null,
            'docente' => $user->docente ? new DocenteResource($user->docente) : null,
        ]);
    }

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
