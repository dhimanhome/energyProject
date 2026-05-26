<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sites', function (Blueprint $table): void {
            $table->id();
            $table->string('site_code')->unique();
            $table->string('site_name');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->unsignedInteger('allowed_radius')->default(100);
            $table->text('address')->nullable();
            $table->string('status')->default('active')->index();
            $table->timestamps();
        });

        Schema::create('employees', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('employee_code')->unique();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->unique();
            $table->string('status')->default('active')->index();
            $table->timestamp('last_seen')->nullable();
            $table->timestamps();
        });

        Schema::create('employee_site', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('unassigned_at')->nullable();
            $table->unique(['employee_id', 'site_id']);
        });

        Schema::create('submissions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->unsignedInteger('distance_from_site')->default(0);
            $table->decimal('voltage', 8, 2);
            $table->decimal('current', 8, 2);
            $table->decimal('load_percent', 5, 2);
            $table->decimal('energy_reading', 12, 2);
            $table->text('notes')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('equipment_photo_path')->nullable();
            $table->boolean('suspicious_flag')->default(false)->index();
            $table->string('risk_level')->default('normal')->index();
            $table->timestamp('gps_recorded_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['site_id', 'created_at']);
            $table->index(['employee_id', 'created_at']);
        });

        Schema::create('location_updates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('accuracy', 8, 2)->nullable();
            $table->timestamp('recorded_at')->nullable();
            $table->timestamps();
            $table->index(['employee_id', 'created_at']);
        });

        Schema::create('suspicious_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('submission_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('site_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type')->index();
            $table->string('severity')->default('warning')->index();
            $table->text('message');
            $table->json('context')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('auditable_type')->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['auditable_type', 'auditable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('suspicious_logs');
        Schema::dropIfExists('location_updates');
        Schema::dropIfExists('submissions');
        Schema::dropIfExists('employee_site');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('sites');
    }
};
