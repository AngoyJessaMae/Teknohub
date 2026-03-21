<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('admins')) {
            Schema::create('admins', function (Blueprint $table) {
                $table->id('admin_id');
                $table->unsignedBigInteger('user_id')->unique();
                $table->string('position')->nullable();
                $table->date('date_added')->nullable();
                $table->timestamps();

                $table->foreign('user_id')
                    ->references('user_id')
                    ->on('users')
                    ->onDelete('cascade');
            });
        }

        Schema::table('customers', function (Blueprint $table) {
            // Enforce Users -> Customers as one-to-one.
            $table->unique('user_id', 'customers_user_id_unique');
        });

        Schema::table('employees', function (Blueprint $table) {
            // Enforce Users -> Employees as one-to-one.
            $table->unique('user_id', 'employees_user_id_unique');
        });

        Schema::table('service_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('service_requests', 'problem_description')) {
                $table->text('problem_description')->nullable()->after('device_description');
            }
            if (!Schema::hasColumn('service_requests', 'date_received')) {
                $table->dateTime('date_received')->nullable()->after('problem_description');
            }
            if (!Schema::hasColumn('service_requests', 'appointment_request')) {
                $table->dateTime('appointment_request')->nullable()->after('date_completed');
            }
        });

        Schema::table('queues', function (Blueprint $table) {
            if (!Schema::hasColumn('queues', 'queue_number')) {
                $table->integer('queue_number')->nullable()->after('service_id');
            }
            if (!Schema::hasColumn('queues', 'priority_level')) {
                $table->string('priority_level')->default('Normal')->after('queue_position');
            }
            if (!Schema::hasColumn('queues', 'queue_status')) {
                $table->enum('queue_status', ['waiting', 'in_progress', 'completed'])
                    ->default('waiting')
                    ->after('priority_level');
            }
        });

        Schema::table('billings', function (Blueprint $table) {
            if (!Schema::hasColumn('billings', 'employee_id')) {
                $table->unsignedBigInteger('employee_id')->nullable()->after('service_id');
                $table->foreign('employee_id')
                    ->references('employee_id')
                    ->on('employees')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('billings', 'warranty')) {
                $table->string('warranty')->nullable()->after('employee_id');
            }

            if (!Schema::hasColumn('billings', 'date_billed')) {
                $table->date('date_billed')->nullable()->after('payment_status');
            }

            // Enforce ServiceRequests -> Billings as one-to-one.
            $table->unique('service_id', 'billings_service_id_unique');
        });

        Schema::table('purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('purchases', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable()->after('service_id');
                $table->foreign('customer_id')
                    ->references('customer_id')
                    ->on('customers')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('purchases', 'date_purchased')) {
                $table->dateTime('date_purchased')->nullable()->after('total_price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (Schema::hasColumn('purchases', 'customer_id')) {
                $table->dropForeign(['customer_id']);
                $table->dropColumn('customer_id');
            }

            if (Schema::hasColumn('purchases', 'date_purchased')) {
                $table->dropColumn('date_purchased');
            }
        });

        Schema::table('billings', function (Blueprint $table) {
            $table->dropUnique('billings_service_id_unique');

            if (Schema::hasColumn('billings', 'employee_id')) {
                $table->dropForeign(['employee_id']);
                $table->dropColumn('employee_id');
            }

            if (Schema::hasColumn('billings', 'warranty')) {
                $table->dropColumn('warranty');
            }

            if (Schema::hasColumn('billings', 'date_billed')) {
                $table->dropColumn('date_billed');
            }
        });

        Schema::table('queues', function (Blueprint $table) {
            if (Schema::hasColumn('queues', 'queue_number')) {
                $table->dropColumn('queue_number');
            }
            if (Schema::hasColumn('queues', 'priority_level')) {
                $table->dropColumn('priority_level');
            }
            if (Schema::hasColumn('queues', 'queue_status')) {
                $table->dropColumn('queue_status');
            }
        });

        Schema::table('service_requests', function (Blueprint $table) {
            if (Schema::hasColumn('service_requests', 'problem_description')) {
                $table->dropColumn('problem_description');
            }
            if (Schema::hasColumn('service_requests', 'date_received')) {
                $table->dropColumn('date_received');
            }
            if (Schema::hasColumn('service_requests', 'appointment_request')) {
                $table->dropColumn('appointment_request');
            }
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropUnique('employees_user_id_unique');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique('customers_user_id_unique');
        });

        if (Schema::hasTable('admins')) {
            Schema::drop('admins');
        }
    }
};
