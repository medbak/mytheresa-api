#!/bin/sh
set -e

# Wait for MySQL to be ready
while ! mysqladmin ping -h"mysql" -u"app" -p"app" --silent; do
    echo "Waiting for MySQL to be ready..."
    sleep 1
done

# Run migrations
php bin/console doctrine:migrations:migrate --no-interaction

# First argument is the command to run
exec "$@"

# docker/mysql/init.sql
CREATE DATABASE IF NOT EXISTS mytheresa;
GRANT ALL PRIVILEGES ON mytheresa.* TO 'app'@'%';
FLUSH PRIVILEGES;