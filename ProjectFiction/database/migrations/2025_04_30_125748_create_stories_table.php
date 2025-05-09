<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Does this have to be changed? I do think I want to save the stories as files with markup
     */
    public function up(): void
    {
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('synopsis');
            $table->string('genre');
            $table->string('file_path'); // To store the path to the uploaded file
            $table->unsignedBigInteger('user_id'); // Foreign key to the users table
            $table->timestamps(); // Creates 'created_at' and 'updated_at' columns

            // Define the foreign key relationship
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade'); // If a user is deleted, delete their stories too
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stories');
    }
};
