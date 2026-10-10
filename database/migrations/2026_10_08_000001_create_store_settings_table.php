<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id();
            $table->string('business_name')->default('ALBOPTIC');
            $table->string('trade_name')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable()->default('Marrakech');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('ice')->nullable();
            $table->string('rc')->nullable();
            $table->string('tax_identifier')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('invoice_prefix')->default('FAC-');
            $table->string('order_prefix')->default('BC-');
            $table->decimal('default_tax_rate', 5, 2)->default(20.00);
            $table->string('currency', 10)->default('MAD');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_settings');
    }
};
