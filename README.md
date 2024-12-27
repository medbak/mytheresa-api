# MyTheresa Product API

A Symfony-based REST API for product management with dynamic discount rules implementation following DDD (Domain-Driven Design) principles.

## Features

- Product catalog management
- Dynamic discount rules
- Category-based discounts
- SKU-specific discounts
- RESTful API endpoints
- Pagination support
- Performance optimized for large datasets

## Prerequisites

- Docker
- Docker Compose
- Make

## Installation

1. Clone the repository:
```
 git clone <repository-url>
 cd mytheresa-api
```

2. Start the environment:
```bash
 make start
```

This command will:
- Set up Docker containers
- Install dependencies
- Run database migrations
- Load fixtures (in dev environment)

## Available Services

- API: http://localhost:8085
- phpMyAdmin: http://localhost:4698
    - Server: mysql
    - Username: app
    - Password: app

## Development

### Directory Structure
```
src/
├── Application/        
│   ├── DTO/
│   └── Service/
├── Domain/             
│   ├── Entity/
│   ├── Repository/
│   └── Service/
├── Infrastructure/     
│   ├── Cache/
│   └── Persistence/
└── UI/                
    └── Controller/
```

### Available Commands

```bash
# Environment
make start     # Start the environment
make stop      # Stop containers
make restart   # Restart containers
make logs      # View logs
make shell     # Access PHP container

# Testing & Quality
make test      # Run PHPUnit tests
make cs        # Check code style
make stan      # Run static analysis
make quality   # Run all checks (cs + stan)
make fix       # Fix code style issues
```

### Running Tests

```bash
 make test
```

### Code Quality

Run all quality checks:
```bash
 make quality
```

Individual checks:
```bash
 make cs    # Code style check
```

```bash
 make stan  # Static analysis
```

```bash
 make fix   # Fix code style issues
```

## API Documentation

### GET /products

Retrieve products with optional filters.

Query Parameters:
- `category` - Filter by product category
- `priceLessThan` - Filter by price (before discounts)

Example Response:
```json
{
    "products": [
        {
            "sku": "000001",
            "name": "BV Lean leather ankle boots",
            "category": "boots",
            "price": {
                "original": 89000,
                "final": 62300,
                "discount_percentage": "30%",
                "currency": "EUR"
            }
        }
    ]
}
```

## Docker Services

The environment includes:
- PHP 8.3 (FPM)
- Nginx
- MySQL 8.0
- Redis
- phpMyAdmin

