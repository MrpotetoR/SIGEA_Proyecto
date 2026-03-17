<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    /**
     * Procesa el mensaje del alumno y responde usando Groq API.
     */
    public function responder(Request $request)
    {
        $request->validate([
            'mensaje' => 'required|string|max:500',
        ]);

        $mensaje = $request->input('mensaje');
        $alumno  = $request->user()->alumno;

        // Construir contexto del alumno para el modelo
        $contexto = $this->construirContexto($alumno);

        // Obtener historial de conversación de la sesión (últimos 10 mensajes)
        $historial = session('chatbot_historial', []);

        // Construir mensajes para la API
        $messages = [
            [
                'role'    => 'system',
                'content' => $this->getSystemPrompt($contexto),
            ],
        ];

        // Agregar historial previo
        foreach ($historial as $msg) {
            $messages[] = $msg;
        }

        // Agregar mensaje actual del usuario
        $messages[] = ['role' => 'user', 'content' => $mensaje];

        try {
            $respuesta = $this->llamarGroq($messages);

            // Guardar en historial de sesión (máximo 10 intercambios)
            $historial[] = ['role' => 'user', 'content' => $mensaje];
            $historial[] = ['role' => 'assistant', 'content' => $respuesta];

            // Limitar historial a los últimos 20 mensajes (10 intercambios)
            if (count($historial) > 20) {
                $historial = array_slice($historial, -20);
            }

            session(['chatbot_historial' => $historial]);

            return response()->json(['respuesta' => $respuesta]);

        } catch (\Exception $e) {
            Log::error('Chatbot Groq error: ' . $e->getMessage());

            // Fallback: respuesta local si Groq falla
            return response()->json([
                'respuesta' => $this->respuestaFallback($mensaje, $alumno),
            ]);
        }
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

    /**
     * Construye el contexto del alumno con datos reales de la BD.
     */
    private function construirContexto($alumno): string
    {
        if (!$alumno) {
            return 'No se encontraron datos del alumno.';
        }

        $datos = [];

        // Datos básicos
        $datos[] = "Nombre: {$alumno->nombre_completo}";
        $datos[] = "Matricula: {$alumno->matricula}";
        $datos[] = "Carrera: " . ($alumno->carrera?->nombre_carrera ?? 'No asignada');
        $datos[] = "Cuatrimestre: " . ($alumno->cuatrimestre_actual ?? 'N/D');
        $datos[] = "Estatus: {$alumno->estatus}";

        // Promedio
        if ($alumno->promedio_general) {
            $datos[] = "Promedio general: {$alumno->promedio_general}";
        }

        // Horas culturales/deportivas
        $horasCulturales = $alumno->hrsCulturales()->where('tipo', 'cultural')->sum('horas_acumuladas');
        $horasDeportivas = $alumno->hrsCulturales()->where('tipo', 'deportiva')->sum('horas_acumuladas');
        $datos[] = "Horas culturales: {$horasCulturales}/30 requeridas";
        $datos[] = "Horas deportivas: {$horasDeportivas}/30 requeridas";

        // Semáforo académico del ciclo actual
        $ciclo = \App\Models\CicloEscolar::cicloActual();
        if ($ciclo) {
            $semaforo = $alumno->semaforosAcademicos()
                ->where('id_ciclo', $ciclo->id_ciclo)
                ->first();
            if ($semaforo) {
                $datos[] = "Semaforo academico: {$semaforo->nivel} (promedio ciclo: {$semaforo->promedio_calificaciones}, asistencia: {$semaforo->porcentaje_asistencia}%)";
            }
            $datos[] = "Ciclo actual: {$ciclo->nombre}";
        }

        // Materias inscritas en el ciclo
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
     * System prompt que define el comportamiento del chatbot.
     */
    private function getSystemPrompt(string $contexto): string
    {
        return <<<PROMPT
Eres el asistente virtual de SIGEA (Sistema de Gestion Educativa Academica).
Tu nombre es "Asistente SIGEA". Eres amable, profesional y conciso.

REGLAS:
- Responde SOLO sobre temas academicos y del sistema SIGEA.
- Usa los datos del alumno para personalizar las respuestas.
- Si el alumno pregunta algo que no puedes responder, sugierele contactar a servicios escolares.
- Responde en espanol (Mexico).
- Se breve: maximo 2-3 oraciones por respuesta.
- Usa HTML basico para formato: <b> para negritas, <br> para saltos de linea.
- NO uses markdown. Usa HTML.
- NO inventes datos. Solo usa la informacion del contexto.

SECCIONES DEL SISTEMA PARA EL ALUMNO:
- Overview/Dashboard: resumen general
- Mi Perfil: datos personales del alumno
- Horario: horario de clases semanal
- Calificaciones: calificaciones del ciclo actual
- Kardex: historial de todas las calificaciones, se puede descargar en PDF
- Historial: historial academico completo
- Horas ACUDE: horas culturales y deportivas (30 requeridas de cada tipo)
- Servicio Social: informacion y estatus del servicio social
- Evaluar Docentes: evaluacion de docentes del ciclo
- Mis Docentes: lista de docentes actuales
- Noticias: avisos y noticias institucionales

DATOS DEL ALUMNO:
{$contexto}
PROMPT;
    }

    /**
     * Respuestas locales de respaldo cuando Groq no está disponible.
     */
    private function respuestaFallback(string $mensaje, $alumno): string
    {
        $mensaje = strtolower($mensaje);

        if (str_contains($mensaje, 'calificaci') || str_contains($mensaje, 'nota')) {
            return 'Puedes ver tus calificaciones en el modulo <b>Calificaciones</b> del menu lateral.';
        }

        if (str_contains($mensaje, 'horario') || str_contains($mensaje, 'clase')) {
            return 'Tu horario completo esta en la seccion <b>Horario</b>.';
        }

        if (str_contains($mensaje, 'acude') || str_contains($mensaje, 'cultural') || str_contains($mensaje, 'deportiv')) {
            $total = $alumno?->hrsCulturales()->sum('horas_acumuladas') ?? 0;
            return "Llevas <b>{$total} horas</b> ACUDE registradas de 30 requeridas.";
        }

        if (str_contains($mensaje, 'kardex')) {
            return 'Tu kardex esta en la seccion <b>Kardex</b>. Puedes descargarlo en PDF.';
        }

        if (str_contains($mensaje, 'hola') || str_contains($mensaje, 'buenos') || str_contains($mensaje, 'buenas')) {
            $nombre = $alumno?->nombre ?? 'estudiante';
            return "Hola, <b>{$nombre}</b>! Soy el asistente SIGEA. El servicio de IA esta temporalmente limitado, pero puedo ayudarte con informacion basica.";
        }

        return 'El asistente IA no esta disponible en este momento. Puedes navegar el menu lateral para encontrar lo que necesitas, o intenta de nuevo mas tarde.';
    }
}
