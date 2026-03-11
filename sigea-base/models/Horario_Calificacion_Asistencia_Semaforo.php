<?php
// =============================================
// 4 modelos — separar en archivos individuales
// =============================================

// ─────────────────────────────────────────────
// app/Models/Horario.php
// ─────────────────────────────────────────────

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    use HasFactory;

    protected $fillable = [
        'docente_id', 'grupo_id', 'materia_id', 'dia_semana', 'hora_inicio', 'hora_fin',
    ];

    public function docente()
    {
        return $this->belongsTo(Docente::class);
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }
}

// ─────────────────────────────────────────────
// app/Models/Calificacion.php
// ─────────────────────────────────────────────

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    use HasFactory;

    protected $table = 'calificaciones';

    protected $fillable = [
        'alumno_id', 'materia_id', 'ciclo_id', 'docente_id', 'parcial', 'calificacion',
    ];

    protected $casts = [
        'calificacion' => 'decimal:2',
        'parcial'      => 'integer',
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    public function ciclo()
    {
        return $this->belongsTo(CicloEscolar::class, 'ciclo_id');
    }

    public function docente()
    {
        return $this->belongsTo(Docente::class);
    }
}

// ─────────────────────────────────────────────
// app/Models/Asistencia.php
// ─────────────────────────────────────────────

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    use HasFactory;

    protected $fillable = ['alumno_id', 'horario_id', 'fecha', 'estatus'];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }

    public function horario()
    {
        return $this->belongsTo(Horario::class);
    }
}

// ─────────────────────────────────────────────
// app/Models/SemaforoAcademico.php
// ─────────────────────────────────────────────

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SemaforoAcademico extends Model
{
    use HasFactory;

    protected $table = 'semaforo_academico';

    protected $fillable = [
        'alumno_id', 'ciclo_id', 'nivel', 'promedio_calificaciones', 'promedio_asistencia',
    ];

    protected $casts = [
        'promedio_calificaciones' => 'decimal:2',
        'promedio_asistencia'     => 'decimal:2',
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }

    public function ciclo()
    {
        return $this->belongsTo(CicloEscolar::class, 'ciclo_id');
    }
}
