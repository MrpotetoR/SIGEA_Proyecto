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
        $user = $request->user();
        $rol = $this->detectarRol($user);

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
            $respuesta = $this->llamarIA($messages);

            $historial[] = ['role' => 'user', 'content' => $mensaje];
            $historial[] = ['role' => 'assistant', 'content' => $respuesta];

            if (count($historial) > 20) {
                $historial = array_slice($historial, -20);
            }

            session(['chatbot_historial' => $historial]);

            return response()->json(['respuesta' => $respuesta]);

        } catch (\Exception $e) {
            $driver = config('services.chatbot.driver', 'local');
            Log::error("Chatbot [{$driver}] error [{$rol}]: " . $e->getMessage());

            return response()->json([
                'respuesta' => $this->respuestaFallback($mensaje, $rol),
            ]);
        }
    }

    /**
     * Limpia el historial de conversacion del chatbot del usuario actual.
     *
     * Se invoca cuando el usuario presiona "Nueva conversacion" desde el panel.
     * Borra unicamente la entrada de sesion `chatbot_historial`, que es la que
     * se le envia al modelo como contexto previo. Es per-sesion, asi que no hay
     * forma de que un usuario afecte el historial de otro.
     */
    public function resetHistorial(Request $request)
    {
        $request->session()->forget('chatbot_historial');
        return response()->json(['ok' => true]);
    }

    /**
     * Detecta el rol principal del usuario.
     */
    private function detectarRol($user): string
    {
        if ($user->hasRole('gestor_escolar'))
            return 'gestor';
        if ($user->hasRole('docente'))
            return 'docente';
        if ($user->hasRole('alumno'))
            return 'alumno';
        return 'general';
    }

    /**
     * Llama al modelo de IA según el driver configurado (local u groq).
     */
    private function llamarIA(array $messages): string
    {
        $driver = config('services.chatbot.driver', 'local');

        return match ($driver) {
            'local' => $this->llamarOllama($messages),
            'groq' => $this->llamarGroq($messages),
            default => $this->llamarOllama($messages),
        };
    }

    /**
     * Llama a Ollama (modelo local — sin API key, sin internet).
     */
    private function llamarOllama(array $messages): string
    {
        $url = config('services.ollama.url');
        $model = config('services.ollama.model');

        $response = Http::timeout(120)->post($url, [
            'model' => $model,
            'messages' => $messages,
            'temperature' => 0.4,
            'max_tokens' => 256,
            'stream' => false,
        ]);

        if ($response->failed()) {
            Log::error('Ollama response: ' . $response->body());
            throw new \Exception('Error Ollama local: ' . $response->status() . '. ¿Está corriendo ollama serve?');
        }

        $data = $response->json();

        return $data['choices'][0]['message']['content'] ?? 'No pude generar una respuesta.';
    }

    /**
     * Llama a la API de Groq (nube).
     */
    private function llamarGroq(array $messages): string
    {
        $apiKey = config('services.groq.api_key');
        $model = config('services.groq.model');
        $url = config('services.groq.url');

        if (empty($apiKey)) {
            throw new \Exception('GROQ_API_KEY no configurada en .env');
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(30)->post($url, [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => 0.4,
                    'max_tokens' => 256,
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
            'alumno'  => $this->contextoAlumno($user),
            'docente' => $this->contextoDocente($user),
            'gestor'  => $this->contextoGestor($user),
            default   => 'Usuario del sistema UDEA.',
        };
    }

    /**
     * Contexto para ALUMNO — datos personales y académicos propios.
     */
    private function contextoAlumno($user): string
    {
        $alumno = $user->alumno;
        if (!$alumno)
            return 'No se encontraron datos del alumno.';

        $datos = [];
        $datos[] = "Nombre: {$alumno->nombre_completo}";
        $datos[] = "ID: {$alumno->id_alumno_publico}";
        $datos[] = "Carrera: " . ($alumno->carrera?->nombre_carrera ?? 'No asignada');
        $datos[] = "Cuatrimestre: " . ($alumno->cuatrimestre_actual ?? 'N/D');
        $datos[] = "Estatus: {$alumno->estatus}";

        if ($alumno->promedio_general) {
            $datos[] = "Promedio general: {$alumno->promedio_general}";
        }

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
            ->with(['grupo.horarios.docente', 'grupo.horarios.materia'])
            ->when($ciclo, fn($q) => $q->whereHas('grupo', fn($g) => $g->where('id_ciclo', $ciclo?->id_ciclo)))
            ->get();

        if ($inscripciones->isNotEmpty()) {
            $materias = $inscripciones->map(function ($i) {
                $materia = $i->grupo?->horarios?->first()?->materia?->nombre_materia ?? 'Desconocida';
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
        if (!$docente)
            return 'No se encontraron datos del docente.';

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
                ->with(['grupo', 'materia'])
                ->whereHas('grupo', fn($q) => $q->where('id_ciclo', $ciclo->id_ciclo))
                ->get();

            if ($horarios->isNotEmpty()) {
                $materias = $horarios->map(function ($h) {
                    $materia = $h->materia?->nombre_materia ?? 'Sin materia';
                    $grupo = $h->grupo?->clave_grupo ?? 'Sin grupo';
                    $dia = $h->dia_semana;
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
     * Contexto para GESTOR ESCOLAR — estadísticas generales del sistema y
     * de las carreras asignadas (fusion de antiguo Servicios Escolares + Director).
     * NO incluye datos personales individuales de alumnos ni docentes.
     */
    private function contextoGestor($user): string
    {
        $datos = [];
        $datos[] = "Nombre: {$user->name}";
        $datos[] = "Rol: Gestor Escolar";

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

        // Carreras asignadas a este gestor (cuando aplique).
        $carrerasIds = $user->carrerasAsignadasIds();
        if (!empty($carrerasIds)) {
            $nombres = \App\Models\Carrera::whereIn('id_carrera', $carrerasIds)
                ->pluck('nombre_carrera')->implode(', ');
            $datos[] = "Carreras asignadas: {$nombres}";
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
                . "- Evaluacion Resultados: ver resultados de evaluaciones docentes\n"
                . "- Noticias: avisos y noticias institucionales",

            'gestor' => "SECCIONES PARA EL GESTOR ESCOLAR:\n"
                . "- Overview: resumen general\n"
                . "- Mi Perfil: datos personales\n"
                . "- Alumnos: registrar, dar de baja, reingresos, pagos\n"
                . "- Historial Alumnos: ver historial academico\n"
                . "- Inscripciones: inscribir alumnos a grupos\n"
                . "- Constancias: generar constancias en PDF\n"
                . "- Servicio Social: gestion del servicio social de los alumnos\n"
                . "- Docentes / Directores: registrar y gestionar\n"
                . "- Carreras / Materias / Ciclos Escolares: catalogos\n"
                . "- Grupos / Horarios: armar grupos y horarios\n"
                . "- Plan de Estudios: materias por cuatrimestre\n"
                . "- Asistencia / Indice Aprobacion / Eval. Docentes: reportes\n"
                . "- Noticias / Documentos: publicar avisos y archivos",
        ];

        $seccionesRol = $secciones[$rol] ?? 'SECCIONES DEL SISTEMA: Navegue por el menu lateral.';

        $rolLabels = [
            'alumno'  => 'un alumno',
            'docente' => 'un docente',
            'gestor'  => 'un gestor escolar',
        ];
        $rolLabel = $rolLabels[$rol] ?? 'un usuario';

        return "Eres el Asistente de la Universidad de los Angeles. Estas hablando con {$rolLabel}.\n\n"
            . "COMO RESPONDER:\n"
            . "- Ve directo al grano. Maximo 1-2 oraciones cortas.\n"
            . "- Habla claro y simple, como si le explicaras a un amigo. Sin tecnicismos.\n"
            . "- No uses palabras como 'modulo', 'sistema', 'plataforma', 'consultar', 'gestionar' o 'realizar'.\n"
            . "- En vez de eso usa palabras simples: 'ver', 'revisar', 'entrar a', 'abrir'.\n"
            . "- Nada de saludos largos ni frases tipo 'Por supuesto', 'Claro que si', 'Con gusto'. Solo responde.\n"
            . "- Si la respuesta esta en una seccion del menu, dilo asi: 'Esta en <b>Nombre</b>.'\n"
            . "- Si te dan datos del usuario, usalos para responder concreto (con numeros, nombres, fechas).\n"
            . "- Responde en espanol de Mexico, tono amable pero directo.\n\n"
            . "REGLAS:\n"
            . "- Solo respondes cosas de la escuela y del sistema.\n"
            . "- Usa <b> para resaltar, <br> para saltos de linea. Nada de markdown.\n"
            . "- No inventes datos. Si no sabes algo, di que pregunte en servicios escolares.\n"
            . "- No reveles datos personales de otras personas.\n\n"
            . "SECCIONES QUE PUEDES MENCIONAR:\n{$seccionesRol}\n\n"
            . "DATOS DE QUIEN PREGUNTA:\n{$contexto}";
    }

    // ─────────────────────────────────────────────────────────────
    //  FALLBACK POR ROL
    // ─────────────────────────────────────────────────────────────

    private function respuestaFallback(string $mensaje, string $rol): string
    {
        $mensaje = strtolower($mensaje);

        if (str_contains($mensaje, 'hola') || str_contains($mensaje, 'buenos') || str_contains($mensaje, 'buenas')) {
            return 'Hola! Soy el asistente de la Universidad de los Angeles. El servicio de IA esta temporalmente limitado, pero puedo ayudarte con informacion basica.';
        }

        return match ($rol) {
            'alumno'  => $this->fallbackAlumno($mensaje),
            'docente' => $this->fallbackDocente($mensaje),
            'gestor'  => $this->fallbackGestor($mensaje),
            default   => 'El asistente IA no esta disponible. Intenta mas tarde.',
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
            return 'Las horas culturales / ACUDE ya no se gestionan en este sistema.';
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

    private function fallbackGestor(string $mensaje): string
    {
        if (str_contains($mensaje, 'alumno') || str_contains($mensaje, 'inscri')) {
            return 'Esta en <b>Alumnos</b> o <b>Inscripciones</b>.';
        }
        if (str_contains($mensaje, 'docente')) {
            return 'Esta en <b>Docentes</b>.';
        }
        if (str_contains($mensaje, 'constancia')) {
            return 'Esta en <b>Constancias</b>.';
        }
        if (str_contains($mensaje, 'grupo')) {
            return 'Esta en <b>Grupos</b>.';
        }
        if (str_contains($mensaje, 'horario')) {
            return 'Esta en <b>Horarios</b>.';
        }
        if (str_contains($mensaje, 'aprobacion') || str_contains($mensaje, 'reprobacion')) {
            return 'Esta en <b>Indice de Aprobacion</b>.';
        }
        if (str_contains($mensaje, 'evaluaci')) {
            return 'Esta en <b>Eval. Docentes</b>.';
        }
        if (str_contains($mensaje, 'reporte')) {
            return 'Esta en <b>Reportes</b>.';
        }
        return 'El asistente no esta disponible. Revisa el menu lateral.';
    }
}
