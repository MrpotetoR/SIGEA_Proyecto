<?php
// =============================================
// Modelos restantes — separar en archivos individuales
// =============================================

// ─────────────────────────────────────────────
// app/Models/HrsCulturalDeportiva.php
// ─────────────────────────────────────────────

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrsCulturalDeportiva extends Model
{
    protected $table = 'hrs_culturales_deportivas';

    protected $fillable = [
        'alumno_id', 'validado_por', 'tipo', 'horas', 'fecha_actividad', 'descripcion', 'validado',
    ];

    protected $casts = [
        'fecha_actividad' => 'date',
        'horas'           => 'decimal:2',
        'validado'        => 'boolean',
    ];

    public function alumno()  { return $this->belongsTo(Alumno::class); }
    public function validador() { return $this->belongsTo(Docente::class, 'validado_por'); }
}

// ─────────────────────────────────────────────
// app/Models/ServicioSocial.php
// ─────────────────────────────────────────────

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicioSocial extends Model
{
    protected $table = 'servicio_social';

    protected $fillable = ['alumno_id', 'horas_acumuladas', 'horas_requeridas', 'estatus'];

    protected $casts = [
        'horas_acumuladas' => 'decimal:2',
        'horas_requeridas' => 'decimal:2',
    ];

    public function alumno() { return $this->belongsTo(Alumno::class); }

    public function getPorcentajeAttribute(): float
    {
        if ($this->horas_requeridas == 0) return 0;
        return round(($this->horas_acumuladas / $this->horas_requeridas) * 100, 2);
    }
}

// ─────────────────────────────────────────────
// app/Models/EvaluacionDocente.php
// ─────────────────────────────────────────────

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluacionDocente extends Model
{
    protected $table = 'evaluaciones_docente';

    protected $fillable = [
        'docente_id', 'alumno_id', 'materia_id', 'ciclo_id', 'calificacion_promedio', 'comentarios',
    ];

    public function docente() { return $this->belongsTo(Docente::class); }
    public function alumno()  { return $this->belongsTo(Alumno::class); }
    public function materia() { return $this->belongsTo(Materia::class); }
    public function ciclo()   { return $this->belongsTo(CicloEscolar::class, 'ciclo_id'); }
    public function respuestas() { return $this->hasMany(EncuestaRespuesta::class, 'evaluacion_id'); }
}

// ─────────────────────────────────────────────
// app/Models/EncuestaPregunta.php
// ─────────────────────────────────────────────

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EncuestaPregunta extends Model
{
    protected $table = 'encuesta_preguntas';

    protected $fillable = ['texto_pregunta', 'orden', 'activa'];

    protected $casts = ['activa' => 'boolean'];

    public function scopeActivas($query) { return $query->where('activa', true); }
}

// ─────────────────────────────────────────────
// app/Models/EncuestaRespuesta.php
// ─────────────────────────────────────────────

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EncuestaRespuesta extends Model
{
    protected $table = 'encuesta_respuestas';

    protected $fillable = ['evaluacion_id', 'pregunta_id', 'valor', 'comentario'];

    public function evaluacion() { return $this->belongsTo(EvaluacionDocente::class, 'evaluacion_id'); }
    public function pregunta()   { return $this->belongsTo(EncuestaPregunta::class, 'pregunta_id'); }
}

// ─────────────────────────────────────────────
// app/Models/Noticia.php
// ─────────────────────────────────────────────

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Noticia extends Model
{
    protected $fillable = ['autor_id', 'titulo', 'contenido', 'fecha_publicacion', 'activa'];

    protected $casts = [
        'fecha_publicacion' => 'datetime',
        'activa'            => 'boolean',
    ];

    public function autor()    { return $this->belongsTo(User::class, 'autor_id'); }
    public function scopeActivas($q) { return $q->where('activa', true); }
    public function scopeRecientes($q) { return $q->orderBy('fecha_publicacion', 'desc'); }
}

// ─────────────────────────────────────────────
// app/Models/Constancia.php
// ─────────────────────────────────────────────

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Constancia extends Model
{
    protected $fillable = ['alumno_id', 'tipo', 'archivo_url', 'fecha_emision', 'generada_por'];

    protected $casts = ['fecha_emision' => 'date'];

    public function alumno()     { return $this->belongsTo(Alumno::class); }
    public function generadaPor() { return $this->belongsTo(User::class, 'generada_por'); }
}

// ─────────────────────────────────────────────
// app/Models/HistorialBaja.php
// ─────────────────────────────────────────────

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialBaja extends Model
{
    protected $table = 'historial_bajas';

    protected $fillable = [
        'alumno_id', 'autorizada_por', 'tipo_baja', 'fecha_baja', 'fecha_reingreso', 'motivo',
    ];

    protected $casts = [
        'fecha_baja'     => 'date',
        'fecha_reingreso' => 'date',
    ];

    public function alumno()       { return $this->belongsTo(Alumno::class); }
    public function autorizadaPor() { return $this->belongsTo(User::class, 'autorizada_por'); }
}

// ─────────────────────────────────────────────
// app/Models/ChatbotSesion.php
// ─────────────────────────────────────────────

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotSesion extends Model
{
    protected $table = 'chatbot_sesiones';

    protected $fillable = ['user_id', 'fecha_hora', 'pregunta', 'respuesta'];

    protected $casts = ['fecha_hora' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
}
