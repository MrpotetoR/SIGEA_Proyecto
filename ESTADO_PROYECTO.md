# SIGEA — Estado del Proyecto
**Sistema Integral de Gestión Escolar y Académica**

> Última actualización: Marzo 2026
> Rama principal: `main`

---

## Descripción general

SIGEA es un sistema web institucional desarrollado en **Laravel 12** con arquitectura orientada a servicios (SOA). Gestiona la información académica y administrativa de un plantel educativo, contemplando cuatro roles de usuario con paneles independientes.

---

## Stack tecnológico

| Componente | Tecnología |
|---|---|
| Backend | PHP 8.2 + Laravel 12 |
| Frontend | Blade + Tailwind CSS (via Vite) |
| Base de datos | MySQL — `sigea_db` |
| Autenticación | Laravel Breeze |
| Roles y permisos | Spatie Laravel-Permission v6 |
| Generación de PDFs | barryvdh/laravel-dompdf v3 |
| Servidor local | XAMPP (Apache + MySQL) |

---

## Configuración del entorno local

### Requisitos
- PHP 8.2+
- MySQL 8+
- Composer
- Node.js + npm
- XAMPP (o servidor equivalente)

### Instalación

```bash
# 1. Clonar el repositorio
git clone <url-repositorio> sigea
cd sigea

# 2. Instalar dependencias PHP
composer install

# 3. Instalar dependencias JS
npm install

# 4. Configurar entorno
cp .env.example .env
php artisan key:generate
```

### Configurar `.env`

```env
APP_NAME=SIGEA
APP_URL=http://localhost/sigea/public

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sigea_db
DB_USERNAME=root
DB_PASSWORD=
```

> Crear la base de datos `sigea_db` en MySQL antes de continuar.

### Base de datos y datos iniciales

```bash
# Ejecutar migraciones + seeders
php artisan migrate:fresh --seed

# Crear enlace simbólico de storage (para archivos subidos)
php artisan storage:link
```

### Compilar assets

```bash
# Desarrollo (con hot reload)
npm run dev

# Producción
npm run build
```

### Acceder al sistema

```
http://localhost/sigea/public
```

---

## Usuarios de prueba (generados por seeders)

| Email | Contraseña | Rol | Estado |
|---|---|---|---|
| `servicios@sigea.edu.mx` | `password` | Servicios Escolares | **Funcional** |
| `docente@sigea.edu.mx` | `password` | Docente | En desarrollo |
| `alumno@sigea.edu.mx` | `password` | Alumno | En desarrollo |

> No hay usuario `director_carrera` de prueba aún. Se puede crear manualmente vía Tinker.

---

## Estado de implementación por panel

### Panel Servicios Escolares — `/servicios` ✅ COMPLETO

El único panel completamente implementado. Incluye:

| Módulo | Ruta | Estado |
|---|---|---|
| Dashboard | `/servicios/dashboard` | ✅ |
| Gestión de Alumnos | `/servicios/alumnos` | ✅ CRUD completo + baja/reingreso |
| Gestión de Docentes | `/servicios/docentes` | ✅ CRUD completo |
| Carreras | `/servicios/carreras` | ✅ CRUD completo |
| Materias | `/servicios/materias` | ✅ CRUD completo |
| Ciclos Escolares | `/servicios/ciclos` | ✅ CRUD completo |
| Inscripciones | `/servicios/inscripciones` | ✅ |
| Constancias | `/servicios/constancias` | ✅ (PDF pendiente de vista) |
| Noticias | `/servicios/noticias` | ✅ CRUD completo |
| Documentos Institucionales | `/servicios/documentos` | ✅ CRUD + subida de archivos |
| Reportes | `/servicios/reportes` | ✅ (estadísticas por carrera/ciclo) |

### Panel Alumno — `/alumno` 🚧 EN DESARROLLO

Muestra pantalla "próximamente". Controladores creados, vistas pendientes.

| Módulo | Controlador | Vistas |
|---|---|---|
| Dashboard | ✅ | Parcial |
| Calificaciones | ✅ | Parcial |
| Horario | ✅ | Parcial |
| Kardex | ✅ | ❌ |
| Historial académico | ✅ | ❌ |
| Evaluación docente | ✅ | ❌ |
| Horas culturales | ✅ | ❌ |
| Servicio social | ✅ | ❌ |
| Noticias | ✅ | ❌ |
| Mis docentes | ✅ | ❌ |

### Panel Docente — `/docente` 🚧 EN DESARROLLO

Muestra pantalla "próximamente". Controladores creados, vistas pendientes.

| Módulo | Controlador | Vistas |
|---|---|---|
| Dashboard | ✅ | Parcial |
| Grupos | ✅ | ❌ |
| Horario | ✅ | ❌ |
| Asistencia | ✅ | ❌ |
| Calificaciones | ✅ | ❌ |
| Reportes | ✅ | ❌ |
| Evaluación (resultados) | ✅ | ❌ |
| Horas culturales | ✅ | ❌ |
| Servicio social | ✅ | ❌ |

### Panel Director de Carrera — `/director` 🚧 EN DESARROLLO

Muestra pantalla "próximamente". Controladores creados, vistas pendientes.

| Módulo | Controlador | Vistas |
|---|---|---|
| Dashboard | ✅ | Parcial |
| Alumnos | ✅ | ❌ |
| Docentes | ✅ | ❌ |
| Grupos | ✅ | ❌ |
| Horarios | ✅ | ❌ |
| Índice de aprobación | ✅ | ❌ |
| Plan de estudios | ✅ | ❌ |
| Evaluación docente | ✅ | ❌ |
| Noticias | ✅ | ❌ |

---

## Arquitectura del proyecto

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/           # Controladores de autenticación (Breeze)
│   │   ├── Alumno/         # 11 controladores — panel alumno
│   │   ├── Docente/        # 12 controladores — panel docente
│   │   ├── Director/       # 11 controladores — panel director
│   │   └── Servicios/      # 11 controladores — panel servicios ✅
│   └── Middleware/
│       └── CheckRole.php   # Middleware de rol + verificación activo
├── Models/                 # 22 modelos Eloquent
│   ├── User.php            # Extiende con HasRoles (Spatie) + panelUrl()
│   ├── Alumno.php
│   ├── Docente.php
│   ├── Carrera.php
│   ├── CicloEscolar.php
│   ├── Grupo.php
│   ├── Materia.php
│   ├── Horario.php
│   ├── Inscripcion.php
│   ├── Calificacion.php
│   ├── Asistencia.php
│   ├── SemaforoAcademico.php
│   ├── EvaluacionDocente.php
│   ├── EncuestaPregunta.php
│   ├── EncuestaRespuesta.php
│   ├── Constancia.php
│   ├── HistorialBaja.php
│   ├── HrsCulturalesDeportivas.php
│   ├── ServicioSocial.php
│   ├── Noticia.php
│   ├── DocumentoInstitucional.php
│   └── ChatbotSesion.php
└── Services/               # Capa de lógica de negocio (SOA)
    ├── KardexService.php
    ├── AsistenciaService.php
    ├── CalificacionService.php
    ├── SemaforoAcademicoService.php
    ├── EstadisticasCarreraService.php
    ├── GrupoService.php
    └── PDFService.php
```

### Roles del sistema

| Rol (Spatie) | Panel | Middleware |
|---|---|---|
| `alumno` | `/alumno/*` | `role:alumno` |
| `docente` | `/docente/*` | `role:docente` |
| `director_carrera` | `/director/*` | `role:director_carrera` |
| `servicios_escolares` | `/servicios/*` | `role:servicios_escolares` |

### Flujo de autenticación

```
/ → (no autenticado) → /login
        ↓ (autenticado)
    User::panelUrl()
        ├── rol alumno           → /alumno/dashboard
        ├── rol docente          → /docente/dashboard
        ├── rol director_carrera → /director/dashboard
        └── rol servicios_escolares → /servicios/dashboard
```

---

## Base de datos — Tablas implementadas

| Tabla | PK | Descripción |
|---|---|---|
| `users` | `id` | Usuarios del sistema (Breeze) + campo `activo` |
| `ciclo_escolar` | `id_ciclo` | Periodos académicos |
| `carrera` | `id_carrera` | Carreras del plantel |
| `docente` | `id_docente` | Perfil docente (FK → users) |
| `alumno` | `id_alumno` | Perfil alumno (FK → users, carrera) |
| `materia` | `id_materia` | Catálogo de materias por carrera |
| `grupo` | `id_grupo` | Grupos académicos por ciclo |
| `horario` | `id_horario` | Horarios de grupos |
| `inscripcion` | `id_inscripcion` | Inscripciones alumno-grupo |
| `calificacion` | `id_calificacion` | Calificaciones por periodo |
| `asistencia` | `id_asistencia` | Registro de asistencia |
| `semaforo_academico` | `id_semaforo` | Estado académico (verde/amarillo/rojo) |
| `hrs_culturales_deportivas` | `id_hrs` | Horas culturales/deportivas |
| `servicio_social` | `id_servicio` | Registro de servicio social |
| `evaluacion_docente` | `id_evaluacion` | Evaluaciones de docentes |
| `encuesta_pregunta` | `id_pregunta` | Preguntas de evaluación docente |
| `encuesta_respuesta` | `id_respuesta` | Respuestas de evaluación |
| `constancia` | `id_constancia` | Constancias generadas |
| `historial_baja` | `id_historial` | Historial de bajas/reingresos |
| `noticia` | `id_noticia` | Noticias institucionales |
| `documento_institucional` | `id_documento` | Documentos subidos |
| `chatbot_sesion` | `id_sesion` | Sesiones del chatbot (futuro) |
| `permissions`, `roles`, etc. | — | Tablas Spatie Permission |

---

## Datos iniciales (Seeders)

Al ejecutar `migrate:fresh --seed` se generan:

- **4 roles**: `alumno`, `docente`, `director_carrera`, `servicios_escolares`
- **Permisos** por módulo asignados a cada rol
- **4 carreras**: DSM (Desarrollo de Software Multiplataforma), GE (Gastronomía), MEC (Mecatrónica), ADM (Administración)
- **2 ciclos escolares**: 2025-2 y 2026-1
- **7 preguntas** de evaluación docente
- **3 usuarios de prueba** con sus respectivos roles

---

## Servicios (SOA) — Estado actual

Los servicios están definidos con la firma de sus métodos pero **la implementación real está pendiente**. Actualmente los controladores realizan las consultas directamente.

| Servicio | Métodos definidos | Implementado |
|---|---|---|
| `KardexService` | `obtenerHistorialCompleto`, `calcularPromedioGeneral`, `generarKardexPDF` | 🚧 Stub |
| `AsistenciaService` | `registrarAsistencia`, `obtenerReportePorGrupo`, `generarListaPDF`, `calcularPorcentaje` | 🚧 Stub |
| `CalificacionService` | `registrarCalificaciones`, `obtenerBoletaPorAlumno`, `calcularPromedioGrupo` | 🚧 Stub |
| `SemaforoAcademicoService` | `calcularSemaforo`, `actualizarTodos`, `enviarAlertasTutores` | 🚧 Stub |
| `EstadisticasCarreraService` | `indiceAprobacion`, `distribucionSemaforo`, `promedioEvaluacionDocente` | 🚧 Stub |
| `GrupoService` | `crearGrupo`, `asignarHorario`, `asignarTutor`, `obtenerAlumnosDeGrupo` | 🚧 Stub |
| `PDFService` | `generarKardex`, `generarBoleta`, `generarConstancia`, `generarListaAsistencia` | 🚧 Stub |

---

## Pendientes prioritarios

### Para colaboradores — tareas disponibles

1. **Vistas Panel Alumno** — Crear vistas Blade para todos los módulos del alumno usando `<x-panel>` como componente base y `@include('partials.alumno-nav')` para la navegación.

2. **Vistas Panel Docente** — Igual que alumno, crear vistas para: grupos, horario, asistencia (lista + registro), calificaciones (captura), reportes.

3. **Vistas Panel Director** — Crear vistas para: alumnos, docentes, grupos, horarios, índice de aprobación.

4. **Implementar Servicios** — Mover la lógica de negocio de los controladores a la capa de servicios (`app/Services/`).

5. **Vistas PDF** — Crear las vistas en `resources/views/pdf/` para kardex, boleta, constancias y listas de asistencia (usadas por `PDFService` con DomPDF).

6. **API REST** — Implementar endpoints en `routes/api.php` con autenticación Sanctum y `JsonResource` para consumo externo (app móvil / chatbot).

---

## Convenciones del proyecto

- **Componente base de panel**: `<x-panel title="Título" panelNombre="Nombre del Panel">`
- **Navegación del panel**: `<x-slot name="nav">@include('partials.{rol}-nav')</x-slot>`
- **PKs personalizadas**: Todas las tablas SIGEA usan `id_{tabla}` como PK, no `id`
- **Relación usuario**: `docente.user_id` y `alumno.user_id` apuntan a `users.id`
- **Ciclo activo**: `CicloEscolar::cicloActual()` devuelve el ciclo vigente por fechas
- **Matrícula**: Se genera automáticamente al crear un alumno: `{CLAVE_CARRERA}{AÑO}{SECUENCIA}`
- **No usar Filament** — El frontend es exclusivamente Blade + Tailwind CSS

---

## Comandos útiles

```bash
# Limpiar caché de vistas
php artisan view:clear

# Limpiar caché de rutas
php artisan route:clear

# Limpiar toda la caché
php artisan optimize:clear

# Ver todas las rutas del sistema
php artisan route:list

# Ver solo rutas de un panel
php artisan route:list --name=servicios

# Acceder a Tinker (consola interactiva)
php artisan tinker

# Crear un usuario de director manualmente (en Tinker)
# $u = App\Models\User::create(['name'=>'Director','email'=>'director@sigea.edu.mx','password'=>bcrypt('password')]);
# $u->assignRole('director_carrera');
```
