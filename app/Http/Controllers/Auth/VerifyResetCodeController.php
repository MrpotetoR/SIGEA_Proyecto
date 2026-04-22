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
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class VerifyResetCodeController extends Controller
{
    /**
     * Maximo de intentos permitidos antes de invalidar el codigo.
     */
    public const MAX_INTENTOS = 5;

    /**
     * Muestra el formulario para ingresar el codigo de 6 digitos.
     */
    public function show(Request $request): View|RedirectResponse
    {
        $email = (string) $request->query('email', '');

        if ($email === '') {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Ingresa tu correo para recibir el codigo.']);
        }

        return view('auth.verify-code', ['email' => $email]);
    }

    /**
     * Valida el codigo ingresado. Si es correcto:
     *  - Elimina el codigo de cache.
     *  - Genera el token de reseteo interno de Laravel.
     *  - Redirige al formulario de nueva contrasena.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'codigo' => ['required', 'digits:6'],
        ], [
            'codigo.required' => 'Ingresa el codigo que recibiste por correo.',
            'codigo.digits' => 'El codigo debe tener 6 digitos.',
        ]);

        $email = strtolower($data['email']);
        $codigoIngresado = $data['codigo'];

        $user = User::where('email', $data['email'])->first();
        if (! $user) {
            throw ValidationException::withMessages([
                'codigo' => 'No encontramos una cuenta con ese correo.',
            ]);
        }

        $cacheKey = PasswordResetLinkController::codigoCacheKey($email);
        $intentosKey = PasswordResetLinkController::intentosCacheKey($email);

        $codigoGuardado = Cache::get($cacheKey);

        if (! $codigoGuardado) {
            throw ValidationException::withMessages([
                'codigo' => 'El codigo expiro o no existe. Solicita uno nuevo.',
            ]);
        }

        // Contador de intentos.
        $intentos = (int) Cache::get($intentosKey, 0);
        if ($intentos >= self::MAX_INTENTOS) {
            Cache::forget($cacheKey);
            Cache::forget($intentosKey);
            throw ValidationException::withMessages([
                'codigo' => 'Superaste el numero maximo de intentos. Solicita un nuevo codigo.',
            ]);
        }

        if (! hash_equals((string) $codigoGuardado, (string) $codigoIngresado)) {
            Cache::put($intentosKey, $intentos + 1, PasswordResetLinkController::CODIGO_TTL);
            throw ValidationException::withMessages([
                'codigo' => 'El codigo es incorrecto. Intenta de nuevo.',
            ]);
        }

        // Codigo correcto: limpia la cache y genera token de Laravel.
        Cache::forget($cacheKey);
        Cache::forget($intentosKey);

        $token = Password::broker()->createToken($user);

        return redirect()
            ->route('password.reset', ['token' => $token, 'email' => $user->email])
            ->with('status', 'Codigo verificado correctamente. Ahora crea tu nueva contrasena.');
    }

    /**
     * Reenvia un nuevo codigo al correo.
     *
     * @throws ValidationException
     */
    public function resend(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $data['email'])->first();
        if (! $user) {
            throw ValidationException::withMessages([
                'email' => 'No encontramos una cuenta con ese correo.',
            ]);
        }

        $codigo = (string) random_int(100000, 999999);

        Cache::put(
            PasswordResetLinkController::codigoCacheKey($user->email),
            $codigo,
            PasswordResetLinkController::CODIGO_TTL
        );
        Cache::put(
            PasswordResetLinkController::intentosCacheKey($user->email),
            0,
            PasswordResetLinkController::CODIGO_TTL
        );

        try {
            Mail::to($user->email)->send(
                new CodigoRecuperacionPasswordMail(
                    codigo: $codigo,
                    nombreUsuario: $user->name ?? 'usuario',
                    minutosExpiracion: (int) (PasswordResetLinkController::CODIGO_TTL / 60),
                )
            );
        } catch (\Throwable $e) {
            Log::warning('No se pudo reenviar el correo de recuperacion: '.$e->getMessage());
        }

        return redirect()
            ->route('password.verify-code.show', ['email' => $user->email])
            ->with('status', 'Reenviamos un nuevo codigo a tu correo.');
    }
}
