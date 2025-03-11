<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityManagementTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // สร้างตาราง activity_informations
        Schema::create('activity_informations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // รหัสผู้จัดกิจกรรม (เชื่อมโยงกับตาราง users)
            $table->text('activity_name');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('location')->nullable();
            $table->string('organizer')->nullable();
            $table->text('description')->nullable();
            $table->integer('limits')->nullable();
            $table->date('regis_due')->nullable();
            $table->text('link')->nullable();
            $table->integer('hours')->nullable();
            $table->timestamps();

            // ตั้งค่า foreign key ที่เชื่อมโยงกับ users และลบข้อมูลที่เกี่ยวข้องแบบ cascade
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // สร้างตาราง activity_registers
        Schema::create('activity_registers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // รหัสผู้ใช้ที่ลงทะเบียน
            $table->unsignedBigInteger('activity_id'); // รหัสกิจกรรมที่ลงทะเบียน
            $table->enum('title', ['0', '1', '2']);
            $table->string('fname');
            $table->string('lname');
            $table->date('regis_date');
            $table->timestamps();

            // ตั้งค่า foreign key ที่เชื่อมโยงกับ users และ activity_informations โดยลบข้อมูลที่เกี่ยวข้องแบบ cascade
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('activity_id')->references('id')->on('activity_informations')->onDelete('cascade');
        });

        // สร้างตาราง activity_saves
        Schema::create('activity_saves', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // รหัสผู้ใช้ (เชื่อมโยงกับตาราง users)
            $table->unsignedBigInteger('regis_id'); // รหัสผู้ใช้ที่ลงทะเบียน
            $table->unsignedBigInteger('activity_id'); // รหัสกิจกรรมที่ลงทะเบียน
            $table->enum('title', ['0', '1', '2']);
            $table->string('fname');
            $table->string('lname');
            $table->text('activity_name');
            $table->date('date');
            $table->text('description');
            $table->string('file', 255);
            $table->string('institution', 255);
            $table->time('launch');
            $table->time('end');
            $table->string('years');
            $table->text('location');
            $table->integer('hours');
            $table->string('result', 5);
            $table->text('suggestions');
            $table->date('check_date');
            $table->timestamps();

            // ตั้งค่า foreign key ที่เชื่อมโยงกับ users, activity_registers และ activity_informations โดยลบข้อมูลที่เกี่ยวข้องแบบ cascade
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('regis_id')->references('id')->on('activity_registers')->onDelete('cascade');
            $table->foreign('activity_id')->references('id')->on('activity_informations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // ลบความสัมพันธ์ foreign keys และตาราง activity_saves
        Schema::table('activity_saves', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['activity_id']);
            $table->dropForeign(['regis_id']);
        });
        Schema::dropIfExists('activity_saves');

        // ลบความสัมพันธ์ foreign keys และตาราง activity_registers
        Schema::table('activity_registers', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['activity_id']);
        });
        Schema::dropIfExists('activity_registers');

        // ลบความสัมพันธ์ foreign keys และตาราง activity_informations
        Schema::table('activity_informations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('activity_informations');
    }
}
