<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\SoftDeletes;

return new class extends Migration {

    use SoftDeletes;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('synopsis');
            $table->string('genre');
            $table->mediumText('content'); // For the rich text content
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key to users table
            // $table->integer('views')->default(0);
            // $table->integer('likes')->default(0);
            // $table->integer('score')->default(0);
            $table->softDeletes(); // Adds the deleted_at column
            $table->timestamps();
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
