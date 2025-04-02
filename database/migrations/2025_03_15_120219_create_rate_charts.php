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
        Schema::create('rate_charts', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->default(0); // Default to 0 for global rates

            $table->decimal('weight', 4, 1);
            $table->decimal('rate_amount', 10, 2);
            // $table->timestamps();
            $table->dateTime('created_date')->nullable();
            $table->dateTime('updated_date')->nullable();
            
            $table->index(['user_id', 'weight']); // Index for faster lookups

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rate_charts');
    }
};
