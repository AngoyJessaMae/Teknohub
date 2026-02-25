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
        Schema::table('billings', function (Blueprint $table) {
            $table->enum('payment_mode', ['Cash', 'Credit Card', 'Debit Card', 'G-Cash', 'PayMaya', 'Bank Transfer'])->nullable()->after('payment_status');
            $table->date('payment_date')->nullable()->after('payment_mode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            $table->dropColumn(['payment_mode', 'payment_date']);
        });
    }
};
