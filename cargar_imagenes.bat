@echo off
echo PAI - Cargando imagenes Docker...
echo (Solo es necesario hacerlo una vez)
echo.

echo [1/3] Cargando imagen de la aplicacion...
docker load -i pai-app.tar
echo.

echo [2/3] Cargando imagen de MySQL...
docker load -i mysql-8.0.tar
echo.

echo [3/3] Cargando imagen de Ollama...
docker load -i ollama-latest.tar
echo.

echo Imagenes cargadas correctamente.
echo.
echo Ahora ejecutar en PowerShell:
echo ==========================================
echo docker compose -f docker-compose.presentacion.yml up
echo ==========================================
pause