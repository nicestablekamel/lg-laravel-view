FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --prefer-dist --optimize-autoloader

RUN npm install && npm run build

# Create SQLite database
RUN mkdir -p database \
    && touch database/database.sqlite

# Create all Laravel tables
RUN php artisan migrate:fresh --seed --force

RUN php artisan storage:link || true

RUN chmod -R 775 storage bootstrap/cache database

EXPOSE 10000

CMD php artisan serve --host=0.0.0.0 --port=10000