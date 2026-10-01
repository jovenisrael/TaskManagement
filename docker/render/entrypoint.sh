#!/bin/sh
set -e

cd /var/www/html

if [ -z "$PORT" ]; then
    PORT=10000
fi

sed "s/__PORT__/${PORT}/" /etc/nginx/templates/default.conf > /etc/nginx/conf.d/default.conf
rm -f /etc/nginx/sites-enabled/default

missing=""
for name in APP_KEY APP_URL DB_HOST DB_PORT DB_DATABASE DB_USERNAME DB_PASSWORD; do
    eval "value=\${$name}"
    if [ -z "$value" ]; then
        missing="$missing $name"
    fi
done

if [ -n "$missing" ]; then
    echo "Missing Render environment variables:$missing"
    exit 1
fi

echo "Waiting for MySQL..."
tries=0
until php -r "try { new PDO('mysql:host='.getenv('DB_HOST').';port='.getenv('DB_PORT').';dbname='.getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); exit(0); } catch (Throwable \$e) { exit(1); }"; do
    tries=$((tries + 1))
    if [ "$tries" -ge 30 ]; then
        echo "MySQL did not become ready. Check DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, and DB_PASSWORD."
        exit 1
    fi
    sleep 2
done

php artisan migrate --force --no-interaction --seed

php-fpm -D
nginx -g "daemon off;"
