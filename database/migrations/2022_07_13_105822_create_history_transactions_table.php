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
        Schema::create('history_transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('uid');
            $table->string('user_email');
            $table->string('status')->nullable();
            $table->float('send_amt')->default('0.00');
            $table->string('action_nature')->nullable();
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
        Schema::dropIfExists('history_transactions');
    }
};