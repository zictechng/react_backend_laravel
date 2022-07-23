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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->string('topup_email')->nullable();
            $table->string('debit_email')->nullable();
            $table->string('login_email')->nullable();
            $table->string('fa2_email')->nullable();
            $table->string('credit_email')->nullable();
            $table->string('system_update')->nullable();
            $table->string('promo_email')->nullable();
            $table->string('otp_email')->nullable();
            $table->string('system_status')->nullable();
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
        Schema::dropIfExists('settings');
    }
};