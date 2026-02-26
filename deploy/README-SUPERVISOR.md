# Supervisor: cola de jobs (driver database)

Este proyecto usa colas con el driver **database**. Para que los jobs se procesen en el servidor, un worker debe estar corriendo. Supervisor mantiene ese worker activo y lo reinicia si falla.

## Requisitos

- Supervisor instalado en el servidor (`apt install supervisor` en Debian/Ubuntu).
- `.env` con `QUEUE_CONNECTION=database`.
- Migraciones ejecutadas (tablas `jobs` y `failed_jobs`).

## Instalación en el servidor

1. **Ajustar rutas y usuario** (si hace falta)  
   Edita `deploy/supervisor/laravel-worker.conf`:
   - `command`: sustituye `/home/objetivosbas/htdocs/sales-target` por la ruta real del proyecto en el servidor.
   - `stdout_logfile`: misma ruta del proyecto para el log del worker.
   - `user`: usuario con el que corre PHP (por ejemplo `www-data`; en CloudPanel puede ser otro).

2. **Copiar la configuración a Supervisor**
   ```bash
   sudo cp /home/objetivosbas/htdocs/sales-target/deploy/supervisor/laravel-worker.conf /etc/supervisor/conf.d/laravel-worker.conf
   ```
   (Usa la ruta real de tu proyecto si es distinta.)

3. **Recargar y arrancar el worker**
   ```bash
   sudo supervisorctl reread
   sudo supervisorctl update
   sudo supervisorctl start laravel-worker:*
   ```

4. **Comprobar**
   ```bash
   sudo supervisorctl status
   ```
   Debe aparecer `laravel-worker:laravel-worker_00   RUNNING`.

## Comandos útiles

| Acción              | Comando |
|---------------------|--------|
| Ver estado          | `sudo supervisorctl status` |
| Parar el worker      | `sudo supervisorctl stop laravel-worker:*` |
| Reiniciar el worker | `sudo supervisorctl restart laravel-worker:*` |
| Ver log del worker  | `tail -f storage/logs/worker.log` |

## Después de desplegar código

Tras un `git pull` o despliegue, reinicia el worker para que use el código nuevo:

```bash
sudo supervisorctl restart laravel-worker:*
```

O, si prefieres que los workers terminen el job actual y luego se reinicien:

```bash
php /ruta/al/proyecto/artisan queue:restart
```
