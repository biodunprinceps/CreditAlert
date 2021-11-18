<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_logs', function (Blueprint $table) {
            $table->id();
            $table->string('uniquenumber')->nullable();
            $table->string('ippisnumber')->nullable();
            $table->string('numberofloan')->nullable();
            $table->string('loandisk_borrowerid')->nullable();
            $table->string('rsptelephone')->nullable();
            $table->string('telephone')->nullable();
            $table->string('statement_month')->nullable();
            $table->string('password')->nullable();
            $table->string('initial_password')->nullable();
            $table->string('firstlogin')->nullable();
            $table->string('invited')->nullable();
            $table->string('referral_number')->nullable();
            $table->string('referral_due')->nullable();
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
        Schema::dropIfExists('loan_logs');
    }
}
