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
        Schema::create('cash_outs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('name')->nullable();
            $table->string('user_email')->nullable();
            $table->string('user_phone')->nullable();
            $table->string('amount_send')->nullable();
            $table->string('note_send')->nullable();
            $table->string('tid_code')->nullable();
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
        Schema::dropIfExists('cash_outs');
    }
};