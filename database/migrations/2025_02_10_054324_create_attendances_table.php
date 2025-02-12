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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->timestamp('check_in')->nullable();
            $table->timestamp('check_out')->nullable();
            $table->decimal('work_hours')->nullable(); // Fixed syntax
            // $table->timestamp('time_ust')->default(DB::raw('CURRENT_TIMESTAMP')); // UTC time
            // $table->timestamp('time_ist')->nullable();
            $table->timestamp();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
