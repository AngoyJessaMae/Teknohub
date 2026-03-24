# TeknoHub Exam Notes

## 1) System Definition (What TeknoHub is)
TeknoHub is a web-based Service Request and Repair Management System for a computer/tech repair shop.

Its main purpose is to:
- Receive repair requests from customers
- Assign and track work by employees/technicians
- Manage queue/order of work
- Track parts used and inventory stock
- Generate billing and payment records
- Provide role-based dashboards for Admin, Employee, and Customer

Technology used:
- Backend: Laravel (PHP)
- Database: MySQL
- Frontend: Blade templates + Bootstrap
- Authentication: Laravel auth/session

---

## 2) Core Modules and Their Functions

### A. Authentication and Account Management
- Users can log in and register.
- Roles in the system: Admin, Employee, Customer.
- Account status exists (ex: Active, Pending).
- Passwords are hashed.

What this module does:
- Validates credentials
- Starts/ends user sessions
- Redirects users to dashboard after login

### B. Dashboard Module (Role-Based)
The dashboard is different per role:

1. Admin dashboard:
- Sees total requests, pending requests, completed requests
- Sees total revenue from paid billings
- Sees user statistics (employees/customers)
- Sees recent service requests

2. Employee dashboard:
- Sees assigned service requests
- Sees active repair count
- Sees completed repair count
- Sees managed customer count

3. Customer dashboard:
- Sees own requests only
- Sees own pending/completed counts
- Sees latest service updates and billing status

### C. Service Request Module
Main business transaction starts here.

Functions:
- Create service request
- View all requests (filtered by role)
- Edit/assign employee
- Update status (pending, in_progress, completed, cancelled)
- Delete request

Important logic:
- Upon creation of service request, system also creates:
  - Queue record
  - Initial billing record
- Supports service type categories (diagnostic, hardware_repair, software_install, cleaning, upgrade, data_recovery)
- Supports appointment request date
- Can assign specific staff

### D. Queue Management Module
Purpose: organize service requests in order.

Functions:
- View queue list
- Process next request
- Update queue position

Important logic:
- Queue entries have position, queue number, status, and priority
- Processing next request updates:
  - Queue status to in_progress
  - Service request status to in_progress
- Waiting items behind it are shifted in position
- Employee-specific behavior:
  - Employee sees only queue items assigned to that employee
  - Admin can see full queue

### E. Inventory Module
Purpose: manage spare parts/items.

Functions:
- Add item
- Edit item
- Delete item
- View inventory list
- Add inventory item to a specific service request

Important logic:
- When item is used in a service request:
  - A purchase record is created
  - Item stock is decreased
  - Billing parts fee and total amount are recalculated

### F. Billing Module
Purpose: handle financial side of completed/ongoing services.

Functions:
- Create billing from a service request
- Update billing details
- Set payment mode and payment status
- View billing details

Important logic:
- Billing per service request is one-to-one
- Total amount = labor fee + parts fee
- Labor fee can be based on labor rate table by service type
- Payment status values: Paid, Unpaid, Pending
- Cancelled service request should not keep active billing

### G. Employee Management Module (Admin only)
Functions:
- Add employee account
- Edit employee details
- Delete employee account
- Auto-generate password on employee creation

Why important:
- Controls staffing records and assignment pool
- Uses one-to-one relation between user and employee profile

---

## 3) End-to-End Business Flow (Process Narrative)
Use this in essay questions that ask "How the system works":

1. Customer (or admin/employee on behalf of customer) creates a service request.
2. System saves request details (device, issue, service type, optional schedule, assigned staff).
3. System automatically creates queue entry.
4. System automatically creates initial billing record.
5. Technician/employee processes queue and starts service.
6. If parts are needed, inventory item is added to service.
7. System deducts stock and updates billing total.
8. Employee updates service status to completed when done.
9. Billing is finalized with labor fee, parts fee, payment mode, and payment status.
10. Admin and customer can monitor the request and payment state through their views.

---

## 4) Database Design Summary (High-Value for Exams)
Main entities:
- users
- customers
- employees
- service_requests
- queues
- items
- purchases
- billings
- labor_rates

Key relationships:
- One user to one customer profile (for customer role)
- One user to one employee profile (for employee role)
- One customer to many service requests
- One employee to many service requests
- One service request to one queue entry
- One service request to one billing record
- One service request to many purchases
- One item to many purchases

Design principles shown:
- Normalized tables for less redundancy
- Foreign keys for referential integrity
- Business rules encoded through status fields and relationships

---

## 5) Access Control and Security Points
What to mention in essays:
- Authentication required before accessing protected routes
- Role-based functionality and page visibility
- Input validation on create/update operations
- Password hashing
- CSRF protection in forms
- Eloquent ORM helps avoid SQL injection patterns

---

## 6) Important Statuses and Fields to Memorize

Service request status:
- pending
- in_progress
- completed
- cancelled

Queue status:
- waiting
- in_progress
- completed

Billing payment status:
- Paid
- Unpaid
- Pending

Payment modes used:
- Cash
- Credit Card
- Debit Card
- G-Cash
- PayMaya
- Bank Transfer

---

## 7) Strengths of the System
- Clear workflow from request to payment
- Automatic queue and billing creation
- Automatic inventory-stock deduction
- Role-based dashboards for clarity and security
- Structured relational database with foreign keys
- Good separation of concerns using MVC architecture

---

## 8) Limitations / Possible Improvements (Good for critical analysis questions)
- Add stricter authorization in every action (policy/middleware per action)
- Add audit trail for who changed status and billing
- Add notifications (email/SMS) for request progress
- Add reports and analytics (daily sales, top service type, inventory movement)
- Add automated tests for major workflows
- Add backup/recovery and deployment hardening for production

---

## 9) Ready-to-Use Essay Outline
If your teacher asks "Explain your system", use this structure:

1. Introduction
- TeknoHub is a repair service management information system built to digitize service operations.

2. Problem addressed
- Manual tracking causes delays, lost records, and billing/inventory inconsistencies.

3. Proposed solution
- Centralized web app with role-based access, queue tracking, inventory and billing integration.

4. How it works (workflow)
- Request creation -> queue -> repair processing -> parts usage -> billing -> payment update.

5. Key functionalities
- Service requests, queue management, inventory control, billing, employee management, dashboards.

6. Technical implementation
- Laravel MVC + MySQL + Blade + Bootstrap, relational schema with foreign keys.

7. Security and control
- Authenticated routes, role restrictions, validated forms, hashed passwords, CSRF.

8. Benefits
- Faster operations, better tracking, accountability, reduced errors, better customer visibility.

9. Conclusion
- TeknoHub improves operational efficiency and provides a scalable digital foundation for repair shop management.

---

## 10) 1-Minute Oral Review (Quick Recitation)
TeknoHub is a Laravel-based repair shop management system with three roles: Admin, Employee, and Customer. The core transaction is a service request. When a request is created, the system automatically creates a queue entry and billing record. Employees process queue tasks, update repair status, and add parts from inventory. When parts are used, stock is deducted and billing is recalculated. Billing tracks labor, parts, total, payment mode, and payment status. Dashboards are role-based so each user sees only relevant data. The database uses relational design with one-to-many and one-to-one links to keep data consistent and accurate.

---

## 11) Last-Minute Memory Anchors
- Core flow: Request -> Queue -> Repair -> Parts -> Billing -> Payment
- Billing formula: Total = Labor + Parts
- Inventory event: Add part -> Create purchase -> Decrease stock
- Role logic: Admin controls system, Employee handles assigned repairs, Customer tracks own requests
