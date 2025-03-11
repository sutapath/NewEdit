<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Student; 
use App\Models\Officer; 
use App\Models\Scholar; 
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // สร้างตาราง students
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('citizen_id', 13)->unique();
            $table->timestamps();            
            $table->string('title', 5)->nullable();
            $table->string('fname')->nullable();
            $table->string('lname')->nullable();
        });

        // สร้างตาราง officers
        Schema::create('officers', function (Blueprint $table) {
            $table->id();
            $table->string('citizen_id', 13)->unique();
            $table->timestamps();            
            $table->string('title', 5)->nullable();
            $table->string('fname')->nullable();
            $table->string('lname')->nullable();
        });

        // สร้างตาราง scholars
        Schema::create('scholars', function (Blueprint $table) {
            $table->id();
            $table->string('citizen_id', 13);
            $table->timestamps();            
            $table->string('title', 5)->nullable();
            $table->string('fname')->nullable();
            $table->string('lname')->nullable();
            $table->string('student_code')->nullable();
        });
                // สร้างตาราง scholars
                Schema::create('members', function (Blueprint $table) {
                    $table->id();
                    $table->string('citizen_id', 13);
                    $table->timestamps();            
                    $table->string('title', 5)->nullable();
            $table->string('fname')->nullable();
            $table->string('lname')->nullable();
                });
            Schema::create('intlscholars', function (Blueprint $table) {
                    $table->id();
                    $table->string('citizen_id', 13);
                    $table->timestamps();            
                    $table->string('title', 5)->nullable();
            $table->string('fname')->nullable();
            $table->string('lname')->nullable();
                });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
        Schema::dropIfExists('officers');
        Schema::dropIfExists('scholars');
        Schema::dropIfExists('members');
    }
};
