<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('shortname')->unique();
            $table->string('nasname');
            $table->integer('ports')->nullable();
            $table->string('secret');
            $table->string('server')->nullable();
            $table->string('community')->nullable();
            $table->string('description')->nullable();
            $table->enum('type', ['mikrotik', 'unifi', 'openwrt', 'cisco', 'other'])->default('mikrotik');
            
            $table->string('location_name')->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->integer('coverage')->nullable()->comment('Coverage radius in meters');
            
            $table->string('api_username')->nullable();
            $table->string('api_password')->nullable();
            $table->integer('api_port')->default(8728);
            $table->integer('winbox_port')->default(8291);
            $table->boolean('use_ssl')->default(false);
            $table->boolean('is_active')->default(true);
            $table->enum('status', ['enabled', 'disabled'])->default('enabled');
            
            $table->boolean('vpn_enabled')->default(false);
            $table->string('vpn_secret')->nullable();
            $table->integer('vpn_port')->default(1701);
            $table->enum('vpn_type', ['l2tp', 'pptp', 'sstp', 'ovpn', 'wireguard'])->nullable();
            $table->string('vpn_server')->nullable();
            $table->string('vpn_username')->nullable();
            $table->string('vpn_password')->nullable();
            $table->string('vpn_local_address')->nullable();
            $table->string('vpn_remote_address')->nullable();
            
            $table->timestamp('last_seen')->nullable();
            $table->json('info')->nullable();
            $table->timestamps();
        });

        Schema::create('service_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['hotspot', 'pppoe', 'dhcp', 'hybrid'])->default('hotspot');
            $table->decimal('price', 12, 2)->default(0);
            $table->integer('validity')->default(30);
            $table->enum('validity_unit', ['minutes', 'hours', 'days', 'months'])->default('days');
            $table->string('bandwidth_up')->nullable();
            $table->string('bandwidth_down')->nullable();
            $table->bigInteger('quota_bytes')->nullable();
            $table->boolean('has_fup')->default(false);
            $table->string('fup_bandwidth_up')->nullable();
            $table->string('fup_bandwidth_down')->nullable();
            $table->bigInteger('fup_threshold_bytes')->nullable();
            $table->boolean('can_share')->default(false);
            $table->integer('max_devices')->default(1);
            $table->integer('simultaneous_use')->default(1);
            $table->boolean('is_active')->default(true);
            $table->json('radius_attributes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_plans');
        Schema::dropIfExists('nas');
    }
};
