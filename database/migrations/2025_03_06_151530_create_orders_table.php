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
        Schema::create('orders', function (Blueprint $table) {
            // $table->id();
            // $table->string('order_id'); // Order1, Order2, etc.
            // $table->string('user_name');
            // $table->string('email'); // Unique per user but allows multiple orders
            // $table->decimal('total_amount', 10, 2);
            // $table->integer('total_qty');
            // $table->timestamps();

            $table->id(); // Auto-increment Order ID (Primary Key)

            $table->unsignedBigInteger('user_id'); // Mapping to Users table

            $table->string('order_no'); // User-entered Order ID
            $table->string('customer_name');
            $table->string('email');
            
            // $table->decimal( 'charged_amount', 10, 2)->nullable();
            // $table->decimal('total_weight', 10, 2)->default(0);

            $table->string('contact_no')->nullable();
            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
            $table->string('pin_code')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->integer('total_qty')->default(0);

            $table->decimal( 'charged_amount', 10, 2)->nullable();    
            $table->decimal( 'charged_weight', 10, 2)->nullable();

            $table->decimal('weight', 10, 2)->default(0);
            $table->decimal('length', 10, 2)->default(0);
            $table->decimal('width', 10, 2)->default(0);
            $table->decimal('height', 10, 2)->default(0);

            $table->boolean('updated_orders')->default(0);
            $table->boolean('status')->default(true); // Indicates if the order status is active
            $table->boolean('is_deleted')->default(false); // Soft delete flag
            // $table->timestamps();
            $table->dateTime('created_date')->nullable();
            $table->dateTime('updated_date')->nullable();

            // Index on user_id for faster lookup
            $table->index('user_id');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
