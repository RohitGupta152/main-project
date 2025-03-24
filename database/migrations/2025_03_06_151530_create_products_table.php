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
        Schema::create('products', function (Blueprint $table) {
            // $table->id();
            // $table->string('order_id'); // Reference to orders table
            // $table->string('user_name');
            // $table->string('email'); // Same as in orders table
            // $table->string('product_name');
            // $table->decimal('price', 10, 2);
            // $table->integer('quantity');
            // $table->timestamps();

            $table->id(); // Auto-increment Product ID (Primary Key)

            $table->unsignedBigInteger('order_table_id'); // Stores Order's Auto-Increment ID
            
            $table->string('order_no'); // User-entered Order ID (from Orders table)
            $table->string('product_name');
            $table->decimal('price', 10, 2);
            $table->integer('quantity');

            // $table->decimal('weight', 10, 2);
            
            // $table->decimal('length', 10, 2);
            // $table->decimal('width', 10, 2);
            // $table->decimal('height', 10, 2);
            
            // $table->timestamps();
            $table->dateTime('created_date');
            $table->dateTime('updated_date');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
