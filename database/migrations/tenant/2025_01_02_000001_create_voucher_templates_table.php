<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('tenant')->create('voucher_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->longText('html_template');
            $table->longText('css_styles')->nullable();
            $table->integer('paper_size')->default(1);
            $table->string('orientation')->default('portrait');
            $table->integer('vouchers_per_page')->default(12);
            $table->integer('columns_per_row')->default(3);
            $table->boolean('show_qr_code')->default(true);
            $table->boolean('show_logo')->default(false);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('voucher_templates');
    }
};
