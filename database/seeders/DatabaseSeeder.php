<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\ServiceRequest;
use App\Models\Queue;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Billing;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Disable foreign key checks to truncate tables
        Schema::disableForeignKeyConstraints();

        // Truncate all tables to start from a clean state
        DB::table('users')->truncate();
        DB::table('employees')->truncate();
        DB::table('customers')->truncate();
        DB::table('service_requests')->truncate();
        DB::table('queues')->truncate();
        DB::table('items')->truncate();
        DB::table('purchases')->truncate();
        DB::table('billings')->truncate();

        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();

        // 1. Create Fixed Admin User
        User::create([
            'full_name' => 'Admin User',
            'email' => 'admin@teknohub.com',
            'contact_number' => '1234567890',
            'password' => Hash::make('password'),
            'role' => 'Admin',
            'account_status' => 'Active',
            'email_verified_at' => Carbon::now(),
        ]);

        // 2. Create an Active Employee
        $activeEmployeeUser = User::create([
            'full_name' => 'John Doe',
            'email' => 'johndoe@teknohub.com',
            'contact_number' => '0987654321',
            'password' => Hash::make('password'),
            'role' => 'Employee',
            'account_status' => 'Active',
            'email_verified_at' => Carbon::now(),
        ]);

        Employee::create([
            'user_id' => $activeEmployeeUser->user_id,
            'department_name' => 'Hardware Repair',
            'job_title' => 'Senior Technician',
        ]);

        // 3. Create a Pending Employee
        $pendingEmployeeUser = User::create([
            'full_name' => 'Jane Smith',
            'email' => 'janesmith@teknohub.com',
            'contact_number' => '1122334455',
            'password' => Hash::make('password'),
            'role' => 'Employee',
            'account_status' => 'Pending',
            'email_verified_at' => Carbon::now(),
        ]);

        Employee::create([
            'user_id' => $pendingEmployeeUser->user_id,
            'department_name' => 'Customer Support',
            'job_title' => 'Trainee',
        ]);

        // 4. Create Customers
        $customerUser1 = User::create([
            'full_name' => 'Alice Johnson',
            'email' => 'alice@example.com',
            'contact_number' => '5551112222',
            'password' => Hash::make('password'),
            'role' => 'Customer',
            'account_status' => 'Active',
            'email_verified_at' => Carbon::now(),
        ]);
        Customer::create(['user_id' => $customerUser1->user_id]);

        $customerUser2 = User::create([
            'full_name' => 'Bob Williams',
            'email' => 'bob@example.com',
            'contact_number' => '5553334444',
            'password' => Hash::make('password'),
            'role' => 'Customer',
            'account_status' => 'Active',
            'email_verified_at' => Carbon::now(),
        ]);
        $customer2 = Customer::create(['user_id' => $customerUser2->user_id]);

        // 5. Create Inventory Items
        Item::create(['item_name' => 'Laptop Battery A-123', 'category' => 'Hardware', 'price' => 85.50, 'stock_quantity' => 20]);
        Item::create(['item_name' => '256GB SSD', 'category' => 'Components', 'price' => 45.00, 'stock_quantity' => 30]);
        Item::create(['item_name' => '15.6" LCD Screen', 'category' => 'Hardware', 'price' => 120.00, 'stock_quantity' => 15]);
        $itemForPurchase = Item::create(['item_name' => '8GB DDR4 RAM', 'category' => 'Components', 'price' => 35.75, 'stock_quantity' => 50]);

        // 6. Create Service Requests, Queue, and Billing
        $serviceRequest1 = ServiceRequest::create([
            'customer_id' => $customer2->customer_id,
            'employee_id' => $activeEmployeeUser->employee->employee_id,
            'device_type' => 'Laptop',
            'device_description' => 'Fails to boot, makes clicking noises.',
            'date_created' => Carbon::now()->subDays(2),
            'status' => 'in_progress',
        ]);

        Queue::create([
            'service_id' => $serviceRequest1->service_id,
            'queue_position' => 1,
            'status' => 'in_progress',
        ]);

        Billing::create([
            'service_id' => $serviceRequest1->service_id,
            'labor_fee' => 50.00,
            'parts_fee' => 0,
            'total_amount' => 50.00,
            'payment_status' => 'Unpaid',
        ]);

        $serviceRequest2 = ServiceRequest::create([
            'customer_id' => $customer2->customer_id,
            'device_type' => 'Smartphone',
            'device_description' => 'Cracked screen after a drop.',
            'date_created' => Carbon::now()->subDay(),
            'status' => 'pending',
        ]);

        Queue::create([
            'service_id' => $serviceRequest2->service_id,
            'queue_position' => 2,
            'status' => 'waiting',
        ]);

        Billing::create([
            'service_id' => $serviceRequest2->service_id,
            'labor_fee' => 0,
            'parts_fee' => 0,
            'total_amount' => 0,
            'payment_status' => 'Pending',
        ]);

        // 7. Create a completed Service with a Purchase
        $completedService = ServiceRequest::create([
            'customer_id' => $customer2->customer_id,
            'employee_id' => $activeEmployeeUser->employee->employee_id,
            'device_type' => 'Laptop',
            'device_description' => 'Needed a RAM upgrade.',
            'date_created' => Carbon::now()->subDays(5),
            'date_completed' => Carbon::now()->subDays(3),
            'status' => 'completed',
        ]);

        $purchase = Purchase::create([
            'service_id' => $completedService->service_id,
            'item_id' => $itemForPurchase->item_id,
            'quantity' => 1,
            'total_price' => $itemForPurchase->price,
        ]);

        Billing::create([
            'service_id' => $completedService->service_id,
            'labor_fee' => 40.00,
            'parts_fee' => $purchase->total_price,
            'total_amount' => 40.00 + $purchase->total_price,
            'payment_status' => 'Paid',
        ]);

        $this->command->info('TeknoHub database seeded successfully!');
        $this->command->info('Admin Login: admin@teknohub.com / password');
        $this->command->info('Employee Login: johndoe@teknohub.com / password');
        $this->command->info('Customer Login: alice@example.com / password');
    }
}
