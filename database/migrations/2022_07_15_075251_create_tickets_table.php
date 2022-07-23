<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('uid')->unsigned();
            $table->string('u_email')->nullable();
            $table->string('u_name')->nullable();
            $table->string('t_title')->nullable();
            $table->string('t_subject')->nullable();
            $table->string('t_message')->nullable();
            $table->string('t_tid')->nullable();
            $table->string('t_status')->nullable();
            $table->string('t_action')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
};