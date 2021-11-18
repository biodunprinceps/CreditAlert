<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string("unique_no")->nullable();
            $table->string('borrower_id')->nullable();
            $table->string('title')->nullable();
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('bankcode')->nullable();
            $table->string('accountnumber')->nullable();
            $table->string('ippisnumber')->nullable();
            $table->string('telephone')->nullable();
            $table->string('alt_telephone')->nullable();
            $table->string('email')->nullable();
            $table->string('place_of_work')->nullable();
            $table->string('netpay')->nullable();
            $table->string('password')->nullable();
            $table->string('passcode')->nullable();
            $table->string('d_o_b')->nullable();
            $table->mediumText('home_address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('profile_status')->default('0');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
