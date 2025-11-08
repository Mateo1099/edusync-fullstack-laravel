# Guía de Despliegue en Railway

## Preparación

1. Asegurarse de tener:
   - Cuenta en Railway.app
   - Repositorio en GitHub
   - Código actualizado

## Pasos para el Despliegue

1. **Conectar con Railway**:
   - Ir a railway.app
   - Iniciar sesión con GitHub
   - Crear nuevo proyecto

2. **Configurar Proyecto**:
   - Seleccionar repositorio
   - Elegir rama principal
   - Configurar variables de entorno

3. **Variables de Entorno Requeridas**:
```env
APP_NAME=EduSync
APP_ENV=production
APP_KEY=base64:tu-key
APP_DEBUG=false
APP_URL=https://tu-app.railway.app

DB_CONNECTION=mysql
DB_HOST=containers-us-west-XXX.railway.app
DB_PORT=7433
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=tu-password

SANCTUM_STATEFUL_DOMAINS=*.railway.app
SESSION_DOMAIN=.railway.app
CORS_ALLOWED_ORIGINS=https://tu-frontend.com
```

4. **Configurar Base de Datos**:
   - Provisionar MySQL en Railway
   - Obtener credenciales
   - Actualizar variables de entorno

5. **Despliegue**:
   - Railway detectará automáticamente Laravel
   - Ejecutará los comandos necesarios
   - Migrará la base de datos

6. **Verificación**:
   - Comprobar logs de despliegue
   - Verificar API endpoints
   - Probar autenticación
   - Validar conexiones frontend

## Mantenimiento

1. **Monitoreo**:
   - Usar Railway Dashboard
   - Revisar logs
   - Monitorear uso de recursos

2. **Actualizaciones**:
   - Usar integración continua
   - Automatizar despliegues
   - Mantener dependencias actualizadas

3. **Backup**:
   - Configurar respaldos automáticos
   - Verificar integridad de datos
   - Mantener copias de seguridad

## Troubleshooting

1. **Problemas Comunes**:
   - Errores de conexión DB
   - Problemas de CORS
   - Errores de sesión

2. **Soluciones**:
   - Verificar variables de entorno
   - Revisar logs de Railway
   - Comprobar configuración CORS
   - Validar dominios permitidos
