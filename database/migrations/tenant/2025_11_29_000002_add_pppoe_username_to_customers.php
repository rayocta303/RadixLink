<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('pppoe_username')->nullable()->after('pppoe_password');
            $table->foreignId('nas_id')->nullable()->after('service_plan_id');
            $table->foreignId('pppoe_profile_id')->nullable()->after('nas_id');
            $table->foreignId('hotspot_profile_id')->nullable()->after('pppoe_profile_id');
            
            $table->index('pppoe_username');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['pppoe_username']);
            $table->dropColumn(['pppoe_username', 'nas_id', 'pppoe_profile_id', 'hotspot_profile_id']);
        });
    }
};
