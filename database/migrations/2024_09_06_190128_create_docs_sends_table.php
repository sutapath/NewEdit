<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocsSendsTable extends Migration
{
    public function up()
    {
        Schema::create('docs_sends', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('user_id');
            
            $table->enum('title', ['Mr', 'Mrs', 'Miss']);
            $table->string('fname');
            $table->string('lname');
            $table->text('operat_docs')->nullable(); 
            $table->text('transcripts')->nullable();
            $table->date('date')->nullable();
            $table->year('years')->nullable();
            $table->string('result', 5)->nullable();
            $table->text('suggestions')->nullable();
            $table->date('check_date')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        });
    }

    public function down()
    {
        Schema::dropIfExists('docs_sends');
    }
}
