# Technical Decisions

This document explains the key technical decisions made during the development of the MyTheresa Products API.

## Architecture

### Domain-Driven Design (DDD)
- **Decision**: Implemented using DDD architecture
- **Why**:
    - Clear separation of concerns
    - Domain logic isolation
    - Better maintainability and testability
    - Easy to extend with new features

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
└── UI
    ├── Controller/
    ├── Validator/
    └── Formatter/
```

## Technical Choices

### Database
- **Choice**: MySQL 8.0
- **Rationale**:
    - Robust and widely supported
    - Strong indexing capabilities for product filtering
    - Built-in transaction support for test isolation
    - Adequate for the expected 20,000+ products scale

### Caching
- **Choice**: Redis
- **Why**:
    - Fast in-memory caching
    - Perfect for product data caching
    - Supports data structures needed for the use case

### Testing
- **Strategy**: Multiple test types
    - Unit tests for business logic
    - Integration tests for repositories
    - Feature tests for API endpoints
- **Benefits**:
    - Ensures business rules are followed
    - Verifies database interactions
    - Tests API contract compliance

### Pagination
- **Implementation**: Simple offset-based pagination with fixed limit
- **Rationale**:
    - Meets requirement of "at most 5 elements"
    - Provides way to access all products
    - Simple to implement and understand
    - Added `has_more` flag for client guidance

### Validation
- **Approach**: Custom request validator
- **Why**:
    - Lightweight solution for simple validation needs
    - Easy to extend with new rules
    - Clear error messages

### Error Handling
- **Strategy**: Centralized error handling in controllers
- **Benefits**:
    - Consistent error responses
    - Proper logging
    - Safe error messages in production

## Performance Considerations

### Database Indexing
- Composite index on category and price for efficient filtering
- SKU uniqueness enforced at database level
- Optimized queries for pagination

### Caching Strategy
- Product caching by SKU
- Cache invalidation on updates
- Redis for high performance

## Future Improvements

1. **Caching**
    - Add cache warming
    - Implement cache tags for better invalidation

2. **API**
    - Add rate limiting
    - Implement API versioning
    - Add request ID for tracking

3. **Monitoring**
    - Add health checks
    - Implement metrics collection
    - Enhanced logging

4. **Performance**
    - Add query result caching
