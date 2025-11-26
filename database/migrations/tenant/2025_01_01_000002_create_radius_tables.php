<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('radcheck', function (Blueprint $table) {
            $table->id();
            $table->string('username', 64)->default('');
            $table->string('attribute', 64)->default('');
            $table->string('op', 2)->default(':=');
            $table->string('value', 253)->default('');
            $table->timestamps();
            
            $table->index('username');
        });

        Schema::create('radreply', function (Blueprint $table) {
            $table->id();
            $table->string('username', 64)->default('');
            $table->string('attribute', 64)->default('');
            $table->string('op', 2)->default('=');
            $table->string('value', 253)->default('');
            $table->timestamps();
            
            $table->index('username');
        });

        Schema::create('radgroupcheck', function (Blueprint $table) {
            $table->id();
            $table->string('groupname', 64)->default('');
            $table->string('attribute', 64)->default('');
            $table->string('op', 2)->default(':=');
            $table->string('value', 253)->default('');
            $table->timestamps();
            
            $table->index('groupname');
        });

        Schema::create('radgroupreply', function (Blueprint $table) {
            $table->id();
            $table->string('groupname', 64)->default('');
            $table->string('attribute', 64)->default('');
            $table->string('op', 2)->default('=');
            $table->string('value', 253)->default('');
            $table->timestamps();
            
            $table->index('groupname');
        });

        Schema::create('radusergroup', function (Blueprint $table) {
            $table->id();
            $table->string('username', 64)->default('');
            $table->string('groupname', 64)->default('');
            $table->integer('priority')->default(1);
            $table->timestamps();
            
            $table->index('username');
        });

        Schema::create('radacct', function (Blueprint $table) {
            $table->id('radacctid');
            $table->string('acctsessionid', 64)->default('');
            $table->string('acctuniqueid', 32)->default('');
            $table->string('username', 64)->default('');
            $table->string('realm', 64)->default('');
            $table->string('nasipaddress', 15)->default('');
            $table->string('nasportid', 32)->nullable();
            $table->string('nasporttype', 32)->nullable();
            $table->timestamp('acctstarttime')->nullable();
            $table->timestamp('acctupdatetime')->nullable();
            $table->timestamp('acctstoptime')->nullable();
            $table->integer('acctinterval')->nullable();
            $table->unsignedInteger('acctsessiontime')->nullable();
            $table->string('acctauthentic', 32)->nullable();
            $table->string('connectinfo_start', 128)->nullable();
            $table->string('connectinfo_stop', 128)->nullable();
            $table->bigInteger('acctinputoctets')->nullable();
            $table->bigInteger('acctoutputoctets')->nullable();
            $table->string('calledstationid', 50)->default('');
            $table->string('callingstationid', 50)->default('');
            $table->string('acctterminatecause', 32)->default('');
            $table->string('servicetype', 32)->nullable();
            $table->string('framedprotocol', 32)->nullable();
            $table->string('framedipaddress', 15)->default('');
            $table->string('framedipv6address', 45)->default('');
            $table->string('framedipv6prefix', 45)->default('');
            $table->string('framedinterfaceid', 44)->default('');
            $table->string('delegatedipv6prefix', 45)->default('');
            $table->string('class', 64)->nullable();
            $table->timestamps();
            
            $table->unique('acctuniqueid');
            $table->index(['username', 'acctstoptime']);
            $table->index('acctstarttime');
            $table->index('acctstoptime');
            $table->index('nasipaddress');
        });

        Schema::create('radpostauth', function (Blueprint $table) {
            $table->id();
            $table->string('username', 64)->default('');
            $table->string('pass', 64)->default('');
            $table->string('reply', 32)->default('');
            $table->timestamp('authdate')->useCurrent();
            $table->string('class', 64)->nullable();
            
            $table->index('username');
            $table->index('authdate');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('radpostauth');
        Schema::dropIfExists('radacct');
        Schema::dropIfExists('radusergroup');
        Schema::dropIfExists('radgroupreply');
        Schema::dropIfExists('radgroupcheck');
        Schema::dropIfExists('radreply');
        Schema::dropIfExists('radcheck');
    }
};
