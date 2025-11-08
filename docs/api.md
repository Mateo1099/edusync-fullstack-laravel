# API Documentation

## Autenticación
Todas las rutas API (excepto login/registro) requieren autenticación mediante Bearer token.

### Headers requeridos
```
Accept: application/json
Authorization: Bearer {token}
```

## Endpoints

### Autenticación
- POST /api/login
- POST /api/register
- POST /api/logout

### Estudiantes
- GET /api/students - Listar estudiantes
- GET /api/students/{id} - Obtener estudiante
- POST /api/students - Crear estudiante
- PUT /api/students/{id} - Actualizar estudiante
- DELETE /api/students/{id} - Eliminar estudiante

### Profesores
- GET /api/teachers - Listar profesores
- GET /api/teachers/{id} - Obtener profesor
- POST /api/teachers - Crear profesor
- PUT /api/teachers/{id} - Actualizar profesor
- DELETE /api/teachers/{id} - Eliminar profesor

### Cursos
- GET /api/courses - Listar cursos
- GET /api/courses/{id} - Obtener curso
- POST /api/courses - Crear curso
- PUT /api/courses/{id} - Actualizar curso
- DELETE /api/courses/{id} - Eliminar curso

### Tareas
- GET /api/assignments - Listar tareas
- GET /api/assignments/{id} - Obtener tarea
- POST /api/assignments - Crear tarea
- PUT /api/assignments/{id} - Actualizar tarea
- DELETE /api/assignments/{id} - Eliminar tarea

### Calificaciones
- GET /api/grades - Listar calificaciones
- GET /api/grades/{id} - Obtener calificación
- POST /api/grades - Crear calificación
- PUT /api/grades/{id} - Actualizar calificación

## Respuestas

### Estructura de respuesta exitosa
```json
{
    "success": true,
    "data": {},
    "message": "Operación exitosa"
}
```

### Estructura de error
```json
{
    "success": false,
    "message": "Mensaje de error",
    "errors": {}
}
```

## Códigos de Estado
- 200: OK
- 201: Created
- 400: Bad Request
- 401: Unauthorized
- 403: Forbidden
- 404: Not Found
- 422: Unprocessable Entity
- 500: Internal Server Error
