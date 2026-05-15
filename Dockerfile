FROM php:8.2-cli

# Dependencias del sistema + dos2unix para arreglar saltos de línea de Windows
RUN apt-get update && apt-get install -y \
    git curl zip unzip dos2unix \
    libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Node.js 20
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar proyecto completo (incluyendo vendor/ y public/build/ ya generados)
COPY . .

# Eliminar \r (CRLF → LF) del entrypoint.sh para que Linux pueda ejecutarlo
RUN dos2unix docker-entrypoint.sh && chmod +x docker-entrypoint.sh

# Instalar dependencias PHP (si vendor/ no viene con el COPY se instala aquí)
RUN composer install --no-interaction --optimize-autoloader

# Compilar assets de frontend (si public/build/ no viene en el COPY se compila aquí)
RUN npm ci && npm run build

# Permisos de Laravel
RUN mkdir -p storage/framework/views storage/framework/cache storage/framework/sessions \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Abrimos el puerto 8000 para acceder en local
EXPOSE 8000

# Archivo del entrypoint
ENTRYPOINT ["/var/www/html/docker-entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
