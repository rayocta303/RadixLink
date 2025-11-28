<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ip_pools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('pool_name')->unique();
            $table->string('range_start');
            $table->string('range_end');
            $table->string('next_pool')->nullable();
            $table->foreignId('nas_id')->nullable()->constrained('nas')->nullOnDelete();
            $table->string('type')->default('hotspot');
            $table->boolean('is_active')->default(true);
            $table->integer('total_ips')->default(0);
            $table->integer('used_ips')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('bandwidth_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_bw')->unique();
            $table->string('rate_up');
            $table->string('rate_down');
            $table->string('burst_limit_up')->nullable();
            $table->string('burst_limit_down')->nullable();
            $table->string('burst_threshold_up')->nullable();
            $table->string('burst_threshold_down')->nullable();
            $table->string('burst_time_up')->nullable();
            $table->string('burst_time_down')->nullable();
            $table->integer('priority')->default(8);
            $table->string('limit_at_up')->nullable();
            $table->string('limit_at_down')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('pppoe_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('profile_name')->unique();
            $table->foreignId('nas_id')->nullable()->constrained('nas')->nullOnDelete();
            $table->foreignId('ip_pool_id')->nullable()->constrained('ip_pools')->nullOnDelete();
            $table->foreignId('bandwidth_id')->nullable()->constrained('bandwidth_profiles')->nullOnDelete();
            $table->string('local_address')->nullable();
            $table->string('remote_address')->nullable();
            $table->string('dns_server')->nullable();
            $table->string('wins_server')->nullable();
            $table->integer('session_timeout')->nullable();
            $table->integer('idle_timeout')->nullable();
            $table->boolean('only_one')->default(true);
            $table->string('parent_queue')->nullable();
            $table->string('address_list')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->json('mikrotik_options')->nullable();
            $table->timestamps();
        });

        Schema::create('hotspot_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('profile_name')->unique();
            $table->foreignId('nas_id')->nullable()->constrained('nas')->nullOnDelete();
            $table->foreignId('ip_pool_id')->nullable()->constrained('ip_pools')->nullOnDelete();
            $table->foreignId('bandwidth_id')->nullable()->constrained('bandwidth_profiles')->nullOnDelete();
            $table->integer('shared_users')->default(1);
            $table->integer('session_timeout')->nullable();
            $table->integer('idle_timeout')->nullable();
            $table->integer('keepalive_timeout')->nullable();
            $table->string('status_autorefresh')->nullable();
            $table->boolean('transparent_proxy')->default(false);
            $table->string('mac_cookie_timeout')->nullable();
            $table->string('parent_queue')->nullable();
            $table->string('address_list')->nullable();
            $table->string('incoming_filter')->nullable();
            $table->string('outgoing_filter')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->json('mikrotik_options')->nullable();
            $table->timestamps();
        });

        Schema::create('hotspot_servers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('nas_id')->constrained('nas')->cascadeOnDelete();
            $table->string('interface');
            $table->string('address_pool')->nullable();
            $table->foreignId('ip_pool_id')->nullable()->constrained('ip_pools')->nullOnDelete();
            $table->foreignId('hotspot_profile_id')->nullable()->constrained('hotspot_profiles')->nullOnDelete();
            $table->string('login_by')->default('cookie,http-chap,http-pap');
            $table->string('http_cookie_lifetime')->default('3d');
            $table->string('split_user_domain')->nullable();
            $table->boolean('https')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('pppoe_servers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('nas_id')->constrained('nas')->cascadeOnDelete();
            $table->string('service_name');
            $table->string('interface');
            $table->integer('max_mtu')->default(1480);
            $table->integer('max_mru')->default(1480);
            $table->integer('max_sessions')->default(0);
            $table->foreignId('pppoe_profile_id')->nullable()->constrained('pppoe_profiles')->nullOnDelete();
            $table->string('authentication')->default('pap,chap,mschap1,mschap2');
            $table->boolean('keepalive')->default(true);
            $table->boolean('one_session_per_host')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('customer_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('nas_id')->nullable()->constrained('nas')->nullOnDelete();
            $table->string('username');
            $table->string('session_id')->unique();
            $table->string('nas_ip_address')->nullable();
            $table->string('nas_port_id')->nullable();
            $table->string('framed_ip_address')->nullable();
            $table->string('calling_station_id')->nullable();
            $table->string('called_station_id')->nullable();
            $table->timestamp('session_start')->nullable();
            $table->timestamp('session_stop')->nullable();
            $table->integer('session_time')->default(0);
            $table->bigInteger('input_octets')->default(0);
            $table->bigInteger('output_octets')->default(0);
            $table->string('terminate_cause')->nullable();
            $table->string('service_type')->default('hotspot');
            $table->timestamps();
            
            $table->index(['customer_id', 'session_start']);
            $table->index('session_start');
        });

        Schema::table('service_plans', function (Blueprint $table) {
            $table->foreignId('bandwidth_id')->nullable()->after('quota_bytes')->constrained('bandwidth_profiles')->nullOnDelete();
            $table->foreignId('ip_pool_id')->nullable()->after('bandwidth_id')->constrained('ip_pools')->nullOnDelete();
            $table->foreignId('pppoe_profile_id')->nullable()->after('ip_pool_id')->constrained('pppoe_profiles')->nullOnDelete();
            $table->foreignId('hotspot_profile_id')->nullable()->after('pppoe_profile_id')->constrained('hotspot_profiles')->nullOnDelete();
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('nas_id')->nullable()->after('service_type')->constrained('nas')->nullOnDelete();
            $table->foreignId('pppoe_profile_id')->nullable()->after('pppoe_password')->constrained('pppoe_profiles')->nullOnDelete();
            $table->foreignId('hotspot_profile_id')->nullable()->after('pppoe_profile_id')->constrained('hotspot_profiles')->nullOnDelete();
            $table->string('caller_id')->nullable()->after('mac_address');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['nas_id']);
            $table->dropForeign(['pppoe_profile_id']);
            $table->dropForeign(['hotspot_profile_id']);
            $table->dropColumn(['nas_id', 'pppoe_profile_id', 'hotspot_profile_id', 'caller_id']);
        });

        Schema::table('service_plans', function (Blueprint $table) {
            $table->dropForeign(['bandwidth_id']);
            $table->dropForeign(['ip_pool_id']);
            $table->dropForeign(['pppoe_profile_id']);
            $table->dropForeign(['hotspot_profile_id']);
            $table->dropColumn(['bandwidth_id', 'ip_pool_id', 'pppoe_profile_id', 'hotspot_profile_id']);
        });

        Schema::dropIfExists('customer_sessions');
        Schema::dropIfExists('pppoe_servers');
        Schema::dropIfExists('hotspot_servers');
        Schema::dropIfExists('hotspot_profiles');
        Schema::dropIfExists('pppoe_profiles');
        Schema::dropIfExists('bandwidth_profiles');
        Schema::dropIfExists('ip_pools');
    }
};
