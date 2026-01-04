<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->string('subject');
            $table->enum('category', ['plant_care', 'product_details', 'delivery', 'order_support']);
            $table->enum('status', ['open', 'pending', 'closed'])->default('open');
            $table->timestamps();

            $table->index(['buyer_id', 'seller_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};