# Modelo Relacional de SIGEA

Este documento describe el modelo relacional de la base de datos de SIGEA, basado en las migraciones reales del proyecto.

## Visión general

SIGEA usa una base de datos relacional centrada en:
- `users` como origen de autenticación y perfiles de usuario.
- `carrera` como eje académico sobre el que se apoyan materias, grupos y alumnos.
- `ciclo_escolar` como contexto temporal de calificaciones, grupos y evaluaciones.
- entidades de registro académico: calificaciones, asistencia, inscripciones, semáforos.
- documentos y trámites asociados a alumnos, docentes y personal.

## Tablas principales y sus claves

### `users`
- pk: `id`
- campos: `name`, `email`, `password`, `activo`
- relaciones:
  - 1:1 con `alumno` (`user_id`)
  - 1:1 con `docente` (`user_id`)
  - 1:1 con `personal_servicios_escolares` (`user_id`)
  - 1:M con `notificaciones`
  - 1:M con `noticia`
  - 1:M con `chatbot_sesion`
  - 1:M con `constancia` como `generada_por`
  - 1:M con `historial_baja` como `autorizada_por`

### `ciclo_escolar`
- pk: `id_ciclo`
- campos: `nombre`, `fecha_inicio`, `fecha_fin`
- relaciones:
  - 1:M con `grupo`
  - 1:M con `calificacion`
  - 1:M con `semaforo_academico`
  - 1:M con `evaluacion_docente`

### `carrera`
- pk: `id_carrera`
- campos: `nombre_carrera`, `clave_carrera`, `id_director`
- relaciones:
  - 1:M con `alumno`
  - 1:M con `materia`
  - 1:M con `grupo`
  - 1:1 con `docente` como director (`id_director`)
  - M:N con `docente` en `docente_carrera`
  - 1:1 con `personal_servicios_escolares` en `personal_carrera`

### `docente`
- pk: `id_docente`
- campos: `user_id`, `nombre`, `apellidos`, `especialidad`, `horas_contrato`, `es_tutor`
- relaciones:
  - 1:1 con `users`
  - 1:M con `alumno` como tutor (`id_tutor`)
  - 1:M con `grupo` como tutor de grupo (`id_tutor`)
  - 1:M con `horario`
  - 1:M con `evaluacion_docente`
  - 1:M con `documento_docente`
  - M:N con `carrera` en `docente_carrera`

### `alumno`
- pk: `id_alumno`
- campos: `user_id`, `id_carrera`, `id_tutor`, `matricula`, `nombre`, `apellidos`, `cuatrimestre_actual`, `estatus`
- relaciones:
  - 1:1 con `users`
  - 1:M con `inscripcion`
  - 1:M con `calificacion`
  - 1:M con `asistencia`
  - 1:M con `semaforo_academico`
  - 1:M con `hrs_culturales_deportivas`
  - 1:1 con `servicio_social`
  - 1:M con `evaluacion_docente`
  - 1:M con `constancia`
  - 1:M con `pago_cuatrimestre`
  - 1:M con `documento_alumno`
  - 1:M con `historial_baja`
  - 1:1 con `padre_tutor`

### `personal_servicios_escolares`
- pk: `id_personal`
- campos: `user_id`, `nombre`, `apellidos`, `num_cedula`, `rfc`, `especialidad`
- relaciones:
  - 1:1 con `users`
  - 1:M con `documento_personal_se`
  - 1:1 con `carrera` en `personal_carrera`

### `materia`
- pk: `id_materia`
- campos: `id_carrera`, `nombre_materia`, `cuatrimestre`, `horas_semana`
- relaciones:
  - 1:M con `horario`
  - 1:M con `calificacion`

### `grupo`
- pk: `id_grupo`
- campos: `id_carrera`, `id_ciclo`, `id_tutor`, `cuatrimestre`, `clave_grupo`
- relaciones:
  - 1:M con `horario`
  - 1:M con `inscripcion`

### `horario`
- pk: `id_horario`
- campos: `id_docente`, `id_grupo`, `id_materia`, `dia_semana`, `hora_inicio`, `hora_fin`
- relaciones:
  - 1:M con `asistencia`

### `inscripcion`
- pk: `id_inscripcion`
- campos: `id_alumno`, `id_grupo`, `fecha_inscripcion`
- constraints:
  - único: (`id_alumno`, `id_grupo`)

### `calificacion`
- pk: `id_calificacion`
- campos: `id_alumno`, `id_materia`, `id_ciclo`, `parcial`, `calificacion`
- constraints:
  - único: (`id_alumno`, `id_materia`, `id_ciclo`, `parcial`)

### `asistencia`
- pk: `id_asistencia`
- campos: `id_alumno`, `id_horario`, `fecha`, `estatus`
- constraints:
  - único: (`id_alumno`, `id_horario`, `fecha`)

### `semaforo_academico`
- pk: `id_semaforo`
- campos: `id_alumno`, `id_ciclo`, `nivel`, `promedio_calificaciones`, `porcentaje_asistencia`
- constraints:
  - único: (`id_alumno`, `id_ciclo`)

### `hrs_culturales_deportivas`
- pk: `id_registro`
- campos: `id_alumno`, `tipo`, `horas_acumuladas`, `descripcion`

### `servicio_social`
- pk: `id_servicio`
- campos: `id_alumno`, `horas_acumuladas`, `horas_requeridas`, `estatus`

### `evaluacion_docente`
- pk: `id_evaluacion`
- campos: `id_docente`, `id_alumno`, `id_ciclo`, `calificacion_promedio`, `comentarios`
- constraints:
  - único: (`id_docente`, `id_alumno`, `id_ciclo`)

### `encuesta_pregunta`
- pk: `id_pregunta`
- campos: `texto_pregunta`, `orden`, `activa`

### `encuesta_respuesta`
- pk: `id_respuesta`
- campos: `id_evaluacion`, `id_pregunta`, `valor`, `comentarios`

### `constancia`
- pk: `id_constancia`
- campos: `id_alumno`, `generada_por`, `tipo`, `archivo_url`, `fecha_emision`

### `pago_cuatrimestre`
- pk: `id_pago`
- campos: `id_alumno`, `cuatrimestre`, `baucher_path`, `subido_en`
- constraints:
  - único: (`id_alumno`, `cuatrimestre`)

### `padre_tutor`
- pk: `id_padre_tutor`
- campos: `id_alumno`, `nombre`, `apellidos`, `email`, `telefono`, `telefono_emergencia`, `ine_path`

### `documento_alumno`
- pk: `id_documento`
- campos: `id_alumno`, `tipo`, `archivo_path`, `subido_en`
- constraints:
  - único: (`id_alumno`, `tipo`)

### `documento_docente`
- pk: `id_documento`
- campos: `id_docente`, `tipo`, `archivo_path`, `subido_en`
- constraints:
  - único: (`id_docente`, `tipo`)

### `documento_personal_se`
- pk: `id_documento`
- campos: `id_personal`, `tipo`, `archivo_path`, `subido_en`
- constraints:
  - único: (`id_personal`, `tipo`)

### `personal_carrera`
- pk: `id`
- campos: `id_personal`, `id_carrera`
- relaciones:
  - 1:1 entre `personal_servicios_escolares` y `carrera`

### `docente_carrera`
- pk compuesto: (`id_docente`, `id_carrera`)
- relaciones:
  - M:N entre `docente` y `carrera`

### `notificaciones`
- pk: `id`
- campos: `user_id`, `tipo`, `titulo`, `mensaje`, `icono`, `color`, `url`, `leida_en`, `created_at`

### `noticia`
- pk: `id_noticia`
- campos: `user_id`, `titulo`, `contenido`, `fecha_publicacion`, `activa`, timestamps

### `chatbot_sesion`
- pk: `id_sesion`
- campos: `user_id`, `fecha_hora`, `pregunta`, `respuesta`

### `historial_baja`
- pk: `id_baja`
- campos: `id_alumno`, `autorizada_por`, `tipo_baja`, `fecha_baja`, `fecha_reingreso`, `motivo`, timestamps

## Relaciones clave

- `users` → `alumno`, `docente`, `personal_servicios_escolares` (1:1)
- `users` → `notificaciones`, `noticia`, `chatbot_sesion`, `constancia`, `historial_baja` (1:M)
- `carrera` → `alumno`, `materia`, `grupo` (1:M)
- `carrera` → `docente` como director (`id_director`) (1:1)
- `carrera` ↔ `docente` vía `docente_carrera` (M:N)
- `carrera` ↔ `personal_servicios_escolares` vía `personal_carrera` (1:1 por carrera)
- `grupo` → `horario`, `inscripcion` (1:M)
- `materia` → `horario`, `calificacion` (1:M)
- `ciclo_escolar` → `grupo`, `calificacion`, `semaforo_academico`, `evaluacion_docente` (1:M)
- `alumno` → `inscripcion`, `calificacion`, `asistencia`, `semaforo_academico`, `hrs_culturales_deportivas`, `servicio_social`, `evaluacion_docente`, `constancia`, `pago_cuatrimestre`, `documento_alumno`, `historial_baja`, `padre_tutor` (1:M/1:1)
- `docente` → `horario`, `evaluacion_docente`, `documento_docente` (1:M)
- `horario` → `asistencia` (1:M)
- `evaluacion_docente` → `encuesta_respuesta` (1:M)
- `encuesta_pregunta` → `encuesta_respuesta` (1:M)

## Notas adicionales

- Las tablas de roles y permisos de Spatie (`roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`) no son el núcleo académico, pero se soportan sobre `users`.
- La tabla `usuario` solo agrega el campo `activo` a `users`.
- Las entidades de documentos (`documento_alumno`, `documento_docente`, `documento_personal_se`) mantienen integridad referencial con `cascadeOnDelete` y claves únicas por tipo.

---

Para un diagrama visual, abre el archivo `MODELO_RELACIONAL.drawio`.
