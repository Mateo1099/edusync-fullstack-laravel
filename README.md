# EduSync Fullstack Laravel

Sistema de gestión educativa completo con API REST y frontend integrado.

---

## 📋 **¿Qué es este proyecto?**

**EduSync** es una plataforma educativa que permite gestionar estudiantes, docentes, cursos, tareas, calificaciones y comunicación interna. Combina:
- **Backend**: API REST construida con Laravel + Sanctum (autenticación por tokens).
- **Frontend**: Aplicación web estática (HTML/CSS/JavaScript) servida desde `public/edusync`.
- **Base de datos**: MySQL (nombre exacto: `edusync_db`).

---

## 🗄️ **BASE DE DATOS**

### **Motor y nombre**
- **Motor**: MySQL (compatible con MariaDB)
- **Nombre de la base de datos**: `edusync_db`
- **Puerto por defecto**: `3306`
- **Usuario**: `root` (puedes cambiarlo en `.env`)
- **Password**: `root` (ajusta según tu entorno)

### **Cómo crear la base de datos**
Abre MySQL Workbench o tu cliente MySQL y ejecuta:
```sql
CREATE DATABASE edusync_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Para pruebas automáticas (opcional):
```sql
CREATE DATABASE edusync_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### **Tablas principales**
El proyecto usa migraciones de Laravel. Al ejecutar `php artisan migrate` se crean automáticamente:
- `users` - Usuarios del sistema (con rol: admin, teacher, guardian, student)
- `students` - Datos adicionales de estudiantes (matrícula, teléfono, etc.)
- `teachers` - Datos de docentes
- `guardians` - Tutores/padres
- `courses` - Cursos ofrecidos
- `enrollments` - Inscripciones de estudiantes a cursos
- `assignments` - Tareas asignadas
- `grades` - Calificaciones
- `messages` - Mensajes internos (placeholder)
- `events` - Eventos escolares
- `schedules` - Horarios
- `password_reset_tokens` - Tokens de recuperación de contraseña
- `personal_access_tokens` - Tokens de autenticación Sanctum
- Y tablas de sistema Laravel (cache, jobs, sessions, migrations)

---

## 🛠️ **STACK TECNOLÓGICO**

### **Backend**
| Componente | Tecnología | Versión |
|-----------|-----------|---------|
| Lenguaje | PHP | 8.2+ |
| Framework | Laravel | 12.x |
| Autenticación | Laravel Sanctum | 4.x |
| ORM | Eloquent | (incluido en Laravel) |
| Validación | Form Requests + Regex | nativo Laravel |
| Base de datos | MySQL | 8.0+ (compatible 5.7+) |

### **Frontend**
| Componente | Tecnología | Notas |
|-----------|-----------|-------|
| Lenguaje | JavaScript (Vanilla) | Sin frameworks (React/Vue) |
| Markup | HTML5 | Semántico y accesible |
| Estilos | CSS3 | Variables CSS, sin preprocessadores |
| Gestión de estado | localStorage | Para token de autenticación |
| Cliente HTTP | Fetch API | Nativo del navegador |

### **Infraestructura y DevOps**
- **Servidor local**: `php artisan serve` (puerto 8000)
- **Docker**: Nginx + PHP-FPM + MySQL (opcional, para portabilidad)
- **CI/CD**: GitHub Actions (tests automáticos con MySQL)
- **Despliegue**: Railway (recomendado para demos rápidas)

---

## 📁 **ESTRUCTURA DEL PROYECTO**

```
edusync-laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/          # Lógica de endpoints API
│   │   │   ├── AuthController.php          # Login, registro, logout
│   │   │   ├── PasswordResetController.php # Recuperación de contraseña
│   │   │   ├── CourseController.php        # CRUD cursos
│   │   │   ├── AssignmentController.php    # CRUD tareas
│   │   │   ├── GradeController.php         # CRUD calificaciones
│   │   │   └── ...
│   │   └── Middleware/
│   │       ├── Authenticate.php  # Retorna 401 JSON (no redirect HTML)
│   │       └── RoleMiddleware.php # Filtro por rol (admin, teacher, etc.)
│   ├── Models/                   # Modelos Eloquent
│   │   ├── User.php
│   │   ├── Student.php
│   │   ├── Teacher.php
│   │   ├── Course.php
│   │   └── ...
│   └── Providers/
│       └── RouteServiceProvider.php # Rate limiters (login, sensitive)
├── database/
│   ├── migrations/               # Esquema de tablas
│   └── seeders/
│       ├── DatabaseSeeder.php    # Orquestador de seeders
│       └── AdminUserSeeder.php   # Crea admin@edusync.com por defecto
├── public/
│   ├── index.php                 # Entry point Laravel
│   └── edusync/                  # Frontend estático
│       ├── login.html            # Página de login
│       ├── DashboardAdmin.html   # Panel de administrador
│       ├── DashboardAlumno.html  # Panel de estudiante
│       ├── DashboardDocente.html # Panel de docente
│       ├── DashboardPadres.html  # Panel de tutores
│       ├── cursos.html
│       ├── tareas.html
│       ├── calificaciones.html
│       ├── perfil.html
│       ├── manage-users.html     # Gestión de usuarios (admin)
│       ├── scripts/
│       │   ├── api-integration.js # Cliente API (fetch + token)
│       │   └── header.js          # Header dinámico con navegación por rol
│       └── styles/
│           └── main.css           # Estilos unificados
├── routes/
│   ├── api.php                   # Definición de endpoints REST
│   └── web.php                   # Rutas web (mayormente redirige a /edusync)
├── tests/
│   └── Feature/
│       └── AuthAndRolesTest.php  # Tests de registro, login y roles
├── .env                          # Variables de entorno (NO versionar)
├── .env.example                  # Plantilla de variables
├── .env.testing                  # Variables para tests
├── composer.json                 # Dependencias PHP
├── docker-compose.yml            # Stack Docker (opcional)
└── README.md                     # Este archivo
```

---

## 🚀 **INSTALACIÓN PASO A PASO (PC NUEVO)**

### **Requisitos previos**
- PHP 8.2 o superior ([descargar](https://windows.php.net/download))
- Composer ([descargar](https://getcomposer.org/download/))
- MySQL 8.0+ o MariaDB 10.3+ ([descargar](https://dev.mysql.com/downloads/installer/))
- Git ([descargar](https://git-scm.com/downloads))

### **Paso 1: Clonar el repositorio**
```bash
git clone https://github.com/Mateo1099/edusync-fullstack-laravel.git
cd edusync-fullstack-laravel
```

### **Paso 2: Instalar dependencias PHP**
```bash
composer install
```

### **Paso 3: Configurar variables de entorno**
```bash
cp .env.example .env
```

Abre `.env` y verifica/ajusta estas líneas:
```properties
APP_NAME=EduSync
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=edusync_db
DB_USERNAME=root
DB_PASSWORD=root
```

### **Paso 4: Generar clave de aplicación**
```bash
php artisan key:generate
```

### **Paso 5: Crear la base de datos**
Abre MySQL Workbench y ejecuta:
```sql
CREATE DATABASE edusync_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### **Paso 6: Ejecutar migraciones**
```bash
php artisan migrate
```

### **Paso 7: Crear usuario administrador inicial**
```bash
php artisan db:seed --class=AdminUserSeeder
```

Credenciales por defecto:
- **Email**: `admin@edusync.com`
- **Password**: `1025`

### **Paso 8: Iniciar el servidor**
```bash
php artisan serve
```

### **Paso 9: Abrir el frontend**
Abre tu navegador en: **http://localhost:8000/edusync/login.html**

---

## 🌐 **ENDPOINTS DE LA API**

Base URL local: `http://localhost:8000/api`

### **Autenticación**
| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| POST | `/register` | Registra estudiante (genera email institucional) | No |
| POST | `/login` | Login (devuelve token Sanctum) | No |
| POST | `/logout` | Cierra sesión (revoca token) | Sí |
| GET | `/user` | Obtiene usuario autenticado | Sí |

### **Recuperación de contraseña**
| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| POST | `/password/forgot` | Solicita enlace de reset | No |
| POST | `/password/reset` | Cambia contraseña con token | No |

### **Estudiantes (rol: student)**
| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| GET | `/my/courses` | Mis cursos inscritos | Sí |
| GET | `/my/assignments` | Mis tareas | Sí |
| GET | `/my/grades` | Mis calificaciones | Sí |

### **Docentes (rol: teacher)**
| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| GET/POST/PUT/DELETE | `/courses` | CRUD de cursos | Sí |
| GET/POST/PUT/DELETE | `/assignments` | CRUD de tareas | Sí |
| GET/POST/PUT/DELETE | `/grades` | CRUD de calificaciones | Sí |

### **Administradores (rol: admin)**
| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| GET/POST/PUT/DELETE | `/teachers` | Gestión de docentes | Sí |
| GET/POST/PUT/DELETE | `/guardians` | Gestión de tutores | Sí |
| GET/POST/PUT/DELETE | `/courses` | Gestión de cursos | Sí |
| GET/POST/PUT/DELETE | `/enrollments` | Gestión de inscripciones | Sí |

### **Salud del sistema**
| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| GET | `/health/openssl` | Verifica OpenSSL habilitado | No |

**Nota**: Todos los endpoints protegidos requieren header:
```
Authorization: Bearer {token}
```

---

## 🔐 **AUTENTICACIÓN Y SEGURIDAD**

### **Flujo de autenticación**
1. Usuario ingresa email y contraseña en `login.html`
2. Frontend hace `POST /api/login` y recibe `{ token, user }`
3. Token se guarda en `localStorage` bajo la clave `edusync_token`
4. Cada petición incluye el header `Authorization: Bearer {token}`
5. Al hacer logout, se llama `POST /api/logout` que revoca el token

### **Roles del sistema**
- **admin**: Acceso completo (gestión de usuarios, cursos, inscripciones)
- **teacher**: Gestiona sus cursos, tareas y calificaciones
- **guardian**: Consulta información de estudiantes bajo su tutela
- **student**: Ve sus cursos, tareas y calificaciones

### **Validación de contraseñas**
Regex aplicado en registro:
```regex
^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&\/\-+]).{8,}$
```
Requiere: mínimo 8 caracteres, mayúsculas, minúsculas, números y símbolos.

### **Rate limiting**
- Login: máximo 5 intentos por minuto por IP
- Registro y password reset: máximo 10 por minuto (rate limiter `sensitive`)
- API general: 60 peticiones por minuto

---

## 🧪 **TESTS**

### **Ejecutar tests localmente**
```bash
php artisan test
```

### **Tests incluidos** (`tests/Feature/AuthAndRolesTest.php`)
- Registro exitoso de estudiante con generación de email institucional
- Login correcto y obtención de token
- Denegación de acceso de estudiante a rutas de admin
- Acceso permitido de admin a rutas protegidas

### **CI/CD en GitHub Actions**
El workflow `.github/workflows/ci.yml` ejecuta automáticamente:
1. Levanta un servicio MySQL 8
2. Instala dependencias con Composer
3. Ejecuta migraciones
4. Corre los tests Feature

---

## 🐳 **DOCKER (OPCIONAL)**

### **Iniciar con Docker Compose**
```bash
docker-compose up -d
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan db:seed --class=AdminUserSeeder
```

### **Acceso**
- Frontend: http://localhost:8080/edusync/login.html
- MySQL: localhost:3307 (mapeado desde contenedor)

---

## ☁️ **DESPLIEGUE EN RAILWAY**

### **Pasos rápidos**
1. Crea proyecto en Railway y conecta este repositorio
2. Añade servicio MySQL desde Railway
3. Configura variables de entorno:
   ```
   APP_ENV=production
   APP_DEBUG=false
   APP_KEY={genera con php artisan key:generate --show}
   APP_URL=https://tu-proyecto.up.railway.app
   DB_CONNECTION=mysql
   DB_HOST={Railway te lo provee}
   DB_PORT=3306
   DB_DATABASE=railway
   DB_USERNAME={Railway te lo provee}
   DB_PASSWORD={Railway te lo provee}
   ```
4. Build command: `composer install && php artisan migrate --force && php artisan db:seed --class=AdminUserSeeder --force`
5. Start command: `php artisan serve --host=0.0.0.0 --port=${PORT}`

---

## 📝 **SCRIPTS COMPOSER ÚTILES**

```bash
composer dev              # Inicia servidor + queue + logs + vite
composer test             # Ejecuta tests
composer build:prod       # Cachea config, rutas y vistas (producción)
composer build:clear      # Limpia caches
```

---

## 🎨 **FRONTEND: ESTRUCTURA Y FUNCIONAMIENTO**

### **Tecnologías**
- **HTML5**: Estructura semántica
- **CSS3**: Variables CSS en `main.css` (sin Sass/LESS)
- **JavaScript**: Vanilla JS (sin jQuery, React o Vue)

### **Cliente API** (`scripts/api-integration.js`)
Expone funciones globales:
```javascript
API.login(email, password)          // Retorna { token, user }
API.register(data)                  // Registra estudiante
API.logout()                        // Cierra sesión
API.getUser()                       // Usuario autenticado
API.getCourses()                    // Lista de cursos
// ... más métodos según endpoint
```

### **Header dinámico** (`scripts/header.js`)
Se ejecuta automáticamente en cada página y:
1. Lee el token de `localStorage`
2. Obtiene datos del usuario con `API.getUser()`
3. Renderiza menú de navegación según el rol
4. Añade botón de logout funcional

### **Páginas principales**
- `login.html`: Formulario de login
- `registro.html`: Formulario de registro (genera email institucional)
- `DashboardAdmin.html`: Panel con estadísticas y gestión
- `DashboardAlumno.html`: Mis cursos, tareas pendientes
- `DashboardDocente.html`: Cursos que imparte, tareas a revisar
- `DashboardPadres.html`: Info de estudiantes bajo tutela
- `cursos.html`: Lista de cursos (filtrada por rol)
- `tareas.html`: Lista de tareas (filtrada por inscripción)
- `calificaciones.html`: Historial de calificaciones
- `perfil.html`: Edición de datos personales
- `manage-users.html`: Administración de usuarios (solo admin)

---

## 📦 **DEPENDENCIAS PRINCIPALES**

### **Backend (composer.json)**
```json
{
  "require": {
    "php": "^8.2",
    "laravel/framework": "^12.0",
    "laravel/sanctum": "^4.2",
    "laravel/tinker": "^2.10.1"
  },
  "require-dev": {
    "fakerphp/faker": "^1.23",
    "phpunit/phpunit": "^11.5.3",
    "mockery/mockery": "^1.6"
  }
}
```

### **Frontend**
Sin dependencias externas (todo nativo del navegador).

---

## 🔧 **COMANDOS ARTISAN ÚTILES**

```bash
php artisan migrate              # Ejecuta migraciones pendientes
php artisan migrate:fresh        # Borra todo y recrea tablas (¡cuidado!)
php artisan db:seed              # Ejecuta todos los seeders
php artisan db:seed --class=AdminUserSeeder  # Ejecuta seeder específico
php artisan route:list           # Lista todos los endpoints
php artisan tinker               # Consola interactiva (útil para debug)
php artisan serve                # Inicia servidor de desarrollo
php artisan config:cache         # Cachea configuración (producción)
php artisan route:cache          # Cachea rutas (producción)
php artisan view:cache           # Cachea vistas (producción)
```

---

## 📚 **DOCUMENTACIÓN ADICIONAL**

- **Arquitectura del sistema**: `docs/arquitectura.md`
- **Documentación de la API**: `docs/api.md`
- **Guía de instalación extendida**: `docs/instalacion.md`
- **Deploy en Railway**: `docs/railway-deployment.md`

---

## 🛣️ **ROADMAP Y MEJORAS FUTURAS**

- [ ] Módulo de mensajería interna funcional
- [ ] Notificaciones push (eventos, tareas nuevas)
- [ ] Exportación de calificaciones a PDF/Excel
- [ ] Sistema de asistencia con código QR
- [ ] Dashboard con gráficos (Chart.js)
- [ ] Soporte multiidioma (i18n)
- [ ] App móvil (React Native o Flutter)
- [ ] Videollamadas integradas (Jitsi/Zoom)

---

## 👥 **CONTRIBUCIÓN**

1. Fork del repositorio
2. Crea una rama: `git checkout -b feature/mi-mejora`
3. Commit: `git commit -m "feat: descripción clara"`
4. Push: `git push origin feature/mi-mejora`
5. Abre un Pull Request con descripción detallada

---

## 📄 **LICENCIA**

Este proyecto usa Laravel, que es open-source bajo licencia MIT.

---

## 📞 **SOPORTE**

Si tienes dudas al instalar en otro PC:
1. Verifica que PHP, Composer y MySQL estén instalados
2. Revisa que la base de datos `edusync_db` exista
3. Ejecuta `php artisan migrate` para crear las tablas
4. Corre `php artisan db:seed` para crear el admin inicial
5. Si el servidor no inicia en 8000, prueba con `php artisan serve --port=8001`

---

**Desarrollado con ❤️ para EduSync - Sistema de Gestión Educativa**

## Estructura del proyecto
- `app/Models`: Modelos Eloquent para cada entidad principal.
- `app/Http/Controllers`: Controladores con lógica CRUD y comentarios.
- `app/Http/Middleware`: Middleware de roles.
- `database/migrations`: Migraciones con comentarios y claves foráneas.
- `routes/api.php`: Endpoints RESTful protegidos por autenticación y roles.

## Puertos por defecto (muy importante)
- Desarrollo con Laravel: `php artisan serve` expone en `http://127.0.0.1:8000` (puerto 8000).
- Frontend: accede en `http://127.0.0.1:8000/edusync/login.html`.
- Docker: Nginx publica en `http://127.0.0.1:8080` (map `8080:80`).
- Railway: usa un puerto dinámico `${PORT}` que la plataforma inyecta; la URL final la verás en el panel.

## Instalación y uso rápido en un PC nuevo
1) Requisitos: PHP 8.2+, Composer, MySQL (o Docker si prefieres contenedores).
2) Clona el repo y prepara el backend:
```bash
composer install
cp .env.example .env
php artisan key:generate
```
3) Configura `.env` con tu MySQL (ver bloque “Variables de entorno”).
4) Migra la base de datos:
```bash
php artisan migrate
```
5) Inicia el servidor:
```bash
php artisan serve
```
6) Abre el frontend: http://127.0.0.1:8000/edusync/login.html

   ## Tabla rápida
   | Componente | Descripción |
   |------------|-------------|
   | Backend | Laravel 10+ (API REST protegida por Sanctum) |
   | Frontend | HTML/CSS/JS plano (sin build frameworks) en `public/edusync` |
   | Auth | Tokens personales (Sanctum) con logout que revoca token |
   | Roles | Middleware `role:*` (admin, teacher, guardian, student) |
   | Seguridad | Contraseña fuerte (regex), JSON 401 sin redirects, sin envío de contraseña por correo |

   ## Tecnologías y librerías
   - **Laravel / Eloquent** para modelos y migraciones.
   - **Sanctum** para emisión de tokens de acceso.
   - **MySQL (recomendado)**, aunque funciona con SQLite para pruebas rápidas.
   - **Vite** listo para usar si decides migrar a componentes modernos.

   ## Estructura destacada
   ```
   app/Http/Controllers    # Lógica de endpoints y validaciones
   app/Models              # Modelos (User, Student, Teacher, Course, etc.)
   app/Http/Middleware     # Authenticate JSON + role middleware
   database/migrations     # Esquema relacional y tokens
   public/edusync          # Frontend estático unificado (dashboards y vistas)
   routes/api.php          # Definición de endpoints principales
   ```

   ## Variables de entorno mínimas (.env)
   ```
   APP_NAME=EduSync
   APP_ENV=local
   APP_KEY=base64:GENERAR_CON php artisan key:generate
   APP_DEBUG=true
   APP_URL=http://127.0.0.1:8000

   LOG_CHANNEL=stack
   LOG_LEVEL=debug

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=edusync
   DB_USERNAME=root
   DB_PASSWORD=yourpass

   SESSION_DRIVER=file
   CACHE_DRIVER=file
   QUEUE_CONNECTION=sync

   SANCTUM_STATEFUL_DOMAINS=127.0.0.1:8000
   ```

   ## Instalación backend
   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   php artisan migrate
   php artisan serve
   ```

   ## Frontend estático
   Accede vía navegador: `http://127.0.0.1:8000/edusync/login.html`.
   Las vistas consumen la API usando `scripts/api-integration.js` (Bearer token en localStorage).

   ## Endpoints principales
   Autenticación:
   ```
   POST /api/register      # Crea estudiante + matrícula + correo institucional
   POST /api/login         # Devuelve { token, user }
   POST /api/logout        # Revoca el token actual
   GET  /api/user          # Usuario autenticado
   ```
   Alumno:
   ```
   GET /api/my/courses
   GET /api/my/assignments
   GET /api/my/grades
   ```
   Admin:
   ```
   CRUD /api/teachers
   CRUD /api/guardians
   CRUD /api/courses
   CRUD /api/enrollments
   ```
   Docente:
   ```
   CRUD /api/assignments
   CRUD /api/grades (limitado)
   ```
   Salud:
   ```
   GET /api/health/openssl
   ```

   ## Flujo de login en frontend
   1. Usuario ingresa correo institucional + contraseña.
   2. Se obtiene token Sanctum y se almacena en `localStorage` bajo `edusync_token`.
   3. `header.js` construye navegación dinámica según `user.role`.
   4. Logout limpia el token y redirige a `login.html`.

   ## Contraseñas y correo institucional
   Durante registro se genera email único con slug del nombre (`nombre.apellido@edusync.com`). Nunca se envía la contraseña en claro por correo (solo aviso de creación).

   ## Tests (pendientes de agregar)
   Se propondrán pruebas en `tests/Feature` para:
   - Registro y login correcto.
   - Acceso denegado a ruta de admin con rol student.
   - Filtro de tareas sólo de cursos inscritos.

   ## Despliegue rápido
   ### Opción A: Railway (simplificada)
   1. Crear proyecto → servicio Web → conectar repo.
   2. Variables: `DB_*` (si usas MySQL remoto) o añadir addon MySQL.
   3. Configurar build command: `composer install && php artisan migrate --force`.
   4. Start command: `php artisan serve --host=0.0.0.0 --port=${PORT}`.
   5. Añadir `APP_URL` con la URL pública para cookies/stateful.

   ### Opción B: Docker Compose (portabilidad)
   Archivo ejemplo:
   ```yaml
   version: '3.9'
   services:
      app:
         image: laravelphp/php-fpm:8.2
         working_dir: /var/www/html
         volumes:
            - ./:/var/www/html
         depends_on:
            - db
      web:
         image: nginx:alpine
         ports:
            - "8080:80"
         volumes:
            - ./:/var/www/html
            - ./docker/nginx.conf:/etc/nginx/conf.d/default.conf
         depends_on:
            - app
      db:
         image: mysql:8.0
         environment:
            MYSQL_DATABASE: edusync
            MYSQL_ROOT_PASSWORD: rootpass
         ports:
            - "3307:3306"
         volumes:
            - dbdata:/var/lib/mysql
   volumes:
      dbdata:
   ```
   Luego (primera vez):
   ```bash
   docker compose up -d
   docker compose exec app composer install
   docker compose exec app php artisan key:generate
   docker compose exec app php artisan migrate --force
   ```
   Accede a: http://127.0.0.1:8080/edusync/login.html

   ### ¿Cuál elegir?
   - Railway: más rápido para mostrar avances, provisioning automático.
   - Docker Compose: reproducible en cualquier PC sin instalar PHP/MySQL localmente.
   Recomendación: usar Docker para desarrollo colaborativo y Railway para staging/demo.

   ## Seguridad y buenas prácticas vigentes
   - Middleware `Authenticate` retorna JSON 401 (evita fugas de HTML de login).
   - Regex de contraseña robusta.
   - Tokens revocados en logout.
   - Sin exposición de contraseña por email.
   - Separación de roles estricta vía `role:*`.

   ## Próximas mejoras sugeridas
   - Añadir pruebas automáticas descritas arriba.
   - Cachear resultados de métricas (cursos activos) con `cache()`.
   - Comando `php artisan edusync:seed-minimal` para crear roles y admin inicial.
   - Eliminar archivos `desktop.ini` de control de Windows del repo (ya agregados al .gitignore).
   - Añadir CI (GitHub Actions) para `composer install && php artisan test`.
   - Endpoint de recuperación de contraseña (flujo token + correo temporal).
   - Rate limit a `/api/login` (Throttle middleware).

   ## CI
   Este repositorio incluye un workflow de GitHub Actions (`.github/workflows/ci.yml`) que ejecuta las pruebas Feature en cada push a `main` usando SQLite en memoria.

   ## Cómo contribuir
   Clonar, crear rama `feature/mi-cambio`, enviar PR con descripción clara. Mantener estilo de validaciones consistente y evitar lógica pesada en controladores (mover a servicios si crece).

   ---
   Este README cubre la capa fullstack actual (API + frontend estático). El README original de Laravel se mantiene abajo para referencia del framework.
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
