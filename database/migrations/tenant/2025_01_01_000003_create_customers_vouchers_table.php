<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('coordinates')->nullable();
            $table->foreignId('service_plan_id')->nullable()->constrained('service_plans');
            $table->enum('service_type', ['hotspot', 'pppoe', 'dhcp', 'hybrid'])->default('hotspot');
            $table->enum('status', ['active', 'suspended', 'expired', 'disabled'])->default('active');
            $table->timestamp('registered_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->string('suspend_reason')->nullable();
            $table->string('mac_address')->nullable();
            $table->decimal('balance', 12, 2)->default(0);
            $table->boolean('auto_renew')->default(false);
            $table->string('pppoe_password')->nullable();
            $table->string('static_ip')->nullable();
            $table->json('meta')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique();
            $table->string('username', 64)->nullable();
            $table->string('password', 64)->nullable();
            $table->foreignId('service_plan_id')->constrained('service_plans');
            $table->enum('status', ['unused', 'used', 'expired', 'disabled'])->default('unused');
            $table->enum('type', ['single', 'multi'])->default('single');
            $table->integer('max_usage')->default(1);
            $table->integer('used_count')->default(0);
            $table->decimal('price', 12, 2)->default(0);
            $table->string('batch_id')->nullable();
            $table->timestamp('generated_at')->useCurrent();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('first_used_at')->nullable();
            $table->foreignId('used_by')->nullable()->constrained('customers');
            $table->string('used_mac')->nullable();
            $table->foreignId('generated_by')->nullable();
            $table->foreignId('sold_by')->nullable();
            $table->timestamps();
            
            $table->index('code');
            $table->index('batch_id');
            $table->index('status');
        });

        Schema::create('voucher_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('html_template');
            $table->text('css_styles')->nullable();
            $table->integer('paper_size')->default(4);
            $table->enum('orientation', ['portrait', 'landscape'])->default('portrait');
            $table->integer('vouchers_per_page')->default(1);
            $table->integer('columns_per_row')->default(3);
            $table->boolean('show_qr_code')->default(true);
            $table->boolean('show_logo')->default(true);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('voucher_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_id')->unique();
            $table->foreignId('service_plan_id')->constrained('service_plans');
            $table->integer('quantity');
            $table->integer('used_count')->default(0);
            $table->string('prefix')->nullable();
            $table->integer('code_length')->default(8);
            $table->enum('code_type', ['numeric', 'alpha', 'alphanumeric'])->default('alphanumeric');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher_batches');
        Schema::dropIfExists('voucher_templates');
        Schema::dropIfExists('vouchers');
        Schema::dropIfExists('customers');
    }
};
