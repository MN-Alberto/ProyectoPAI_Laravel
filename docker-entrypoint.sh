#!/bin/bash
# El script se ejecuta con bash

set -e
# Hace que el script se detenga automaticamente si algún comando falla

echo "   PAI – Iniciando sistema..."
# Mensaje de inicio del sistema

# Esperar a MySQL
echo "[1/4] Esperando a MySQL..."
# Indica el paso en el que está

MAX_TRIES=40
# Máximo 40 intentos para conectarse

TRIES=0 
# Inentos actuales

until php -r "
    try{
        new PDO('mysql:host=mysql;port=3306;dbname=pai', 'usuario_pai', 'paso1234');
        exit(0);
    }
	catch (Exception \$e){
        	exit(1);
    }
" 2>/dev/null; do

# Ejecuta un script php para intentar conectase a mysql usando PDO
# si la conexión es satisfactoria hace exit(0) y termina el bucle
# si falla hace exit(1)
# el bucle solo se repite si el comando falla con exit(1)
# la linea 2>/dev/null: do oculta los errores php

    TRIES=$((TRIES + 1))
    # Suma 1 al contador de intentos


    if [ $TRIES -ge $MAX_TRIES ]; then
    # Comprueba si se llegó al número máximo de intentos

        echo "ERROR: MySQL no respondio a tiempo."
	# Si se llegó al núm máximo de intentos muestra un mensaje de error        

	 exit 1
	 # Termina el script con errores
    fi

    echo "Esperando MySQL... ($TRIES/$MAX_TRIES)"
    # Muestra la cantidad de intentos que se han hecho

    sleep 3
    # Espera 3 segundos antes del siguiente intento

done

echo "MySQL listo"

# Generar APP_KEY si no existe
echo "[2/4] Verificando APP_KEY..."
# Indicamos el inicio de la verificación de la clave de Laravel

APP_KEY_VALUE=$(grep "^APP_KEY=" /var/www/html/.env | cut -d '=' -f2)
# Busca la linea APP_KEY en el archivo .env y coge el valor despues del =
if [ -z "$APP_KEY_VALUE" ] || [ "$APP_KEY_VALUE" = "base64:" ]; then
# Comprueba que la variable que coge la key esta completa o vacia

    php artisan key:generate --force
    # Genera una nueva key de Laravel

    echo "APP_KEY generada"
else
    echo "APP_KEY ya configurada"
fi

# Migraciones
echo "[3/4] Ejecutando migraciones..."
# Inicio de las migraciones de db

php artisan migrate --force
# Ejecuta las migraciones de Laravel, el --force es para que no haya confirmacion

echo "Migraciones completadas"

# Optimizar
echo "[4/4] Optimizando..."
# Optimizacion de Laravel

php artisan config:cache 2>/dev/null || true
# Genera cache de config
# Si da error 2>/dev/null oculta los errores y || true hace que el script no se detenga

php artisan route:cache 2>/dev/null || true
# Genera cache de route
# Gestiona los errores y la continuacion del script igual que la generacion del cache de config

# Corregir permisos de storage generados por comandos ejecutados como root
chown -R www-data:www-data storage bootstrap/cache

echo "Estamos ready :)"

echo "Abrir http://localhost:8000 en el navegador"

exec "$@"
# Ejecuta el comando principal del contenedor
