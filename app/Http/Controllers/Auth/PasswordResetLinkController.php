<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\CodigoRecuperacionPasswordMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * TTL (en segundos) del codigo de verificacion.
     */
    public const CODIGO_TTL = 900; // 15 minutos

    /**
     * Muestra el formulario de "recuperar contrasena".
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Maneja la solicitud de restablecimiento.
     *
     * Flujo:
     *  1. Valida que el correo exista.
     *  2. Genera un codigo de 6 digitos y lo guarda en Cache con TTL de 15 min.
     *  3. Envia el codigo por correo al usuario.
     *  4. Redirige a la pantalla de verificacion.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'No encontramos una cuenta con ese correo.']);
        }

        // Genera un codigo de 6 digitos (100000 - 999999).
        $codigo = (string) random_int(100000, 999999);

        // Guarda el codigo en cache asociado al correo.
        Cache::put($this->codigoCacheKey($user->email), $codigo, self::CODIGO_TTL);
        // Permite un pequeno contador de intentos (maximo 5 intentos por codigo).
        Cache::put($this->intentosCacheKey($user->email), 0, self::CODIGO_TTL);

        // Intenta enviar el correo. Si el mailer falla (config SMTP), loguea el error
        // pero aun asi permite al usuario avanzar para no bloquear la recuperacion.
        try {
            Mail::to($user->email)->send(
                new CodigoRecuperacionPasswordMail(
                    codigo: $codigo,
                    nombreUsuario: $user->name ?? 'usuario',
                    minutosExpiracion: (int) (self::CODIGO_TTL / 60),
                )
            );
        } catch (\Throwable $e) {
            Log::warning('No se pudo enviar el correo de recuperacion: '.$e->getMessage());
        }

        return redirect()
            ->route('password.verify-code.show', ['email' => $user->email])
            ->with('status', 'Enviamos un codigo de 6 digitos a tu correo. Revisa tu bandeja de entrada (y la carpeta de spam).');
    }

    /**
     * Genera la clave de cache para el codigo de un correo.
     */
    public static function codigoCacheKey(string $email): string
    {
        return 'pwreset_code:'.strtolower($email);
    }

    /**
     * Genera la clave de cache para el contador de intentos de un correo.
     */
    public static function intentosCacheKey(string $email): string
    {
        return 'pwreset_intentos:'.strtolower($email);
    }
}
