FROM webdevops/php-nginx:8.2

# Install dependencies yang dibutuhkan yt-dlp
RUN apt-get update && apt-get install -y \
    python3 \
    ffmpeg \
    && apt-get clean

# Konfigurasi web root Nginx untuk Laravel
ENV WEB_DOCUMENT_ROOT=/app/public
ENV WEB_DOCUMENT_INDEX=index.php

WORKDIR /app

# Copy semua file project
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install Node.js dan compile assets Tailwind/Alpine
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install \
    && npm run build

# Download yt-dlp menggunakan artisan command yang sudah ada
RUN php artisan yt-dlp:update

# Fix permission untuk folder storage dan bootstrap cache Laravel
RUN chown -R application:application /app/storage /app/bootstrap/cache /app/database
RUN chmod -R 775 /app/storage /app/bootstrap/cache /app/database

# Expose port 80 (standard web port yang akan ditangkap oleh Render)
EXPOSE 80
