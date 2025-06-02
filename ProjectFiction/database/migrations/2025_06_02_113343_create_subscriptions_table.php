<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id(); // Or remove this if you prefer a composite primary key
            $table->foreignId('subscriber_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('subscribed_to_id')->constrained('users')->onDelete('cascade');
            $table->unique(['subscriber_id', 'subscribed_to_id']); // Prevent duplicate subscriptions
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
