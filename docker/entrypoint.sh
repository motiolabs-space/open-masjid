#!/bin/bash
set -e

DB_HOST="${DB_HOST:-db}"
DB_NAME="${DB_NAME:-open_masjid}"
DB_USER="${DB_USER:-open_masjid}"
DB_PASSWORD="${DB_PASSWORD:-open_masjid_secret}"
APP_PORT="${APP_PORT:-8080}"

echo "[open-masjid] Waiting for database at ${DB_HOST}..."

php -r "
\$host = getenv('DB_HOST') ?: 'db';
\$user = getenv('DB_USER') ?: 'open_masjid';
\$pass = getenv('DB_PASSWORD') ?: 'open_masjid_secret';
\$db   = getenv('DB_NAME') ?: 'open_masjid';
for (\$i = 0; \$i < 60; \$i++) {
    try {
        \$mysqli = @new mysqli(\$host, \$user, \$pass, \$db);
        if (!\$mysqli->connect_errno) {
            \$mysqli->close();
            exit(0);
        }
    } catch (Throwable \$e) {
    }
    sleep(2);
}
fwrite(STDERR, 'Database not ready after 120s' . PHP_EOL);
exit(1);
"

if [ ! -f /var/www/html/app-core/.env ]; then
    echo "[open-masjid] Creating app-core/.env from docker template..."
    cp /var/www/html/docker/.env.docker /var/www/html/app-core/.env
fi

if [ ! -d /var/www/html/app-core/vendor ]; then
    echo "[open-masjid] Installing Composer dependencies..."
    composer install --working-dir=/var/www/html/app-core --no-interaction --prefer-dist
fi

echo "[open-masjid] Ensuring writable directories and permissions..."
mkdir -p /var/www/html/app-core/writable/cache
mkdir -p /var/www/html/app-core/writable/logs
mkdir -p /var/www/html/app-core/writable/session
mkdir -p /var/www/html/app-core/writable/uploads
chown -R www-data:www-data /var/www/html/app-core/writable 2>/dev/null || true
chmod -R 775 /var/www/html/app-core/writable 2>/dev/null || true

echo "[open-masjid] Running database migrations..."
cd /var/www/html/app-core && php spark migrate --all -n 2>&1 | tee /var/www/html/app-core/writable/logs/migration_debug.log

echo "[open-masjid] Application ready at http://localhost:${APP_PORT}/"
echo "[open-masjid] Login: admin@openmasjid.com / password123"

exec "$@"

exec "$@"
