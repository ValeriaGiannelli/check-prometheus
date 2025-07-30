<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogIgnoreTable extends Migration
{
    public function up()
    {
        Schema::create('log_ignore', function (Blueprint $table) {
            $table->id();
            $table->string('client_id')->nullable(); // Matches logs.client_id
            $table->text('description'); // Error description to ignore
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('log_ignore');
    }
}