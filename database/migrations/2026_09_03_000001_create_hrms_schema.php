<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Departments Table
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('code', 50)->nullable();
            $table->text('description')->nullable();
            $table->foreignId('head_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Add HR fields to Users table if not already present
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'department_id')) {
                $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'designation')) {
                $table->string('designation', 100)->nullable();
            }
            if (!Schema::hasColumn('users', 'salary')) {
                $table->decimal('salary', 12, 2)->default(0.00);
            }
            if (!Schema::hasColumn('users', 'joining_date')) {
                $table->date('joining_date')->nullable();
            }
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable();
            }
        });

        // 3. Work Shifts Table
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100); // e.g., Morning Shift, Evening Shift, Night Shift
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Duty Rosters Table
        Schema::create('duty_rosters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('shift_id')->constrained('shifts')->cascadeOnDelete();
            $table->date('duty_date');
            $table->string('status', 30)->default('SCHEDULED'); // SCHEDULED, COMPLETED, SWAPPED, OFF
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 5. Attendance Table
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('date');
            $table->time('clock_in')->nullable();
            $table->time('clock_out')->nullable();
            $table->string('status', 30)->default('PRESENT'); // PRESENT, LATE, HALF_DAY, ABSENT, ON_LEAVE
            $table->decimal('overtime_hours', 5, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['hotel_id', 'user_id', 'date']);
        });

        // 6. Leaves Table
        Schema::create('employee_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('leave_type', 50)->default('CASUAL'); // CASUAL, SICK, ANNUAL, MATERNITY, UNPAID
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_days')->default(1);
            $table->text('reason')->nullable();
            $table->string('status', 30)->default('PENDING'); // PENDING, APPROVED, REJECTED
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 7. Payrolls Table
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('month_year', 20); // e.g. "2026-09"
            $table->decimal('base_salary', 12, 2)->default(0.00);
            $table->decimal('allowances', 12, 2)->default(0.00);
            $table->decimal('overtime_amount', 12, 2)->default(0.00);
            $table->decimal('deductions', 12, 2)->default(0.00);
            $table->decimal('net_salary', 12, 2)->default(0.00);
            $table->string('status', 30)->default('PENDING'); // PENDING, PAID, CANCELLED
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method', 50)->nullable(); // BANK_TRANSFER, CASH, BKASH, CHEQUE
            $table->string('transaction_reference', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
        Schema::dropIfExists('employee_leaves');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('duty_rosters');
        Schema::dropIfExists('shifts');
        Schema::dropIfExists('departments');
    }
};
