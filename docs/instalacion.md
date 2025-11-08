# Manual de Instalación

## Requisitos Previos
- PHP >= 8.2
- Composer
- Node.js >= 16
- Git

## Instalación del Backend (edusync-laravel)

1. Clonar el repositorio:
```bash
git clone https://github.com/tu-usuario/edusync-laravel.git
cd edusync-laravel
```

2. Instalar dependencias PHP:
```bash
composer install
```

3. Configurar el entorno:
```bash
cp .env.example .env
php artisan key:generate
```

4. Configurar la base de datos en .env:
```env
DB_CONNECTION=sqlite
# o usar MySQL/PostgreSQL según necesidad
```

5. Ejecutar migraciones:
```bash
php artisan migrate --seed
```

6. Iniciar el servidor:
```bash
php artisan serve
```

## Instalación del Frontend (EDUSYNC)

1. Navegar al directorio del frontend:
```bash
cd ../EDUSYNC
```

2. Abrir index.html en un navegador o usar un servidor local

## Configuración del Despliegue

### Railway
1. Crear cuenta en Railway
2. Conectar con el repositorio de GitHub
3. Configurar variables de entorno
4. Desplegar aplicación

### Variables de Entorno Necesarias
- `APP_KEY`
- `APP_ENV`
- `DB_CONNECTION`
- `DATABASE_URL`
- `SANCTUM_STATEFUL_DOMAINS`
- `SESSION_DOMAIN`
- `CORS_ALLOWED_ORIGINS`

## Verificación
1. Comprobar API: http://localhost:8000/api/health
2. Acceder al frontend: http://localhost/EDUSYNC
3. Probar autenticación
4. Verificar roles y permisos
