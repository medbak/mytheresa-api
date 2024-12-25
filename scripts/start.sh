#!/bin/bash
set -e

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function definitions
print_status() {
    echo -e "${BLUE}➡️ $1${NC}"
}

print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️ $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
    exit 1
}

check_docker() {
    if ! command -v docker >/dev/null 2>&1; then
        print_error "Docker is not installed. Please install Docker first."
    fi

    if ! docker info > /dev/null 2>&1; then
        print_error "Docker is not running. Please start Docker and try again."
    fi
}

check_dependencies() {
    if ! command -v docker-compose >/dev/null 2>&1; then
        print_error "Docker Compose is not installed. Please install Docker Compose first."
    fi
}

wait_for_mysql() {
    print_status "Waiting for MySQL to be ready..."
    local retries=30
    while [ $retries -gt 0 ]; do
        if docker-compose exec mysql mysqladmin ping -h"localhost" -u"app" -p"app" --silent >/dev/null 2>&1; then
            print_success "MySQL is ready"
            return 0
        fi
        echo -n "."
        sleep 1
        retries=$((retries-1))
    done
    print_error "MySQL failed to become ready in time"
}

setup_environment() {
    # Create .env if it doesn't exist
    if [ ! -f .env ]; then
        print_status "Creating .env file..."
        cp .env.dev .env || print_error "Failed to create .env file"
        print_success "Created .env file"
    fi

    # Ensure required directories exist
    mkdir -p docker/mysql/data var/log
}

install_dependencies() {
    print_status "Installing Composer dependencies..."
    docker-compose exec php composer install || print_error "Failed to install dependencies"
    print_success "Dependencies installed"
}

run_migrations() {
    print_status "Running database migrations..."
    docker-compose exec php bin/console doctrine:migrations:migrate --no-interaction || print_error "Failed to run migrations"
    print_success "Migrations completed"
}

load_fixtures() {
    if grep -q "APP_ENV=dev" .env; then
        print_status "Loading fixtures..."
        docker-compose exec php bin/console doctrine:fixtures:load --no-interaction || print_error "Failed to load fixtures"
        print_success "Fixtures loaded"
    fi
}

clear_cache() {
    print_status "Clearing cache..."
    docker-compose exec php bin/console cache:clear || print_error "Failed to clear cache"
    print_success "Cache cleared"
}

# Main execution
main() {
    echo "🚀 Starting MyTheresa Product API Setup and Launch"
    echo "================================================"

    # Initial checks
    check_docker
    check_dependencies

    # Setup phase
    setup_environment

    # Build and start containers
    print_status "Building and starting containers..."
    docker-compose up -d --build || print_error "Failed to start containers"
    print_success "Containers started"

    # Wait for services
    wait_for_mysql

    # Setup application
    install_dependencies
    run_migrations
    load_fixtures
    clear_cache

    # Print final success message
    echo -e "\n${GREEN}🎉 Setup and launch completed successfully!${NC}"
    echo -e "${BLUE}You can now use the following make commands:${NC}"
    echo -e "  make logs     - View application logs"
    echo -e "  make shell    - Access PHP container shell"
    echo -e "  make test     - Run tests"
    echo -e "  make stop     - Stop the application"
}

# Execute main function
main "$@"