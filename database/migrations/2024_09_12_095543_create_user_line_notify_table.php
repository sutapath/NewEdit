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
        Schema::create('user_line_notify', function (Blueprint $table) {
            $table->id(); // Primary key for this table
            $table->unsignedBigInteger('user_id'); // Reference to user id
            $table->string('line_notify_token', 255);
            $table->timestamps(); // created_at, updated_at

            // Foreign key constraint to reference user_id in users table
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_line_notify');
    }
};
