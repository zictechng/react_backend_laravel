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
        Schema::create('transfer_funds', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('receiver_id')->nullable();
            $table->string('sender_email')->nullable();
            $table->string('sender_name')->nullable();
            $table->string('receiver_email')->nullable();
            $table->string('receiver_name')->nullable();
            $table->string('status')->nullable();
            $table->float('amt_send')->default('0.00');
            $table->string('tran_code')->nullable();
            $table->string('acct_status')->nullable();
            $table->string('acct_status2')->nullable();
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
        Schema::dropIfExists('transfer_funds');
    }
};