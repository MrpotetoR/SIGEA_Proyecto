# SIGEA — Estado del Proyecto
**Sistema Integral de Gestión Escolar y Académica**

> Última actualización: 18 de marzo de 2026
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
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sigea_db
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=tu_correo@gmail.com
MAIL_PASSWORD="contraseña_de_aplicacion_google"
MAIL_FROM_ADDRESS=tu_correo@gmail.com
MAIL_FROM_NAME="SIGEA - Recuperación de contraseña"
```

> Crear la base de datos `sigea_db` en MySQL antes de continuar.
> La contraseña de correo debe ser una **contraseña de aplicación de Google** (no la contraseña normal de Gmail).

### Base de datos y datos iniciales

```bash
# Ejecutar migraciones + seeders
php artisan migrate:fresh --seed

# Crear enlace simbólico de storage (para archivos subidos y PDFs)
php artisan storage:link
```

### Compilar assets

```bash
# Desarrollo (con hot reload)
npm run dev

# Producción
npm run build
```

### Iniciar servidor

```bash
php artisan serve
# Acceder en: http://127.0.0.1:8000
```

---

## Usuarios de prueba (generados por seeders)

| Email | Contraseña | Rol | Panel |
|---|---|---|---|
| `servicios@sigea.edu.mx` | `password` | Servicios Escolares | ✅ Funcional |
| `docente@sigea.edu.mx` | `password` | Docente | 🚧 Parcial |
| `alumno@sigea.edu.mx` | `password` | Alumno | 🚧 Parcial |

> No hay usuario `director_carrera` de prueba. Crear manualmente vía Tinker (ver sección de comandos).

---

## Estado de implementación por panel

### Autenticación — ✅ COMPLETA

| Función | Estado |
|---|---|
| Login con redirección por rol | ✅ |
| Recuperación de contraseña por correo | ✅ (Gmail SMTP configurado) |
| Restablecimiento de contraseña | ✅ |
| Cambio de contraseña (usuario autenticado) | ✅ |

---

### Panel Servicios Escolares — `/servicios` ✅ COMPLETO

| Módulo | Ruta | Estado |
|---|---|---|
| Dashboard | `/servicios/dashboard` | ✅ |
| Gestión de Alumnos | `/servicios/alumnos` | ✅ CRUD + baja/reingreso + matrícula automática |
| Gestión de Docentes | `/servicios/docentes` | ✅ CRUD completo |
| Carreras | `/servicios/carreras` | ✅ CRUD + detalle con plan de estudios |
| Materias | `/servicios/materias` | ✅ CRUD + detalle por carrera |
| Ciclos Escolares | `/servicios/ciclos` | ✅ CRUD + badge de estado activo |
| Inscripciones | `/servicios/inscripciones` | ✅ |
| Constancias | `/servicios/constancias` | ✅ Generación y descarga de PDF |
| Noticias | `/servicios/noticias` | ✅ CRUD completo |
| Documentos Institucionales | `/servicios/documentos` | ✅ CRUD + subida de archivos |
| Reportes | `/servicios/reportes` | ✅ Estadísticas por carrera/ciclo |

---

### Panel Alumno — `/alumno` 🚧 VISTAS CREADAS, SIN CONECTAR

Controladores y vistas existen. Pendiente conectar con datos reales y revisar errores.

| Módulo | Controlador | Vista |
|---|---|---|
| Dashboard | ✅ | ✅ Parcial |
| Calificaciones | ✅ | ✅ Parcial |
| Horario | ✅ | ✅ Parcial |
| Kardex | ✅ | ✅ Parcial |
| Historial académico | ✅ | ✅ Parcial |
| Evaluación docente | ✅ | ✅ Parcial |
| Horas culturales | ✅ | ✅ Parcial |
| Servicio social | ✅ | ✅ Parcial |
| Noticias | ✅ | ✅ Parcial |
| Mis docentes | ✅ | ✅ Parcial |
| Perfil | ✅ | ✅ Parcial |

> "Parcial" = vista existe pero no está conectada a datos reales ni probada.

---

### Panel Docente — `/docente` 🚧 VISTAS CREADAS, SIN CONECTAR

Controladores y vistas existen. Pendiente conectar con datos reales y revisar errores.

| Módulo | Controlador | Vista |
|---|---|---|
| Dashboard | ✅ | ✅ Parcial |
| Grupos | ✅ | ✅ Parcial |
| Horario | ✅ | ✅ Parcial |
| Asistencia (lista + registro) | ✅ | ✅ Parcial |
| Calificaciones (captura) | ✅ | ✅ Parcial |
| Reporte asistencia | ✅ | ✅ Parcial |
| Reporte rendimiento | ✅ | ✅ Parcial |
| Evaluación docente (resultados) | ✅ | ✅ Parcial |
| Horas culturales | ✅ | ✅ Parcial (CRUD completo) |
| Servicio social | ✅ | ✅ Parcial (CRUD completo) |
| Noticias | ✅ | ✅ Parcial |
| Perfil | ✅ | ✅ Parcial |

---

### Panel Director de Carrera — `/director` 🚧 EN DESARROLLO

Controladores creados. Solo existe la vista del dashboard.

| Módulo | Controlador | Vista |
|---|---|---|
| Dashboard | ✅ | ✅ Parcial |
| Alumnos | ✅ | ❌ Pendiente |
| Docentes | ✅ | ❌ Pendiente |
| Grupos | ✅ | ❌ Pendiente |
| Horarios | ✅ | ❌ Pendiente |
| Índice de aprobación | ✅ | ❌ Pendiente |
| Plan de estudios | ✅ | ❌ Pendiente |
| Evaluación docente | ✅ | ❌ Pendiente |
| Noticias | ✅ | ❌ Pendiente |

---

## Arquitectura del proyecto

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/           # Autenticación Breeze + recuperación de contraseña
│   │   ├── Alumno/         # 12 controladores — panel alumno
│   │   ├── Docente/        # 12 controladores — panel docente
│   │   ├── Director/       # 11 controladores — panel director
│   │   └── Servicios/      # 11 controladores — panel servicios ✅
│   └── Middleware/
│       └── CheckRole.php   # Middleware de rol + verificación campo activo
├── Models/                 # 22 modelos Eloquent
│   ├── User.php            # HasRoles (Spatie) + panelUrl()
│   ├── Alumno.php          # scopes: activos(), deCarrera()
│   ├── Docente.php
│   ├── Carrera.php
│   ├── CicloEscolar.php    # cicloActual() por fechas
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
└── Services/               # Capa SOA — firmas definidas, implementación pendiente
    ├── KardexService.php
    ├── AsistenciaService.php
    ├── CalificacionService.php
    ├── SemaforoAcademicoService.php
    ├── EstadisticasCarreraService.php
    ├── GrupoService.php
    └── PDFService.php      # generarConstancia() funcional con DomPDF

resources/views/
├── auth/                   # Login, forgot-password, reset-password ✅
├── layouts/
│   ├── guest.blade.php     # Layout institucional para auth
│   └── panel.blade.php     # Layout base para todos los paneles
├── components/
│   ├── panel.blade.php     # Componente <x-panel> con sidebar + slots
│   └── sidebar-link.blade.php
├── partials/
│   ├── servicios-nav.blade.php
│   ├── alumno-nav.blade.php
│   └── docente-nav.blade.php
├── pdf/
│   ├── constancia.blade.php  ✅
│   └── kardex.blade.php      ✅ (stub)
├── servicios/              # ✅ Todas las vistas completas
├── alumno/                 # 🚧 Vistas creadas, sin conectar
├── docente/                # 🚧 Vistas creadas, sin conectar
└── director/               # 🚧 Solo dashboard
```

---

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
        ↓ credenciales correctas
    User::panelUrl()
        ├── alumno              → /alumno/dashboard
        ├── docente             → /docente/dashboard
        ├── director_carrera    → /director/dashboard
        └── servicios_escolares → /servicios/dashboard

/forgot-password → correo con enlace → /reset-password/{token}
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
- **4 carreras**: DSM, GE, MEC, ADM
- **2 ciclos escolares**: 2025-2 y 2026-1
- **7 preguntas** de evaluación docente
- **3 usuarios de prueba** con sus respectivos roles

---

## Servicios (SOA) — Estado actual

Los servicios tienen firmas definidas. `PDFService::generarConstancia()` es el único método con implementación funcional. El resto son stubs — los controladores realizan las consultas directamente por ahora.

| Servicio | Estado |
|---|---|
| `PDFService` | 🟡 Parcial — `generarConstancia()` funcional, otros stubs |
| `KardexService` | 🚧 Stub |
| `AsistenciaService` | 🚧 Stub |
| `CalificacionService` | 🚧 Stub |
| `SemaforoAcademicoService` | 🚧 Stub |
| `EstadisticasCarreraService` | 🚧 Stub |
| `GrupoService` | 🚧 Stub |

---

## Pendientes prioritarios

### Scope actual del equipo (Login + Servicios Escolares)
- [ ] Recorrido de prueba completo del Panel Servicios Escolares
- [ ] Verificar PDF de constancias con DomPDF en entorno local

### Siguientes módulos a implementar
1. **Panel Alumno** — Vistas existen, conectar controladores con datos reales
2. **Panel Docente** — Vistas existen, conectar con servicios de asistencia y calificaciones
3. **Panel Director** — Crear vistas faltantes (8 módulos pendientes)
4. **Implementar Servicios** — Mover lógica de negocio de controladores a `app/Services/`
5. **API REST** — Implementar endpoints en `routes/api.php` con Sanctum y `JsonResource` (después de completar los paneles)

---

## Convenciones del proyecto

- **No usar Filament** — Frontend exclusivamente Blade + Tailwind CSS
- **Componente base de panel**: `<x-panel title="Título" panelNombre="Nombre">`
- **Navegación del panel**: `<x-slot name="nav">@include('partials.{rol}-nav')</x-slot>`
- **PKs personalizadas**: Todas las tablas SIGEA usan `id_{tabla}` como PK, no `id`
- **Relación usuario**: `docente.user_id` y `alumno.user_id` → `users.id`
- **Ciclo activo**: `CicloEscolar::cicloActual()` devuelve el ciclo vigente por fechas
- **Matrícula**: Se genera automáticamente al crear alumno: `{CLAVE_CARRERA}{AÑO}{SECUENCIA}`
- **Archivos subidos**: Se guardan en `storage/app/public/` — requiere `php artisan storage:link`

---

## Comandos útiles

```bash
# Limpiar toda la caché
php artisan optimize:clear

# Ver todas las rutas del sistema
php artisan route:list

# Ver rutas de un panel específico
php artisan route:list --name=servicios

# Crear usuario director manualmente (Tinker)
php artisan tinker
>>> $u = App\Models\User::create(['name'=>'Director','email'=>'director@sigea.edu.mx','password'=>bcrypt('password'),'activo'=>true]);
>>> $u->assignRole('director_carrera');
```
