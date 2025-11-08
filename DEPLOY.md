# 🚀 Guía de Despliegue - EduSync

## Opciones de Despliegue

### 1️⃣ Railway (Recomendado para demos rápidas)
### 2️⃣ Docker (Portabilidad y desarrollo)
### 3️⃣ VPS/Cloud (Control completo)

---

## 🚂 DESPLIEGUE EN RAILWAY

### **Paso 1: Preparar el repositorio**
✅ Ya está listo. Tu repo en GitHub tiene todo lo necesario.

### **Paso 2: Crear proyecto en Railway**
1. Ve a [Railway.app](https://railway.app)
2. Conecta tu cuenta de GitHub
3. Click en "New Project" → "Deploy from GitHub repo"
4. Selecciona `Mateo1099/edusync-fullstack-laravel`

### **Paso 3: Agregar base de datos MySQL**
1. En tu proyecto Railway, click "New" → "Database" → "Add MySQL"
2. Railway creará automáticamente las variables:
   - `MYSQLHOST`
   - `MYSQLPORT`
   - `MYSQLDATABASE`
   - `MYSQLUSER`
   - `MYSQLPASSWORD`

### **Paso 4: Configurar variables de entorno**
En Railway, ve a tu servicio web → "Variables" y agrega:

```bash
# App básica
APP_NAME=EduSync
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-proyecto.up.railway.app  # Railway te da esta URL

# Genera una nueva key con: php artisan key:generate --show
APP_KEY=base64:TU_KEY_GENERADA_AQUI

# Conectar a la base MySQL de Railway (usa las variables reference)
DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQLHOST}}
DB_PORT=${{MySQL.MYSQLPORT}}
DB_DATABASE=${{MySQL.MYSQLDATABASE}}
DB_USERNAME=${{MySQL.MYSQLUSER}}
DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}

# Password del admin (IMPORTANTE: cambia esto)
ADMIN_PASSWORD=TuPasswordSuperSegura2025!

# Session y cache
SESSION_DRIVER=database
CACHE_DRIVER=database

# Mail (opcional, usa 'log' para desarrollo)
MAIL_MAILER=log

# Sanctum
SANCTUM_STATEFUL_DOMAINS=${{RAILWAY_PUBLIC_DOMAIN}}
```

### **Paso 5: Configurar comandos de build**
En Railway, ve a "Settings":

**Build Command:**
```bash
composer install --no-dev --optimize-autoloader && php artisan config:cache && php artisan route:cache && php artisan view:cache
```

**Start Command:**
```bash
php artisan migrate --force && php artisan db:seed --class=AdminUserSeeder --force && php artisan serve --host=0.0.0.0 --port=${PORT}
```

### **Paso 6: Deploy**
Railway detectará automáticamente que es un proyecto PHP y usará el `Procfile`.
- El deploy toma ~3-5 minutos
- Railway te dará una URL pública: `https://edusync-production-XXXX.up.railway.app`

### **Paso 7: Verificar**
1. Abre `https://tu-url.up.railway.app/edusync/login.html`
2. Login con: `admin@edusync.com` / (la password que pusiste en `ADMIN_PASSWORD`)
3. ✅ Listo!

---

## 🐳 DESPLIEGUE CON DOCKER

### **Paso 1: Build de las imágenes**
```bash
docker-compose build
```

### **Paso 2: Levantar los servicios**
```bash
docker-compose up -d
```

### **Paso 3: Instalar dependencias dentro del contenedor**
```bash
docker-compose exec app composer install --no-dev --optimize-autoloader
```

### **Paso 4: Generar key de aplicación**
```bash
docker-compose exec app php artisan key:generate
```

### **Paso 5: Ejecutar migraciones**
```bash
docker-compose exec app php artisan migrate --force
```

### **Paso 6: Crear usuario admin**
```bash
docker-compose exec app php artisan db:seed --class=AdminUserSeeder --force
```

### **Paso 7: Cachear para producción**
```bash
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

### **Paso 8: Acceder**
Abre: `http://localhost:8080/edusync/login.html`

---

## 🖥️ DESPLIEGUE EN VPS (DigitalOcean, AWS, Linode, etc.)

### **Requisitos del servidor**
- Ubuntu 22.04 LTS (recomendado)
- PHP 8.2+
- MySQL 8.0+
- Nginx o Apache
- Composer
- Git

### **Paso 1: Instalar dependencias**
```bash
sudo apt update
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-bcmath php8.2-curl php8.2-zip unzip nginx mysql-server git
```

### **Paso 2: Instalar Composer**
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### **Paso 3: Clonar el proyecto**
```bash
cd /var/www
sudo git clone https://github.com/Mateo1099/edusync-fullstack-laravel.git edusync
sudo chown -R www-data:www-data /var/www/edusync
cd edusync
```

### **Paso 4: Instalar dependencias**
```bash
composer install --no-dev --optimize-autoloader
```

### **Paso 5: Configurar .env**
```bash
cp .env.example .env
nano .env  # Edita con tus credenciales MySQL
```

### **Paso 6: Generar key y migrar**
```bash
php artisan key:generate
php artisan migrate --force
php artisan db:seed --class=AdminUserSeeder --force
```

### **Paso 7: Permisos**
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### **Paso 8: Configurar Nginx**
```nginx
server {
    listen 80;
    server_name tu-dominio.com;
    root /var/www/edusync/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### **Paso 9: SSL con Let's Encrypt (opcional pero recomendado)**
```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d tu-dominio.com
```

### **Paso 10: Reiniciar servicios**
```bash
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
```

---

## 🔒 CHECKLIST DE SEGURIDAD PRODUCCIÓN

- [ ] `APP_DEBUG=false` en `.env`
- [ ] `APP_ENV=production` en `.env`
- [ ] Generar nuevo `APP_KEY` único
- [ ] Cambiar `ADMIN_PASSWORD` a algo seguro
- [ ] Usar SSL/HTTPS (certificado válido)
- [ ] Configurar firewall (solo puertos 80, 443, 22)
- [ ] Deshabilitar listado de directorios en Nginx/Apache
- [ ] Revisar permisos de archivos (storage, bootstrap/cache)
- [ ] Configurar backups automáticos de la base de datos
- [ ] Rate limiting habilitado (ya está en el código)
- [ ] Logs monitoreados (`storage/logs/laravel.log`)

---

## 🧪 VERIFICAR DEPLOYMENT

### **1. Health check**
```bash
curl https://tu-dominio.com/api/health/openssl
```
Debe retornar JSON con estado de OpenSSL.

### **2. Login**
Abre `https://tu-dominio.com/edusync/login.html` y loguea con admin@edusync.com

### **3. Endpoint protegido**
```bash
curl -X POST https://tu-dominio.com/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@edusync.com","password":"TuPassword"}'
```

### **4. Dashboard**
Navega por los dashboards según tu rol.

---

## 📊 MONITOREO Y MANTENIMIENTO

### **Logs en Railway**
Railway muestra logs en tiempo real en el dashboard.

### **Logs en Docker**
```bash
docker-compose logs -f app
```

### **Logs en VPS**
```bash
tail -f /var/www/edusync/storage/logs/laravel.log
```

### **Backup de base de datos**
```bash
# Exportar
mysqldump -u root -p edusync_db > backup_$(date +%F).sql

# Importar
mysql -u root -p edusync_db < backup_2025-11-08.sql
```

---

## 🆘 TROUBLESHOOTING

### **Error: "No application encryption key"**
```bash
php artisan key:generate
```

### **Error: "SQLSTATE[HY000] [2002] Connection refused"**
- Verifica que MySQL esté corriendo
- Revisa credenciales en `.env`

### **Error: "The stream or file could not be opened"**
```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

### **Frontend no carga estilos**
- Verifica que `APP_URL` en `.env` coincida con tu dominio
- Limpia caché: `php artisan cache:clear`

### **Token Mismatch / CORS**
- Asegúrate que `SANCTUM_STATEFUL_DOMAINS` incluya tu dominio
- Verifica que `APP_URL` sea correcto

---

## 📞 SOPORTE

Si algo falla durante el deploy:
1. Revisa los logs del servidor
2. Verifica las variables de entorno
3. Confirma que MySQL esté accesible
4. Ejecuta `php artisan migrate:status` para ver estado de migraciones

---

**¡Listo para producción! 🚀**
