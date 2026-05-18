<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\PagoCuatrimestre;
use App\Models\User;
use App\Services\NotificacionService;
use Illuminate\Http\Request;

class PagosController extends Controller
{
    public function __construct(private NotificacionService $notificaciones) {}

    public function index()
    {
        $alumno = Alumno::where('user_id', auth()->id())->firstOrFail();
        $alumno->loadMissing('carrera');
        $pagos  = $alumno->pagosCuatrimestre()->orderBy('cuatrimestre')->get()->keyBy('cuatrimestre');

        // El siguiente cuatrimestre permitido: mayor cuatrimestre aprobado + 1
        // (no cuenta rechazados ni pendientes como "completados")
        $maxAprobado = $alumno->pagosCuatrimestre()
            ->where('estatus', 'aprobado')
            ->max('cuatrimestre') ?? 0;

        // Permite subir si no hay pendiente ni rechazado sin resubir
        $pendiente = $alumno->pagosCuatrimestre()
            ->where('estatus', 'pendiente')
            ->exists();

        $siguiente = $pendiente ? null : ($maxAprobado + 1);

        return view('alumno.pagos', compact('alumno', 'pagos', 'siguiente'));
    }

    public function store(Request $request)
    {
        $alumno = Alumno::where('user_id', auth()->id())->firstOrFail();
        $maxPeriodos = $alumno->carrera?->max_periodos ?? 10;

        $request->validate([
            'cuatrimestre' => "required|integer|min:1|max:{$maxPeriodos}",
            'baucher'      => 'required|file|mimes:pdf|max:5120',
        ]);
        $cuatri = (int) $request->cuatrimestre;

        // Validación secuencial: solo aprobados cuentan como completados
        $maxAprobado = $alumno->pagosCuatrimestre()
            ->where('estatus', 'aprobado')
            ->max('cuatrimestre') ?? 0;

        if ($cuatri !== $maxAprobado + 1) {
            return back()->with('error', 'Solo puedes subir el váucher del ' . ($maxAprobado + 1) . '° cuatrimestre.');
        }

        // No permitir si hay uno pendiente
        if ($alumno->pagosCuatrimestre()->where('estatus', 'pendiente')->exists()) {
            return back()->with('error', 'Ya tienes un váucher pendiente de revisión. Espera la respuesta antes de subir otro.');
        }

        // No permitir duplicados aprobados
        if ($alumno->pagosCuatrimestre()->where('cuatrimestre', $cuatri)->where('estatus', 'aprobado')->exists()) {
            return back()->with('error', 'Este cuatrimestre ya tiene un váucher aprobado.');
        }

        // Si existe uno rechazado para este cuatrimestre, eliminarlo
        $rechazado = $alumno->pagosCuatrimestre()->where('cuatrimestre', $cuatri)->where('estatus', 'rechazado')->first();
        if ($rechazado) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($rechazado->baucher_path);
            $rechazado->delete();
        }

        $path = $request->file('baucher')->store("alumnos/{$alumno->id_alumno}/pagos", 'public');

        PagoCuatrimestre::create([
            'id_alumno'    => $alumno->id_alumno,
            'cuatrimestre' => $cuatri,
            'baucher_path' => $path,
            'estatus'      => 'pendiente',
            'subido_por'   => auth()->id(),
        ]);

        // Notificar a todos los usuarios de servicios escolares
        $staffUsers = User::role('gestor_escolar')->where('activo', true)->get();
        $this->notificaciones->enviarMasivo(
            $staffUsers,
            'pago',
            'Váucher pendiente de revisión',
            "El alumno {$alumno->nombre_completo} (matrícula {$alumno->id_alumno_publico}) ha cargado su váucher del {$cuatri}° cuatrimestre para revisión.",
            ['icono' => 'clipboard-check', 'color' => 'amber', 'url' => route('servicios.alumnos.show', $alumno)]
        );

        return back()->with('success', 'Váucher del ' . $cuatri . '° cuatrimestre subido. Queda pendiente de revisión por Servicios Escolares.');
    }
}
