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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps(); 
            $table->string('student_code', 15)->nullable();
            $table->string('title', 5)->nullable();
            $table->string('fname')->nullable();
            $table->string('lname')->nullable();
            $table->date('birthday')->nullable();
            $table->string('phone', 15)->nullable();
            $table->string('id_card', 15)->nullable();
            $table->string('faculty', 255)->nullable();
            $table->string('branch', 255)->nullable();
            $table->string('en_year', 5)->nullable();
            $table->string('house_no', 10)->nullable();
            $table->string('village_name')->nullable();
            $table->string('sub_district')->nullable();
            $table->string('district')->nullable();
            $table->string('province')->nullable();
            $table->string('zip_code', 15)->nullable();
            $table->string('officer_code', 15)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};