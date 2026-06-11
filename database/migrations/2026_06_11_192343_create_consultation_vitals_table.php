<?php

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
        Schema::create('consultation_vitals', function (Blueprint $table) {

            $table->id();

            $table->foreignId('consultation_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('weight', 5, 2)->nullable();

            $table->decimal('height', 5, 2)->nullable();

            $table->integer('heart_rate')->nullable();

            $table->integer('respiratory_rate')->nullable();

            $table->decimal('temperature', 4, 1)->nullable();

            $table->string('blood_pressure')->nullable();

            $table->integer('oxygen_saturation')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultation_vitals');
    }
};
