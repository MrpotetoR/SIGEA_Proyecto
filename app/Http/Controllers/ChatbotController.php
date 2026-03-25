<?php

namespace App\Http\Controllers;

use App\Models\CicloEscolar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    /**
     * Procesa el mensaje del usuario y responde usando Groq API.
     * Detecta el rol automáticamente para construir el contexto adecuado.
     */
    public function responder(Request $request)
    {
        $request->validate([
            'mensaje' => 'required|string|max:500',
        ]);

        $mensaje = $request->input('mensaje');
        $user    = $request->user();
        $rol     = $this->detectarRol($user);

        // Construir contexto según el rol del usuario
        $contexto = $this->construirContexto($user, $rol);

        // Historial de conversación por sesión
        $historial = session('chatbot_historial', []);

        $messages = [
            ['role' => 'system', 'content' => $this->getSystemPrompt($contexto, $rol)],
        ];

        foreach ($historial as $msg) {
            $messages[] = $msg;
        }

        $messages[] = ['role' => 'user', 'content' => $mensaje];

        try {
            $respuesta = $this->llamarGroq($messages);

            $historial[] = ['role' => 'user', 'content' => $mensaje];
            $historial[] = ['role' => 'assistant', 'content' => $respuesta];

            if (count($historial) > 20) {
                $historial = array_slice($historial, -20);
            }

            session(['chatbot_historial' => $historial]);

            return response()->json(['respuesta' => $respuesta]);

        } catch (\Exception $e) {
            Log::error("Chatbot Groq error [{$rol}]: " . $e->getMessage());

            return response()->json([
                'respuesta' => $this->respuestaFallback($mensaje, $rol),
            ]);
        }
    }

    /**
     * Detecta el rol principal del usuario.
     */
    private function detectarRol($user): string
    {
        if ($user->hasRole('servicios_escolares')) return 'servicios';
        if ($user->hasRole('director_carrera'))    return 'director';
        if ($user->hasRole('docente'))             return 'docente';
        if ($user->hasRole('alumno'))              return 'alumno';
        return 'general';
    }

    /**
     * Llama a la API de Groq.
     */
    private function llamarGroq(array $messages): string
    {
        $apiKey = config('services.groq.api_key');
        $model  = config('services.groq.model');
        $url    = config('services.groq.url');

        if (empty($apiKey)) {
            throw new \Exception('GROQ_API_KEY no configurada en .env');
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type'  => 'application/json',
        ])->timeout(30)->post($url, [
            'model'       => $model,
            'messages'    => $messages,
            'temperature' => 0.7,
            'max_tokens'  => 1024,
        ]);

        if ($response->failed()) {
            Log::error('Groq API response: ' . $response->body());
            throw new \Exception('Error de API Groq: ' . $response->status());
        }

        $data = $response->json();

        return $data['choices'][0]['message']['content'] ?? 'No pude generar una respuesta.';
    }

    // ─────────────────────────────────────────────────────────────
    //  CONTEXTO POR ROL
    // ─────────────────────────────────────────────────────────────

    private function construirContexto($user, string $rol): string
    {
        return match ($rol) {
            'alumno'    => $this->contextoAlumno($user),
            'docente'   => $this->contextoDocente($user),
            'director'  => $this->contextoDirector($user),
            'servicios' => $this->contextoServicios($user),
            default     => 'Usuario del sistema SIGEA.',
        };
    }

    /**
     * Contexto para ALUMNO — datos personales y académicos propios.
     */
    private function contextoAlumno($user): string
    {
        $alumno = $user->alumno;
        if (!$alumno) return 'No se encontraron datos del alumno.';

        $datos = [];
        $datos[] = "Nombre: {$alumno->nombre_completo}";
        $datos[] = "Matricula: {$alumno->matricula}";
        $datos[] = "Carrera: " . ($alumno->carrera?->nombre_carrera ?? 'No asignada');
        $datos[] = "Cuatrimestre: " . ($alumno->cuatrimestre_actual ?? 'N/D');
        $datos[] = "Estatus: {$alumno->estatus}";

        if ($alumno->promedio_general) {
            $datos[] = "Promedio general: {$alumno->promedio_general}";
        }

        $horasCulturales = $alumno->hrsCulturales()->where('tipo', 'cultural')->sum('horas_acumuladas');
        $horasDeportivas = $alumno->hrsCulturales()->where('tipo', 'deportiva')->sum('horas_acumuladas');
        $datos[] = "Horas culturales: {$horasCulturales}/30 requeridas";
        $datos[] = "Horas deportivas: {$horasDeportivas}/30 requeridas";

        $ciclo = CicloEscolar::cicloActual();
        if ($ciclo) {
            $semaforo = $alumno->semaforosAcademicos()
                ->where('id_ciclo', $ciclo->id_ciclo)->first();
            if ($semaforo) {
                $datos[] = "Semaforo academico: {$semaforo->nivel} (promedio ciclo: {$semaforo->promedio_calificaciones}, asistencia: {$semaforo->porcentaje_asistencia}%)";
            }
            $datos[] = "Ciclo actual: {$ciclo->nombre}";
        }

        $inscripciones = $alumno->inscripciones()
            ->with(['grupo.materia', 'grupo.horarios.docente'])
            ->when($ciclo, fn($q) => $q->whereHas('grupo', fn($g) => $g->where('id_ciclo', $ciclo?->id_ciclo)))
            ->get();

        if ($inscripciones->isNotEmpty()) {
            $materias = $inscripciones->map(function ($i) {
                $materia = $i->grupo?->materia?->nombre_materia ?? 'Desconocida';
                $docente = $i->grupo?->horarios?->first()?->docente?->nombre_completo ?? 'Sin docente';
                return "{$materia} (docente: {$docente})";
            })->join(', ');
            $datos[] = "Materias inscritas: {$materias}";
        }

        return implode("\n", $datos);
    }

    /**
     * Contexto para DOCENTE — sus grupos, materias, horarios y evaluaciones.
     * NO incluye datos personales de alumnos.
     */
    private function contextoDocente($user): string
    {
        $docente = $user->docente;
        if (!$docente) return 'No se encontraron datos del docente.';

        $datos = [];
        $datos[] = "Nombre: {$docente->nombre_completo}";
        $datos[] = "Especialidad: {$docente->especialidad}";
        $datos[] = "Horas contrato: {$docente->horas_contrato}";
        $datos[] = "Es tutor: " . ($docente->es_tutor ? 'Si' : 'No');

        $ciclo = CicloEscolar::cicloActual();
        if ($ciclo) {
            $datos[] = "Ciclo actual: {$ciclo->nombre}";

            // Horarios/materias que imparte
            $horarios = $docente->horarios()
                ->with(['grupo.materia', 'grupo'])
                ->whereHas('grupo', fn($q) => $q->where('id_ciclo', $ciclo->id_ciclo))
                ->get();

            if ($horarios->isNotEmpty()) {
                $materias = $horarios->map(function ($h) {
                    $materia = $h->grupo?->materia?->nombre_materia ?? 'Sin materia';
                    $grupo   = $h->grupo?->clave_grupo ?? 'Sin grupo';
                    $dia     = $h->dia_semana;
                    return "{$materia} ({$grupo}) - {$dia} {$h->hora_inicio}-{$h->hora_fin}";
                })->join(', ');
                $datos[] = "Materias/horarios: {$materias}";
            }

            // Total de alumnos en sus grupos
            $grupoIds = $horarios->pluck('grupo.id_grupo')->filter()->unique();
            $totalAlumnos = \App\Models\Inscripcion::whereIn('id_grupo', $grupoIds)->count();
            $datos[] = "Total alumnos en sus grupos: {$totalAlumnos}";

            // Grupos de tutoría
            $gruposTutoria = $docente->gruposTutoria()
                ->where('id_ciclo', $ciclo->id_ciclo)->count();
            if ($gruposTutoria > 0) {
                $datos[] = "Grupos de tutoria asignados: {$gruposTutoria}";
            }
        }

        // Promedio de evaluación docente
        if ($docente->promedio_evaluacion > 0) {
            $datos[] = "Promedio evaluacion docente: {$docente->promedio_evaluacion}/5";
        }

        return implode("\n", $datos);
    }

    /**
     * Contexto para DIRECTOR DE CARRERA — estadísticas de su carrera.
     * NO incluye datos personales individuales de alumnos ni docentes.
     */
    private function contextoDirector($user): string
    {
        $docente = $user->docente;
        if (!$docente) return 'No se encontraron datos del director.';

        $carrera = $docente->carrerasDirigidas()->first();

        $datos = [];
        $datos[] = "Nombre: {$docente->nombre_completo}";
        $datos[] = "Rol: Director de Carrera";

        if ($carrera) {
            $datos[] = "Carrera que dirige: {$carrera->nombre_carrera} ({$carrera->clave_carrera})";
            $datos[] = "Total alumnos en carrera: " . $carrera->alumnos()->count();
            $datos[] = "Alumnos activos: " . $carrera->alumnos()->where('estatus', 'activo')->count();
            $datos[] = "Total materias: " . $carrera->materias()->count();

            $ciclo = CicloEscolar::cicloActual();
            if ($ciclo) {
                $datos[] = "Ciclo actual: {$ciclo->nombre}";
                $gruposActivos = $carrera->grupos()->where('id_ciclo', $ciclo->id_ciclo)->count();
                $datos[] = "Grupos activos este ciclo: {$gruposActivos}";
            }
        }

        return implode("\n", $datos);
    }

    /**
     * Contexto para SERVICIOS ESCOLARES — estadísticas generales del sistema.
     * NO incluye datos personales de ningún usuario individual.
     */
    private function contextoServicios($user): string
    {
        $datos = [];
        $datos[] = "Nombre: {$user->name}";
        $datos[] = "Rol: Servicios Escolares";

        $datos[] = "Total alumnos registrados: " . \App\Models\Alumno::count();
        $datos[] = "Alumnos activos: " . \App\Models\Alumno::where('estatus', 'activo')->count();
        $datos[] = "Total docentes: " . \App\Models\Docente::count();
        $datos[] = "Total carreras: " . \App\Models\Carrera::count();
        $datos[] = "Total materias: " . \App\Models\Materia::count();

        $ciclo = CicloEscolar::cicloActual();
        if ($ciclo) {
            $datos[] = "Ciclo actual: {$ciclo->nombre}";
            $datos[] = "Grupos este ciclo: " . \App\Models\Grupo::where('id_ciclo', $ciclo->id_ciclo)->count();
            $datos[] = "Inscripciones este ciclo: " . \App\Models\Inscripcion::whereHas('grupo', fn($q) => $q->where('id_ciclo', $ciclo->id_ciclo))->count();
        }

        return implode("\n", $datos);
    }

    // ─────────────────────────────────────────────────────────────
    //  SYSTEM PROMPTS POR ROL
    // ─────────────────────────────────────────────────────────────

    private function getSystemPrompt(string $contexto, string $rol): string
    {
        $secciones = [
            'alumno' => "SECCIONES DEL SISTEMA PARA EL ALUMNO:\n"
                . "- Overview/Dashboard: resumen general\n"
                . "- Mi Perfil: datos personales del alumno\n"
                . "- Horario: horario de clases semanal\n"
                . "- Calificaciones: calificaciones del ciclo actual\n"
                . "- Kardex: historial de todas las calificaciones, se puede descargar en PDF\n"
                . "- Historial: historial academico completo\n"
                . "- Horas ACUDE: horas culturales y deportivas (30 requeridas de cada tipo)\n"
                . "- Servicio Social: informacion y estatus del servicio social\n"
                . "- Evaluar Docentes: evaluacion de docentes del ciclo\n"
                . "- Mis Docentes: lista de docentes actuales\n"
                . "- Noticias: avisos y noticias institucionales",

            'docente' => "SECCIONES DEL SISTEMA PARA EL DOCENTE:\n"
                . "- Dashboard: resumen de grupos y actividades\n"
                . "- Mi Perfil: datos personales del docente\n"
                . "- Mis Grupos: grupos asignados en el ciclo\n"
                . "- Horario: horario semanal de clases\n"
                . "- Asistencia: registro y consulta de asistencia por grupo\n"
                . "- Calificaciones: captura y consulta de calificaciones\n"
                . "- Reporte Asistencia: reportes de asistencia por grupo\n"
                . "- Reporte Rendimiento: reportes de rendimiento academico\n"
                . "- Horas ACUDE: gestion de horas culturales y deportivas de alumnos\n"
                . "- Servicio Social: gestion de servicio social\n"
                . "- Evaluacion Resultados: ver resultados de evaluaciones docentes\n"
                . "- Noticias: avisos y noticias institucionales",

            'director' => "SECCIONES DEL SISTEMA PARA EL DIRECTOR DE CARRERA:\n"
                . "- Dashboard: KPIs y estadisticas de la carrera\n"
                . "- Mi Perfil: datos personales\n"
                . "- Grupos: gestion CRUD de grupos de la carrera\n"
                . "- Horarios: gestion CRUD de horarios\n"
                . "- Docentes: listado de docentes de la carrera\n"
                . "- Alumnos: listado y historial de alumnos\n"
                . "- Asistencia: consulta de asistencia por grupo\n"
                . "- Indice Aprobacion: porcentaje de aprobacion/reprobacion\n"
                . "- Evaluacion Docente: resultados de evaluaciones por docente\n"
                . "- Plan de Estudios: materias organizadas por cuatrimestre\n"
                . "- Noticias: avisos institucionales",

            'servicios' => "SECCIONES DEL SISTEMA PARA SERVICIOS ESCOLARES:\n"
                . "- Dashboard: estadisticas generales del sistema\n"
                . "- Alumnos: gestion CRUD de alumnos, bajas y reingresos\n"
                . "- Docentes: gestion CRUD de docentes\n"
                . "- Carreras: gestion CRUD de carreras\n"
                . "- Materias: gestion CRUD de materias\n"
                . "- Ciclos Escolares: gestion de ciclos\n"
                . "- Inscripciones: inscribir alumnos a grupos\n"
                . "- Constancias: generar constancias en PDF\n"
                . "- Noticias: publicar y gestionar noticias\n"
                . "- Documentos: gestion de documentos\n"
                . "- Reportes: reportes generales del sistema",
        ];

        $seccionesRol = $secciones[$rol] ?? 'SECCIONES DEL SISTEMA: Navegue por el menu lateral.';

        $rolLabels = [
            'alumno'    => 'un alumno',
            'docente'   => 'un docente',
            'director'  => 'un director de carrera',
            'servicios' => 'personal de servicios escolares',
        ];
        $rolLabel = $rolLabels[$rol] ?? 'un usuario';

        return "Eres el asistente virtual de SIGEA (Sistema de Gestion Educativa Academica).\n"
            . "Tu nombre es \"Asistente SIGEA\". Eres amable, profesional y conciso.\n\n"
            . "Estas hablando con {$rolLabel}.\n\n"
            . "REGLAS:\n"
            . "- Responde SOLO sobre temas academicos y del sistema SIGEA.\n"
            . "- Usa los datos del usuario para personalizar las respuestas.\n"
            . "- Si preguntan algo que no puedes responder, sugiere contactar a servicios escolares.\n"
            . "- Responde en espanol (Mexico).\n"
            . "- Se breve: maximo 2-3 oraciones por respuesta.\n"
            . "- Usa HTML basico para formato: <b> para negritas, <br> para saltos de linea.\n"
            . "- NO uses markdown. Usa HTML.\n"
            . "- NO inventes datos. Solo usa la informacion del contexto.\n"
            . "- NUNCA reveles datos personales de otros usuarios (alumnos, docentes, etc.).\n"
            . "- Solo proporciona informacion que corresponda al rol del usuario.\n\n"
            . "{$seccionesRol}\n\n"
            . "DATOS DEL USUARIO:\n{$contexto}";
    }

    // ─────────────────────────────────────────────────────────────
    //  FALLBACK POR ROL
    // ─────────────────────────────────────────────────────────────

    private function respuestaFallback(string $mensaje, string $rol): string
    {
        $mensaje = strtolower($mensaje);

        if (str_contains($mensaje, 'hola') || str_contains($mensaje, 'buenos') || str_contains($mensaje, 'buenas')) {
            return 'Hola! Soy el asistente SIGEA. El servicio de IA esta temporalmente limitado, pero puedo ayudarte con informacion basica.';
        }

        return match ($rol) {
            'alumno' => $this->fallbackAlumno($mensaje),
            'docente' => $this->fallbackDocente($mensaje),
            'director' => $this->fallbackDirector($mensaje),
            'servicios' => $this->fallbackServicios($mensaje),
            default => 'El asistente IA no esta disponible. Intenta mas tarde.',
        };
    }

    private function fallbackAlumno(string $mensaje): string
    {
        if (str_contains($mensaje, 'calificaci') || str_contains($mensaje, 'nota')) {
            return 'Puedes ver tus calificaciones en la seccion <b>Calificaciones</b> del menu lateral.';
        }
        if (str_contains($mensaje, 'horario') || str_contains($mensaje, 'clase')) {
            return 'Tu horario esta en la seccion <b>Horario</b>.';
        }
        if (str_contains($mensaje, 'acude') || str_contains($mensaje, 'cultural') || str_contains($mensaje, 'deportiv')) {
            return 'Consulta tus horas ACUDE en la seccion <b>Horas ACUDE</b>.';
        }
        if (str_contains($mensaje, 'kardex')) {
            return 'Tu kardex esta en la seccion <b>Kardex</b>. Puedes descargarlo en PDF.';
        }
        return 'El asistente IA no esta disponible. Navega el menu lateral para encontrar lo que necesitas.';
    }

    private function fallbackDocente(string $mensaje): string
    {
        if (str_contains($mensaje, 'asistencia')) {
            return 'Puedes registrar asistencia en la seccion <b>Asistencia</b> del menu lateral.';
        }
        if (str_contains($mensaje, 'calificaci') || str_contains($mensaje, 'nota')) {
            return 'Captura calificaciones en la seccion <b>Calificaciones</b>.';
        }
        if (str_contains($mensaje, 'grupo')) {
            return 'Consulta tus grupos en la seccion <b>Mis Grupos</b>.';
        }
        if (str_contains($mensaje, 'horario')) {
            return 'Tu horario esta en la seccion <b>Horario</b>.';
        }
        if (str_contains($mensaje, 'evaluaci')) {
            return 'Los resultados de tus evaluaciones estan en <b>Evaluacion Resultados</b>.';
        }
        return 'El asistente IA no esta disponible. Navega el menu lateral para encontrar lo que necesitas.';
    }

    private function fallbackDirector(string $mensaje): string
    {
        if (str_contains($mensaje, 'alumno')) {
            return 'Consulta el listado de alumnos en la seccion <b>Alumnos</b>.';
        }
        if (str_contains($mensaje, 'docente')) {
            return 'El listado de docentes esta en la seccion <b>Docentes</b>.';
        }
        if (str_contains($mensaje, 'grupo')) {
            return 'Gestiona los grupos en la seccion <b>Grupos</b>.';
        }
        if (str_contains($mensaje, 'aprobacion') || str_contains($mensaje, 'reprobacion')) {
            return 'Consulta los indices en la seccion <b>Indice de Aprobacion</b>.';
        }
        if (str_contains($mensaje, 'evaluaci')) {
            return 'Los resultados de evaluacion docente estan en <b>Evaluacion Docente</b>.';
        }
        return 'El asistente IA no esta disponible. Navega el menu lateral para encontrar lo que necesitas.';
    }

    private function fallbackServicios(string $mensaje): string
    {
        if (str_contains($mensaje, 'alumno') || str_contains($mensaje, 'inscri')) {
            return 'Gestiona alumnos e inscripciones desde las secciones <b>Alumnos</b> e <b>Inscripciones</b>.';
        }
        if (str_contains($mensaje, 'docente')) {
            return 'Gestiona docentes en la seccion <b>Docentes</b>.';
        }
        if (str_contains($mensaje, 'constancia')) {
            return 'Genera constancias en la seccion <b>Constancias</b>.';
        }
        if (str_contains($mensaje, 'reporte')) {
            return 'Los reportes estan disponibles en la seccion <b>Reportes</b>.';
        }
        return 'El asistente IA no esta disponible. Navega el menu lateral para encontrar lo que necesitas.';
    }
}
