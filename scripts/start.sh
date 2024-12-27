#!/bin/bash
set -e

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m'

# Functions must be defined before they're used
print_status() {
    echo -e "${BLUE}➡️ $1${NC}"
}

print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
    exit 1
}

check_dependencies() {
   if ! command -v docker >/dev/null 2>&1; then
       print_error "Docker is not installed"
   fi
   if ! command -v docker-compose >/dev/null 2>&1; then
       print_error "Docker Compose is not installed"
   fi
   if ! docker info > /dev/null 2>&1; then
       print_error "Docker is not running"
   fi
}

setup_environment() {
   if [ ! -f .env ]; then
       print_status "Creating .env file..."
       cp .env.dev .env
       echo "UID=$(id -u)" >> .env
       print_success "Created .env file"
   fi
   mkdir -p docker/mysql/data var/log
}

wait_for_mysql() {
   print_status "Waiting for MySQL..."
   local retries=30
   while [ $retries -gt 0 ]; do
       if docker-compose exec -T mysql mysqladmin ping -h"localhost" -u"app" -p"app" --silent; then
           print_success "MySQL is ready"
           return 0
       fi
       echo -n "."
       sleep 1
       retries=$((retries-1))
   done
   print_error "MySQL timeout"
}

main() {
   echo "🚀 Setting up MyTheresa API"
   echo "=========================="

   check_dependencies
   setup_environment

   print_status "Building and starting containers..."
   docker-compose down -v
   docker-compose build
   docker-compose up -d
   print_success "Containers started"

   wait_for_mysql

   print_status "Installing dependencies..."
   docker-compose exec -T php composer install
   docker-compose exec -T php chmod 777 -R vendor
   print_success "Dependencies installed"

   print_status "Running migrations..."
   docker-compose exec -T php bin/console doctrine:migrations:migrate --no-interaction
   print_success "Migrations completed"

   if grep -q "APP_ENV=dev" .env; then
       print_status "Loading fixtures..."
       docker-compose exec -T php bin/console doctrine:fixtures:load --no-interaction
       print_success "Fixtures loaded"
   fi

   print_status "Clearing cache..."
   docker-compose exec -T php bin/console cache:clear
   print_success "Cache cleared"

   echo -e "\n${GREEN}✅ Setup complete!${NC}"
   echo -e "API available at http://localhost:8085"
   echo -e "\nAvailable commands:"
   echo "  make logs   - View logs"
   echo "  make shell  - Access PHP shell"
   echo "  make test   - Run tests"
   echo "  make stop   - Stop containers"
}

main "$@"