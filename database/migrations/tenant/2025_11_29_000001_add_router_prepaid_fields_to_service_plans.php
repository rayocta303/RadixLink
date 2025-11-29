<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_plans', function (Blueprint $table) {
            $table->string('router_name')->nullable()->after('radius_attributes');
            $table->string('pool')->nullable()->after('router_name');
            $table->boolean('prepaid')->default(true)->after('pool');
            $table->boolean('enabled')->default(true)->after('prepaid');
            $table->date('expired_date')->nullable()->after('enabled');
            
            $table->index('router_name');
            $table->index('type');
            $table->index('enabled');
        });
    }

    public function down(): void
    {
        Schema::table('service_plans', function (Blueprint $table) {
            $table->dropIndex(['router_name']);
            $table->dropIndex(['type']);
            $table->dropIndex(['enabled']);
            
            $table->dropColumn([
                'router_name',
                'pool',
                'prepaid',
                'enabled',
                'expired_date'
            ]);
        });
    }
};
