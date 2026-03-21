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
use App\Models\LaborRate;
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
        DB::table('labor_rates')->truncate();
        DB::table('employees')->truncate();
        DB::table('customers')->truncate();
        DB::table('service_requests')->truncate();
        DB::table('queues')->truncate();
        DB::table('items')->truncate();
        DB::table('purchases')->truncate();
        DB::table('billings')->truncate();

        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();

        // 1. Create Admin Users
        User::create([
            'full_name' => 'Admin User',
            'email' => 'admin@teknohub.com',
            'contact_number' => '1234567890',
            'password' => Hash::make('password'),
            'role' => 'Admin',
            'account_status' => 'Active',
            'email_verified_at' => Carbon::now(),
        ]);

        User::create([
            'full_name' => 'Super Admin',
            'email' => 'superadmin@teknohub.com',
            'contact_number' => '0987654321',
            'password' => Hash::make('password'),
            'role' => 'Admin',
            'account_status' => 'Active',
            'email_verified_at' => Carbon::now(),
        ]);

        // 2. Create Employee Users
        $employee1 = Employee::create([
            'user_id' => User::create([
                'full_name' => 'John Doe',
                'email' => 'johndoe@teknohub.com',
                'contact_number' => '1112223333',
                'password' => Hash::make(1), // Employee ID will be 1
                'role' => 'Employee',
                'account_status' => 'Active',
                'email_verified_at' => Carbon::now(),
            ])->user_id,
            'department_name' => 'Hardware Repair',
            'job_title' => 'Senior Technician',
            'skills' => 'Component-level repair, soldering, diagnostics',
        ]);

        $employee2 = Employee::create([
            'user_id' => User::create([
                'full_name' => 'Jane Smith',
                'email' => 'janesmith@teknohub.com',
                'contact_number' => '4445556666',
                'password' => Hash::make(2), // Employee ID will be 2
                'role' => 'Employee',
                'account_status' => 'Active',
                'email_verified_at' => Carbon::now(),
            ])->user_id,
            'department_name' => 'Software Support',
            'job_title' => 'Software Specialist',
            'skills' => 'OS troubleshooting, malware removal, data recovery',
        ]);

        $employee3 = Employee::create([
            'user_id' => User::create([
                'full_name' => 'Peter Jones',
                'email' => 'peterjones@teknohub.com',
                'contact_number' => '7778889999',
                'password' => Hash::make(3), // Employee ID will be 3
                'role' => 'Employee',
                'account_status' => 'Pending',
                'email_verified_at' => Carbon::now(),
            ])->user_id,
            'department_name' => 'Hardware Repair',
            'job_title' => 'Junior Technician',
            'skills' => 'Screen replacement, battery swaps',
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

// 4. Create Labor Rates
        LaborRate::create(['service_type' => 'diagnostic', 'standard_fee' => 100.00, 'description' => 'Basic diagnostic fee']);
        LaborRate::create(['service_type' => 'hardware_repair', 'standard_fee' => 500.00, 'description' => 'Hardware repair labor']);
        LaborRate::create(['service_type' => 'software_install', 'standard_fee' => 200.00, 'description' => 'Software installation']);
        LaborRate::create(['service_type' => 'cleaning', 'standard_fee' => 150.00, 'description' => 'Device cleaning']);
        LaborRate::create(['service_type' => 'upgrade', 'standard_fee' => 300.00, 'description' => 'Hardware upgrade']);
        LaborRate::create(['service_type' => 'data_recovery', 'standard_fee' => 800.00, 'description' => 'Data recovery services']);

// 5. Create Inventory Items
        Item::create(['item_name' => 'Laptop Battery A-123', 'category' => 'Hardware', 'price' => 85.50, 'stock_quantity' => 20]);
        Item::create(['item_name' => '256GB SSD', 'category' => 'Components', 'price' => 45.00, 'stock_quantity' => 30]);
        Item::create(['item_name' => '15.6" LCD Screen', 'category' => 'Hardware', 'price' => 120.00, 'stock_quantity' => 15]);
        $itemForPurchase = Item::create(['item_name' => '8GB DDR4 RAM', 'category' => 'Components', 'price' => 35.75, 'stock_quantity' => 50]);

        // 6. Create Service Requests, Queue, and Billing
$serviceRequest1 = ServiceRequest::create([
            'customer_id' => $customer2->customer_id,
            'employee_id' => $employee1->employee_id,
            'service_type' => 'hardware_repair',
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
            'service_type' => 'hardware_repair',
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
            'employee_id' => $employee1->employee_id,
            'service_type' => 'upgrade',
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
        $this->command->info('Admin Logins: admin@teknohub.com / superadmin@teknohub.com (password: password)');
        $this->command->info('Employee Logins: Use email and their Employee ID as the password.');
        $this->command->info('Customer Logins: alice@example.com / bob@example.com (password: password)');
    }
}