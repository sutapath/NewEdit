<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScholarshipTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scholarships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('officer_code');
            $table->string('scholar_name')->nullable();
            $table->date('apply_date')->nullable();
            $table->date('due_date')->nullable();
            $table->enum('status', [0, 1])->nullable(); 
            $table->text('description')->nullable();
            $table->string('file')->nullable(); 
            $table->string('type')->nullable();
            $table->string('link')->nullable();
            $table->timestamps();
        });

        Schema::create('scholarship_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('scholar_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('title', ['0', '1', '2']);
            $table->string('fname');
            $table->string('lname');
            $table->string('phone');
            $table->year('years')->nullable();
            $table->string('faculty')->nullable();
            $table->string('Interview_results')->nullable();
            $table->string('branch')->nullable();
            $table->decimal('gpax', 4, 2)->nullable(); 
            $table->string('imagefile')->nullable(); 
            $table->string('scholar_form')->nullable();
            $table->string('reg_form')->nullable();
            $table->string('fee_receipt')->nullable();
            $table->string('certificates')->nullable();
            $table->decimal('tuition_fee', 10, 2)->nullable(); 
            $table->string('scholar_type')->nullable();
            $table->string('type_ability')->nullable();
            $table->string('conduct_cert')->nullable();
            $table->string('portfolio')->nullable();
            $table->string('fam_cert')->nullable();
            $table->string('award_certs')->nullable();
            $table->string('leader_proof')->nullable();
            $table->string('gpa_image')->nullable();
            $table->string('id_card')->nullable();
            $table->enum('result', ['0', '1','2','3','4'])->nullable(); 
            $table->text('suggestions')->nullable();
            $table->date('interview_date')->nullable();
            $table->decimal('interview_score', 5, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('scholarship_recipients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('apply_id');
            $table->timestamps();
        });

        Schema::table('scholarship_applications', function (Blueprint $table) {
            // ตั้งค่า Foreign Key ที่เชื่อมกับ scholarships โดยใช้ onDelete('cascade')
            $table->foreign('scholar_id')
                  ->references('id')
                  ->on('scholarships')
                  ->onDelete('cascade');
        
            // ตั้งค่า Foreign Key ที่เชื่อมกับ users โดยใช้ onDelete('cascade')
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
        
        Schema::table('scholarship_recipients', function (Blueprint $table) {
            // ตั้งค่า Foreign Key ที่เชื่อมกับ scholarship_applications โดยใช้ onDelete('cascade')
            $table->foreign('apply_id')
                  ->references('id')
                  ->on('scholarship_applications')
                  ->onDelete('cascade');
        });
        
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scholarship_recipients', function (Blueprint $table) {
            $table->dropForeign(['apply_id']);
        });

        Schema::table('scholarship_applications', function (Blueprint $table) {
            $table->dropForeign(['scholar_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::dropIfExists('scholarship_recipients');
        Schema::dropIfExists('scholarship_applications');
        Schema::dropIfExists('scholarships');
    }
}

