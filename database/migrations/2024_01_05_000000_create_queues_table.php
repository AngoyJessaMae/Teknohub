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
        Schema::create('queues', function (Blueprint $table) {
            $table->id('queue_id');
            $table->unsignedBigInteger('service_id');
            $table->integer('queue_position');
            $table->enum('status', ['waiting', 'in_progress', 'completed']);
            $table->timestamps();

            $table->foreign('service_id')->references('service_id')->on('service_requests')->onDelete('cascade');
            $table->unique('service_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};
