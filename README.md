# EduSync Laravel

Sistema de gestión educativa migrado a Laravel, con arquitectura profesional y escalable.

## Tecnologías principales
- **Laravel**: Framework PHP moderno y robusto.
- **Eloquent ORM**: Mapeo objeto-relacional para modelos y relaciones.
- **Sanctum**: Autenticación API segura.
- **Middleware personalizado**: Control de acceso por roles.

## Estructura del proyecto
- `app/Models`: Modelos Eloquent para cada entidad principal.
- `app/Http/Controllers`: Controladores con lógica CRUD y comentarios.
- `app/Http/Middleware`: Middleware de roles.
- `database/migrations`: Migraciones con comentarios y claves foráneas.
- `routes/api.php`: Endpoints RESTful protegidos por autenticación y roles.

## Guía de uso
1. Instala dependencias:
   ```bash
   # EduSync Fullstack Laravel

- Database agnostic [schema migrations](https://laravel.com/docs/migrations).

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
   Luego:
   ```bash
   docker compose up -d
   docker compose exec app composer install
   docker compose exec app php artisan key:generate
   docker compose exec app php artisan migrate --force
   ```

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
   - Eliminar archivos `desktop.ini` de control de Windows del repo.
   - Añadir CI (GitHub Actions) para `composer install && php artisan test`.
   - Endpoint de recuperación de contraseña (flujo token + correo temporal).
   - Rate limit a `/api/login` (Throttle middleware).

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
