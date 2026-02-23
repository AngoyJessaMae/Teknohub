# TeknoHub System Architecture

## Overview

TeknoHub is a modern, responsive web-based Service Request and Repair Management System built using Laravel 10+ framework. The system provides comprehensive functionality for managing technology repair services with role-based access control for Administrators, Employees, and Customers.

## System Architecture

### Technology Stack

- **Backend Framework**: Laravel 10+ (PHP 8.1+)
- **Database**: MySQL 8.0+
- **Frontend**: Blade templating engine with Bootstrap 5
- **Authentication**: Laravel Breeze (built-in authentication)
- **CSS Framework**: Bootstrap 5 with custom dark theme
- **JavaScript**: Vanilla JavaScript with Bootstrap components
- **Development Environment**: XAMPP (Apache, MySQL, PHP)

### Database Architecture

The system uses a relational database with 8 core entities and proper foreign key constraints to ensure data integrity:

#### Entity Relationships

```
Users (1) ─────┬─── (1) Customers
              │
              ├─── (1) Employees
              │
              └─── (1) Admin

Service_Requests (Many) ─── (1) Customers
Service_Requests (Many) ─── (1) Employees (nullable)
Service_Requests (1) ────── (1) Queues
Service_Requests (1) ────── (1) Billings
Service_Requests (Many) ─── (1) Purchases

Items (1) ───────────────── (Many) Purchases
```

#### Database Schema

**Users Table**
- Primary Key: user_id (auto-increment)
- Fields: full_name, email, contact_number, role (enum: admin, employee, customer), password, email_verified_at, timestamps
- Indexes: email (unique), role

**Customers Table**
- Primary Key: customer_id (auto-increment)
- Foreign Key: user_id → users.user_id
- Fields: timestamps

**Employees Table**
- Primary Key: employee_id (auto-increment)
- Foreign Key: user_id → users.user_id
- Fields: department_name, job_title, timestamps

**Service_Requests Table**
- Primary Key: service_id (auto-increment)
- Foreign Keys: customer_id → customers.customer_id, employee_id → employees.employee_id (nullable)
- Fields: device_type, device_description, date_created, date_completed (nullable), status (enum: pending, in_progress, completed, cancelled), timestamps
- Indexes: customer_id, employee_id, status, date_created

**Queues Table**
- Primary Key: queue_id (auto-increment)
- Foreign Key: service_id → service_requests.service_id (unique)
- Fields: queue_position, status (enum: waiting, in_progress, completed), timestamps
- Indexes: service_id, queue_position, status

**Items Table**
- Primary Key: item_id (auto-increment)
- Fields: item_name, category (enum: Hardware, Software, Accessories, Components, Tools, Other), price, stock_quantity, timestamps
- Indexes: category

**Purchases Table**
- Primary Key: purchase_id (auto-increment)
- Foreign Keys: item_id → items.item_id, service_id → service_requests.service_id
- Fields: quantity, total_price, timestamps
- Indexes: item_id, service_id

**Billings Table**
- Primary Key: billing_id (auto-increment)
- Foreign Key: service_id → service_requests.service_id (unique)
- Fields: labor_fee, parts_fee, total_amount, payment_status (enum: pending, paid, unpaid), timestamps
- Indexes: service_id, payment_status

### Application Architecture

#### Model-View-Controller (MVC) Pattern

The system follows Laravel's MVC architecture pattern:

**Models** (app/Models/)
- User: Base authentication model with role-based relationships
- Customer: Customer-specific data and relationships
- Employee: Employee-specific data and relationships
- ServiceRequest: Service request management with automatic billing computation
- Queue: Queue management with automatic position updates
- Item: Inventory management with stock tracking
- Purchase: Parts purchase tracking with automatic billing updates
- Billing: Billing management with automatic total calculation

**Controllers** (app/Http/Controllers/)
- AuthController: Authentication and registration logic
- DashboardController: Role-based dashboard statistics
- ServiceRequestController: Service request CRUD operations with automatic queue and billing management
- InventoryController: Inventory management with stock tracking
- BillingController: Billing management and payment status updates
- QueueController: Queue management with process-next functionality

**Views** (resources/views/)
- layouts/app.blade.php: Main application layout with dark theme and responsive sidebar
- auth/: Authentication views (login, register)
- dashboard/: Role-specific dashboards with statistics cards
- service_requests/: Service request management views
- inventory/: Inventory management views
- billing/: Billing management views
- queue/: Queue management views

#### Business Logic Implementation

**Automatic Queue Management**
- When a service request is created, a queue entry is automatically created with the next available position
- When a service request is marked as completed, the queue position is automatically updated for remaining items
- Employees can process the next request in queue, which updates the queue status and service request status

**Automatic Billing Computation**
- Billing records are automatically created when service requests are created with a default labor fee
- When parts are added to a service request, the billing total is automatically updated
- Total amount is calculated as: labor_fee + parts_fee (sum of all purchases for the service)

**Role-Based Access Control**
- Admin: Full system access including user management, inventory management, and all reports
- Employee: Access to service requests, queue management, and customer information
- Customer: Access to their own service requests and billing information

### Security Implementation

#### Authentication & Authorization
- Laravel's built-in authentication system with email verification
- Role-based middleware for route protection
- CSRF protection on all forms
- Password hashing using bcrypt

#### Data Validation
- Form request validation for all user inputs
- Database constraints and foreign key relationships
- Proper data type casting in models

#### SQL Injection Prevention
- Laravel's query builder and Eloquent ORM prevent SQL injection
- Parameterized queries for all database operations

#### XSS Prevention
- Blade templating automatically escapes output
- Proper input sanitization and validation

### Performance Optimization

#### Database Optimization
- Proper indexing on frequently queried columns
- Foreign key constraints for data integrity
- Efficient query relationships with eager loading

#### Caching Strategy
- Laravel's built-in caching for configuration and routes
- Database query result caching for frequently accessed data

#### Asset Optimization
- Bootstrap 5 CDN for faster loading
- Font Awesome icons for consistent UI
- Responsive design for mobile optimization

### Scalability Considerations

#### Horizontal Scaling
- Stateless application design allows for multiple server deployment
- Database can be separated from application server
- Session management supports database or Redis storage

#### Database Scaling
- Proper indexing for large datasets
- Database partitioning for historical data
- Read replicas for reporting queries

#### Code Maintainability
- Follows Laravel best practices and conventions
- Comprehensive model relationships and business logic
- Clear separation of concerns

### Deployment Architecture

#### Development Environment
- XAMPP stack for local development
- MySQL database with proper configuration
- Laravel development server for testing

#### Production Environment
- Linux-based web server (Apache/Nginx)
- MySQL database server
- PHP 8.1+ with required extensions
- SSL certificate for secure connections
- Environment-based configuration

### API Design (Future Enhancement)

The system is designed to support RESTful API endpoints for potential mobile application integration:

**Authentication Endpoints**
- POST /api/login
- POST /api/register
- POST /api/logout

**Service Request Endpoints**
- GET /api/service-requests
- POST /api/service-requests
- GET /api/service-requests/{id}
- PUT /api/service-requests/{id}
- DELETE /api/service-requests/{id}

**Inventory Endpoints**
- GET /api/inventory
- POST /api/inventory
- PUT /api/inventory/{id}
- DELETE /api/inventory/{id}

**Queue Endpoints**
- GET /api/queue
- POST /api/queue/process-next

**Billing Endpoints**
- GET /api/billing
- GET /api/billing/{id}
- PUT /api/billing/{id}/payment-status

### Monitoring and Logging

#### Application Logging
- Laravel's built-in logging system
- Error tracking and reporting
- Performance monitoring for database queries

#### System Monitoring
- Database performance monitoring
- Application uptime monitoring
- User activity logging for security

### Backup and Recovery

#### Database Backup
- Regular automated database backups
- Point-in-time recovery capability
- Backup verification and testing

#### Application Backup
- Source code version control (Git)
- Configuration backup and management
- Asset backup and CDN management

## Conclusion

The TeknoHub system architecture provides a robust, scalable, and secure foundation for managing technology repair services. The modular design allows for easy maintenance and future enhancements while maintaining high performance and security standards. The system is ready for production deployment with proper server configuration and monitoring setup.

## Next Steps

1. **Performance Testing**: Load testing with simulated user traffic
2. **Security Audit**: Comprehensive security review and penetration testing
3. **User Acceptance Testing**: Real-world testing with actual users
4. **Documentation**: User manual and administrator guide creation
5. **Training**: Staff training on system usage and best practices
6. **Maintenance Plan**: Regular update and maintenance schedule