#!/bin/sh
set -e

echo "=== Starting Nho Net's Laravel Backend ==="

# Ensure .env exists
if [ ! -f .env ]; then
    echo "Creating .env from .env.example..."
    cp .env.example .env
fi

# Ensure app key exists
php artisan key:generate --force || true

# Ensure uploads directory exists and is writable
mkdir -p public/uploads
chmod -R 777 public/uploads storage bootstrap/cache

# Wait for database using Laravel's own database configuration
echo "Waiting for MariaDB connection..."
max_retries=30
count=0
until php artisan tinker --execute="DB::connection()->getPdo();" > /dev/null 2>&1 || [ $count -ge $max_retries ]; do
    echo "MariaDB not ready yet. Retrying in 2 seconds... ($count/$max_retries)"
    sleep 2
    count=$((count+1))
done

if [ $count -ge $max_retries ]; then
    echo "Warning: Database check reached max retries. Continuing..."
else
    echo "MariaDB connection established successfully!"
fi

# Run database migrations
echo "Running migrations..."
php artisan migrate --force || true

echo "Starting Laravel server on 0.0.0.0:8000..."
exec php artisan serve --host=0.0.0.0 --port=8000
