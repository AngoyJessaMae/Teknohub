# TeknoHub Entity Relationship Diagram (ERD)

## Overview

This document describes the Entity Relationship Diagram for the TeknoHub Service Request and Repair Management System. The ERD illustrates the relationships between all entities in the system and their attributes.

## Entity Relationship Diagram (Text Format)

```
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│                                    USERS                                              │
├─────────────────────────────────────────────────────────────────────────────────────────┤
│ user_id (PK)         │ INT AUTO_INCREMENT                                           │
│ full_name            │ VARCHAR(255)                                                 │
│ email                │ VARCHAR(255) UNIQUE                                          │
│ contact_number       │ VARCHAR(20)                                                  │
│ role                 │ ENUM('admin', 'employee', 'customer')                      │
│ password             │ VARCHAR(255)                                                 │
│ email_verified_at    │ TIMESTAMP NULLABLE                                           │
│ created_at           │ TIMESTAMP                                                    │
│ updated_at           │ TIMESTAMP                                                    │
└─────────────────────────────────────────────────────────────────────────────────────────┘
                               │ 1
                               │
                               │ 1
                ┌──────────────────────────────┬──────────────────────────────┐
                │                              │                              │
                ▼ 1                            ▼ 1                            ▼ 1
┌─────────────────────────────────────┐ ┌─────────────────────────────────────┐ ┌─────────────────────────────────────┐
│            CUSTOMERS                │ │            EMPLOYEES                │ │            ADMIN                    │
├─────────────────────────────────────┤ ├─────────────────────────────────────┤ ├─────────────────────────────────────┤
│ customer_id (PK) INT AUTO_INCREMENT │ │ employee_id (PK) INT AUTO_INCREMENT │ │ (Inherits from USERS)               │
│ user_id (FK) → USERS.user_id        │ │ user_id (FK) → USERS.user_id        │ │ role = 'admin'                      │
│ created_at TIMESTAMP                  │ │ department_name VARCHAR(255)        │ │                                     │
│ updated_at TIMESTAMP                  │ │ job_title VARCHAR(255)              │ │                                     │
│                                     │ │ created_at TIMESTAMP                │ │                                     │
│                                     │ │ updated_at TIMESTAMP                │ │                                     │
└─────────────────────────────────────┘ └─────────────────────────────────────┘ └─────────────────────────────────────┘
                │                              │
                │                              │
                │ 1                            │ 1
                │                              │
                ▼ Many                         ▼ Many (Nullable)
┌────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                                    SERVICE_REQUESTS                                                      │
├────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
│ service_id (PK)         │ INT AUTO_INCREMENT                                                                      │
│ customer_id (FK)        │ → CUSTOMERS.customer_id                                                                 │
│ employee_id (FK)        │ → EMPLOYEES.employee_id (NULLABLE)                                                      │
│ device_type             │ VARCHAR(100)                                                                            │
│ device_description      │ TEXT                                                                                    │
│ date_created            │ DATE                                                                                    │
│ date_completed          │ DATE (NULLABLE)                                                                         │
│ status                  │ ENUM('pending', 'in_progress', 'completed', 'cancelled')                                │
│ created_at              │ TIMESTAMP                                                                               │
│ updated_at              │ TIMESTAMP                                                                               │
└────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
                │ 1
                │
                │ 1
                ▼
┌────────────────────────────────────────────────────────────────────────────────────────────┐
│                                    QUEUES                                                │
├────────────────────────────────────────────────────────────────────────────────────────────┤
│ queue_id (PK)         │ INT AUTO_INCREMENT                                        │
│ service_id (FK)       │ → SERVICE_REQUESTS.service_id (UNIQUE)                    │
│ queue_position        │ INT                                                       │
│ status                │ ENUM('waiting', 'in_progress', 'completed')             │
│ created_at            │ TIMESTAMP                                                 │
│ updated_at            │ TIMESTAMP                                                 │
└────────────────────────────────────────────────────────────────────────────────────────────┘
                │ 1
                │
                │ 1
                ▼
┌────────────────────────────────────────────────────────────────────────────────────────────┐
│                                   BILLINGS                                               │
├────────────────────────────────────────────────────────────────────────────────────────────┤
│ billing_id (PK)       │ INT AUTO_INCREMENT                                        │
│ service_id (FK)       │ → SERVICE_REQUESTS.service_id (UNIQUE)                    │
│ labor_fee             │ DECIMAL(10,2)                                             │
│ parts_fee             │ DECIMAL(10,2)                                             │
│ total_amount          │ DECIMAL(10,2)                                             │
│ payment_status        │ ENUM('pending', 'paid', 'unpaid')                         │
│ created_at            │ TIMESTAMP                                                 │
│ updated_at            │ TIMESTAMP                                                 │
└────────────────────────────────────────────────────────────────────────────────────────────┘
                │ 1
                │
                │ Many
                ▼
┌────────────────────────────────────────────────────────────────────────────────────────────┐
│                                   PURCHASES                                              │
├────────────────────────────────────────────────────────────────────────────────────────────┤
│ purchase_id (PK)      │ INT AUTO_INCREMENT                                        │
│ item_id (FK)          │ → ITEMS.item_id                                           │
│ service_id (FK)       │ → SERVICE_REQUESTS.service_id                             │
│ quantity              │ INT                                                       │
│ total_price           │ DECIMAL(10,2)                                             │
│ created_at            │ TIMESTAMP                                                 │
│ updated_at            │ TIMESTAMP                                                 │
└────────────────────────────────────────────────────────────────────────────────────────────┘
                │ Many
                │
                │ 1
                ▼
┌────────────────────────────────────────────────────────────────────────────────────────────┐
│                                     ITEMS                                                │
├────────────────────────────────────────────────────────────────────────────────────────────┤
│ item_id (PK)          │ INT AUTO_INCREMENT                                        │
│ item_name             │ VARCHAR(255)                                              │
│ category              │ ENUM('Hardware', 'Software', 'Accessories', 'Components', 'Tools', 'Other') │
│ price                 │ DECIMAL(10,2)                                             │
│ stock_quantity        │ INT                                                       │
│ created_at            │ TIMESTAMP                                                 │
│ updated_at            │ TIMESTAMP                                                 │
└────────────────────────────────────────────────────────────────────────────────────────────┘
```

## Relationship Details

### One-to-One Relationships

1. **Users → Customers**
   - Each user with role 'customer' has exactly one customer record
   - Customer record cannot exist without a corresponding user
   - Foreign Key: customers.user_id → users.user_id

2. **Users → Employees**
   - Each user with role 'employee' has exactly one employee record
   - Employee record cannot exist without a corresponding user
   - Foreign Key: employees.user_id → users.user_id

3. **Service_Requests → Queues**
   - Each service request has exactly one queue entry
   - Queue entry cannot exist without a corresponding service request
   - Foreign Key: queues.service_id → service_requests.service_id

4. **Service_Requests → Billings**
   - Each service request has exactly one billing record
   - Billing record cannot exist without a corresponding service request
   - Foreign Key: billings.service_id → service_requests.service_id

### One-to-Many Relationships

1. **Customers → Service_Requests**
   - Each customer can have multiple service requests
   - Service request must belong to exactly one customer
   - Foreign Key: service_requests.customer_id → customers.customer_id

2. **Employees → Service_Requests**
   - Each employee can handle multiple service requests
   - Service request can have zero or one employee assigned
   - Foreign Key: service_requests.employee_id → employees.employee_id (nullable)

3. **Service_Requests → Purchases**
   - Each service request can have multiple purchase records
   - Purchase record must belong to exactly one service request
   - Foreign Key: purchases.service_id → service_requests.service_id

4. **Items → Purchases**
   - Each item can be purchased multiple times
   - Purchase record must reference exactly one item
   - Foreign Key: purchases.item_id → items.item_id

### Many-to-Many Relationships (Implicit)

**Items ↔ Service_Requests** (through Purchases)
- Items can be used in multiple service requests
- Service requests can use multiple items
- Relationship is managed through the purchases junction table

## Cardinality Summary

| Relationship | Type | Cardinality | Description |
|--------------|------|-------------|-------------|
| Users - Customers | One-to-One | 1:1 | Each customer user has one customer profile |
| Users - Employees | One-to-One | 1:1 | Each employee user has one employee profile |
| Customers - Service_Requests | One-to-Many | 1:N | Each customer can create multiple service requests |
| Employees - Service_Requests | One-to-Many | 1:N | Each employee can handle multiple service requests |
| Service_Requests - Queues | One-to-One | 1:1 | Each service request has one queue entry |
| Service_Requests - Billings | One-to-One | 1:1 | Each service request has one billing record |
| Service_Requests - Purchases | One-to-Many | 1:N | Each service request can have multiple purchases |
| Items - Purchases | One-to-Many | 1:N | Each item can be purchased multiple times |

## Data Flow Diagram

```
Customer creates Service Request
    ↓
System creates Queue Entry (with position)
    ↓
System creates Billing Record (with default labor fee)
    ↓
Employee assigns parts (creates Purchases)
    ↓
System updates Billing (adds parts_fee to total)
    ↓
Employee completes service
    ↓
System updates Queue status
    ↓
System updates Service Request status
    ↓
Customer pays (updates Billing payment_status)
```

## Key Business Rules

1. **Queue Management**: Each service request automatically gets a queue position based on creation order
2. **Billing Calculation**: Total amount = labor_fee + parts_fee (sum of all purchases)
3. **Stock Management**: When parts are purchased, item stock_quantity is automatically decreased
4. **Status Transitions**: Service request status affects queue status and billing payment status
5. **Role-Based Access**: Different user roles have different access levels to system functionality

## Index Strategy

### Primary Indexes
- All primary keys are automatically indexed

### Foreign Key Indexes
- customers.user_id
- employees.user_id
- service_requests.customer_id
- service_requests.employee_id
- queues.service_id
- billings.service_id
- purchases.item_id
- purchases.service_id

### Performance Indexes
- users.email (unique)
- users.role
- service_requests.status
- service_requests.date_created
- billings.payment_status
- items.category

## Data Integrity Constraints

### Referential Integrity
- All foreign key relationships enforce referential integrity
- Cascade delete is used where appropriate (e.g., deleting a user deletes their customer/employee record)
- Restrict delete is used to prevent deletion of referenced records

### Domain Integrity
- ENUM fields restrict values to predefined options
- Numeric fields have appropriate precision and scale
- Date fields ensure temporal consistency
- Nullable fields are properly defined

### Business Rule Integrity
- Queue positions are unique and sequential
- Billing total_amount is automatically calculated
- Stock quantities cannot go negative
- Service request status transitions follow business rules

This ER diagram provides a complete view of the TeknoHub database structure and serves as the foundation for understanding system data relationships and implementing business logic.