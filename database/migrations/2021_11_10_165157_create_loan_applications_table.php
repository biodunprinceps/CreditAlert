<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_applications', function (Blueprint $table) {
            $table->id();
            $table->string('loanid')->nullable();
            $table->string('borrower_id')->nullable();
            $table->string('auth_code')->nullable();
            $table->string('telephone')->nullable();
            $table->string('ippis_number')->nullable();
            $table->string('loan_amount')->nullable();
            $table->string('tenor')->nullable();
            $table->string('repayment')->nullable();
            $table->string('approved_loan_amount')->nullable();
            $table->string('approved_repayment')->nullable();
            $table->string('final_loan_amount')->nullable();
            $table->string('final_interest')->nullable();
            $table->string('final_repayment')->nullable();
            $table->string('net_pay')->nullable();
            $table->string('place_of_work')->nullable();
            $table->string('bank_code')->nullable();
            $table->string('acc_no')->nullable();
            $table->string('bvn')->nullable();
            $table->string('preferred_bank_code')->nullable();
            $table->string('giro_reference')->nullable();
            $table->string('preferred_bank_account_no')->nullable();
            $table->string('loan_status')->default('-1');
            $table->string('remita_status')->default('0');
            $table->string('remita_authcode')->nullable();
            $table->string('remita_customer_id')->nullable();
            $table->string('remita_mda_name')->nullable();
            $table->string('remita_name')->nullable();
            $table->string('remita_salary_bank_account')->nullable();
            $table->string('remita_salary_bank_name')->nullable();
            $table->string('awaiting_confirmation_status')->default('0');
            $table->string('disbursement_status')->default('0');
            $table->string('mandate_reference')->nullable();
            $table->longText('giro_pay_load')->nullable();
            $table->date('due_date')->nullable();
            $table->date('final_due_date')->nullable();
            $table->string('document_upload')->nullable();
            $table->string('facial_recognition')->default(0);
            $table->string('type')->nullable();
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
        Schema::dropIfExists('loan_applications');
    }
}
