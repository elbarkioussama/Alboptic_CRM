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
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->date('prescription_date');

            // OD (Right Eye)
            $table->decimal('od_sphere', 4, 2)->nullable();
            $table->decimal('od_cylinder', 4, 2)->nullable();
            $table->integer('od_axis')->nullable();
            $table->decimal('od_addition', 4, 2)->nullable();

            // OG (Left Eye)
            $table->decimal('og_sphere', 4, 2)->nullable();
            $table->decimal('og_cylinder', 4, 2)->nullable();
            $table->integer('og_axis')->nullable();
            $table->decimal('og_addition', 4, 2)->nullable();

            // Pupillary Distance
            $table->decimal('pd', 4, 1)->nullable();
            $table->decimal('pd_right', 4, 1)->nullable();
            $table->decimal('pd_left', 4, 1)->nullable();

            // Additional details
            $table->string('prescription_file_path')->nullable();
            $table->string('prescribed_by')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
