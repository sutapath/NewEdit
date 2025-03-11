<?php
// database/migrations/2024_09_17_create_publish_requests_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('publish_requests', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('user_id');  
            $table->string('document_type'); 
            $table->date('send_date'); 
            $table->timestamps();  
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('publish_requests', function (Blueprint $table) { 
            $table->dropForeign(['user_id']);
        });
        
        Schema::dropIfExists('publish_requests');
    }
}
