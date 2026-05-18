<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class CambiarPasswordController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Determinar el panel y nav del usuario
        $panel = match(true) {
            $user->hasRole('alumno')              => ['nombre' => 'Panel Alumno',    'nav' => 'partials.alumno-nav'],
            $user->hasRole('docente')              => ['nombre' => 'Panel Docente',   'nav' => 'partials.docente-nav'],
            $user->hasRole('gestor_escolar')     => ['nombre' => 'Panel Director',  'nav' => 'partials.gestor-nav'],
            $user->hasRole('gestor_escolar')  => ['nombre' => 'Panel Servicios', 'nav' => 'partials.gestor-nav'],
            default                                => ['nombre' => 'Panel',           'nav' => null],
        };

        return view('cambiar-password', compact('panel'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required'       => 'La contrasena actual es obligatoria.',
            'current_password.current_password' => 'La contrasena actual no es correcta.',
            'password.required'               => 'La nueva contrasena es obligatoria.',
            'password.confirmed'              => 'Las contrasenas no coinciden.',
            'password.min'                    => 'La contrasena debe tener al menos 8 caracteres.',
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Contrasena actualizada correctamente.');
    }
}
