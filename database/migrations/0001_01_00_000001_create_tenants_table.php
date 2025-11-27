<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantsTable extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('company_name');
            $table->text('address')->nullable();
            $table->string('logo')->nullable();
            $table->string('subdomain')->unique();
            $table->string('custom_domain')->nullable()->unique();
            $table->string('subscription_plan')->default('basic');
            $table->timestamp('subscription_expires_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_suspended')->default(false);
            $table->string('suspend_reason')->nullable();
            $table->integer('max_routers')->default(1);
            $table->integer('max_users')->default(50);
            $table->integer('max_vouchers')->default(100);
            $table->integer('max_online_users')->default(10);
            $table->json('settings')->nullable();
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
}
