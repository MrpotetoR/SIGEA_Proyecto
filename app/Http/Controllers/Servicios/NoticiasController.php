<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use App\Models\Noticia;
use App\Services\NotificacionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class NoticiasController extends Controller
{
    public function __construct(private NotificacionService $notificaciones) {}

    public function index()
    {
        $noticias = Noticia::with('autor')->orderByDesc('fecha_publicacion')->paginate(15);
        return view('servicios.noticias.index', compact('noticias'));
    }

    public function create() { return view('servicios.noticias.create'); }

    public function store(Request $request)
    {
        $data = $this->validarFormulario($request);

        $imagenUrl = $this->resolverImagen($request);
        [$fechaPub, $inmediata] = $this->resolverFechaPublicacion($request);

        $roles = $request->audiencia === 'roles' ? $request->roles : null;

        $noticia = Noticia::create([
            'user_id'           => auth()->id(),
            'titulo'            => $request->titulo,
            'contenido'         => $request->contenido,
            'imagen_url'        => $imagenUrl,
            'fecha_publicacion' => $fechaPub,
            'activa'            => true,
            'destinatarios'     => $roles,
            'notificado'        => false,
        ]);

        // Publicación inmediata → notificar ahora y marcar como notificado.
        // Programada → quedará pendiente; NotificacionController la disparará al llegar el momento.
        if ($inmediata) {
            $this->notificaciones->notificarNuevaNoticia(
                $noticia->titulo,
                route('noticias.show', $noticia),
                $roles
            );
            $noticia->update(['notificado' => true]);
            $msg = 'Noticia publicada.';
        } else {
            $msg = 'Noticia programada para el ' . $fechaPub->format('d/m/Y H:i') . '.';
        }

        return redirect()->route('servicios.noticias.index')->with('success', $msg);
    }

    public function show(Noticia $noticia) { return view('servicios.noticias.show', compact('noticia')); }

    public function edit(Noticia $noticia) { return view('servicios.noticias.edit', compact('noticia')); }

    public function update(Request $request, Noticia $noticia)
    {
        $data = $this->validarFormulario($request, $noticia);

        $imagenUrl = $this->resolverImagen($request, $noticia);

        if ($request->boolean('quitar_imagen') && !$request->hasFile('imagen') && !$request->imagen_url) {
            $this->borrarImagenLocal($noticia);
            $imagenUrl = null;
        }

        [$fechaPub, $inmediata] = $this->resolverFechaPublicacion($request);
        $roles = $request->audiencia === 'roles' ? $request->roles : null;

        $noticia->update([
            'titulo'            => $request->titulo,
            'contenido'         => $request->contenido,
            'imagen_url'        => $imagenUrl,
            'fecha_publicacion' => $fechaPub,
            'destinatarios'     => $roles,
        ]);

        // Si se re-programó hacia el futuro, permitir re-notificar cuando llegue el momento.
        if ($fechaPub->isFuture()) {
            $noticia->update(['notificado' => false]);
        } elseif ($inmediata && !$noticia->notificado) {
            $this->notificaciones->notificarNuevaNoticia(
                $noticia->titulo,
                route('noticias.show', $noticia),
                $roles
            );
            $noticia->update(['notificado' => true]);
        }

        return redirect()->route('servicios.noticias.index')->with('success', 'Noticia actualizada.');
    }

    public function destroy(Noticia $noticia)
    {
        $this->borrarImagenLocal($noticia);
        $noticia->delete();
        return redirect()->route('servicios.noticias.index')->with('success', 'Noticia eliminada.');
    }

    // ────────────────────────── helpers ──────────────────────────

    /** Valida el formulario de create/update y aplica reglas de fecha/hora. */
    private function validarFormulario(Request $request, ?Noticia $noticia = null): array
    {
        $rules = [
            'titulo'            => 'required|string|max:200',
            'contenido'         => 'required|string',
            'tipo_publicacion'  => ['required', Rule::in(['inmediata', 'programada'])],
            'fecha_publicacion' => 'required_if:tipo_publicacion,programada|nullable|date',
            'hora_publicacion'  => 'required_if:tipo_publicacion,programada|nullable|date_format:H:i',
            'audiencia'         => 'required|in:todos,roles',
            'roles'             => 'required_if:audiencia,roles|array|min:1',
            'roles.*'           => 'in:servicios_escolares,director_carrera,docente,alumno',
            'imagen'            => 'nullable|image|max:512',
            'imagen_url'        => 'nullable|url|max:500',
        ];

        $validated = $request->validate($rules);

        // Reglas de negocio sobre fecha/hora para programada.
        if ($request->tipo_publicacion === 'programada') {
            $fechaHora = $this->combinarFechaHora($request->fecha_publicacion, $request->hora_publicacion);
            $hoy       = Carbon::today();

            if ($fechaHora->lt($hoy)) {
                throw ValidationException::withMessages([
                    'fecha_publicacion' => 'La fecha de publicación no puede ser anterior a hoy.',
                ]);
            }

            if ($fechaHora->isSameDay($hoy) && $fechaHora->lte(now())) {
                throw ValidationException::withMessages([
                    'hora_publicacion' => 'La hora debe ser posterior a la hora actual.',
                ]);
            }

            $finDelDia = Carbon::parse($request->fecha_publicacion)->setTime(23, 59, 59);
            if ($fechaHora->gt($finDelDia)) {
                throw ValidationException::withMessages([
                    'hora_publicacion' => 'La hora no puede exceder las 23:59 del día seleccionado.',
                ]);
            }
        }

        return $validated;
    }

    /** Devuelve [Carbon $fecha, bool $esInmediata]. */
    private function resolverFechaPublicacion(Request $request): array
    {
        if ($request->tipo_publicacion === 'inmediata') {
            return [now(), true];
        }
        $fechaHora = $this->combinarFechaHora($request->fecha_publicacion, $request->hora_publicacion);
        return [$fechaHora, $fechaHora->lte(now())];
    }

    private function combinarFechaHora(string $fecha, ?string $hora): Carbon
    {
        $hora = $hora ?: '00:00';
        return Carbon::parse("$fecha $hora:00");
    }

    private function resolverImagen(Request $request, ?Noticia $noticia = null): ?string
    {
        if ($request->hasFile('imagen')) {
            if ($noticia) $this->borrarImagenLocal($noticia);
            return '/storage/' . $request->file('imagen')->store('noticias', 'public');
        }
        if ($request->filled('imagen_url')) {
            if ($noticia) $this->borrarImagenLocal($noticia);
            return $request->imagen_url;
        }
        return $noticia?->imagen_url;
    }

    private function borrarImagenLocal(Noticia $noticia): void
    {
        if ($noticia->imagen_url && str_starts_with($noticia->imagen_url, '/storage/')) {
            $path = str_replace('/storage/', '', $noticia->imagen_url);
            Storage::disk('public')->delete($path);
        }
    }
}
