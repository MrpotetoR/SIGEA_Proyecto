<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/login",
     *     tags={"Auth"},
     *     summary="Iniciar sesión y obtener token",
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"email","password"},
     *
     *             @OA\Property(property="email", type="string", format="email", example="admin@sigea.local"),
     *             @OA\Property(property="password", type="string", format="password", example="secret")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Token emitido",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="token", type="string", example="1|a8f2h9j3k..."),
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(response=422, description="Credenciales inválidas o cuenta desactivada")
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (! Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        $user = Auth::user();

        if (! $user->activo) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => ['Esta cuenta se encuentra desactivada.'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/logout",
     *     tags={"Auth"},
     *     summary="Cerrar sesión (revoca el token actual)",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(response=200, description="Sesión cerrada")
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada correctamente.']);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/me",
     *     tags={"Auth"},
     *     summary="Usuario autenticado",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(response=200, description="Datos del usuario")
     * )
     */
    public function me(Request $request)
    {
        return new UserResource($request->user());
    }
}
