<?php
namespace App\services;

use App\Models\User;
use App\Models\LoanLog;
use App\Models\RemitaPayment;
use App\Models\LoanApplication;
use App\Models\RemitaLoanHistory;

class SchedulerServices{

    public static function processPayment()
    {
        $loans = LoanApplication::orderBy('id','DESC')->where('status', "4")->take(1)->get();
        if($loans->isEmpty()){
            exit;
        }
        foreach($loans as $loan){
            $user = User::where('authid',$loan->authid)->first();
            $loan_history = RemitaLoanHistory::where('authcode',$loan->auth_code)->get();
            $sum = 0;
            foreach($loan_history as $value){
                $sum += $value['repaymentamount'];
            }
            $total_repayment_amount = $sum;
            $loan_amount = $loan->loan_amount;
            $last_salary_entry = RemitaPayment::where('authcode',$loan->auth_code)->orderBy('id','ASC')->first();
            $net_pay = $last_salary_entry->amount;
            $available_balance = $net_pay - $total_repayment_amount -$loan_amount - 5000;
            $affordability_status = false;
            if($available_balance > 0){
                $affordability_status = true;
            }
            if($affordability_status == false){
                $loan->cron_error = "Affordability issue";
                $loan->status = "+4";
                $loan->save();
                continue;
            }

            if($bankdata = PaystackServices::validateAccountNumber(trim($loan->preferred_bank_account_no), trim($loan->preferred_bank_code))){
                if($bankdata['status'] == 'success'){
                    $loan->verified_bank_full_name = $bankdata["data"]["account_name"];
                    $loan->save();
                }else{
                    $loan->cron_error = "Error validating account number";
                    $loan->save();
                    continue;
                }
            }else{
                $loan->cron_error = "Error validating account number";
                $loan->save();
                continue;
            }
            $borrower = LoanDiskServices::searchTelephoneLoanDisk($user->telephone);

            if($borrower == 0){ //not found on loandisk
                $borrower = LoanDiskServices::searchEmailLoanDisk($user->email);
                if($borrower == 0){ //not found on loandisk

                    //create new borrower
                    $newuniquenumber = self::generateUniqueNumber(); //generate new unique number
                    $loanid = LoanDiskServices::getLoanUniqueNumber(0, $newuniquenumber); //generate loan id
                    $loan->loanid = $loanid;
                    $loan->save();

                    $new_log = new LoanLog;
                    $new_log->uniquenumber = $newuniquenumber;
                    $new_log->ippisnumber = $user->ippisnumber;
                    $new_log->numberofloan = '1';
                    $new_log->save();

                    $addBorrower = LoanDiskServices::addBorrower($loan->id);
                    if (!$addBorrower) {
                        $loan->cron_error = "Error adding Borrower";
                        // $loan->status = '-3';
                        $loan->save();
                        continue;
                    }
                    if ($addBorrower['status'] == 'error') {
                        $loan->cron_error = "Add borrower " . $addBorrower['message'][0];
                        // $loan->status = '-3';
                        $loan->save();
                        continue;
                    } else {
                        $loan->cron_error = "Add borrower " . $addBorrower['message'];
                        $loan->save();
                    }

                    if(!$new_borrower = LoanDiskServices::fetchBorrowerWithTelephone($user->telephone)){
                        if(!$new_borrower = LoanDiskServices::fetchBorrowerFromEmail($user->email)){
                            $loan->cron_error = "Error fetching new borrower detail";
                            $loan->save();
                            continue;
                        }
                    }
                    $new_log->loandisk_borrowerid = $new_borrower['borrower_id'];
                    $new_log->save();
                }else{
                    $borrower_numeric_id = $borrower['borrower_id'];
                    $borrower_alphanumeric_id = $borrower['borrower_unique_number'];
                    $no = "01";
                    if ($all_loans = LoanDiskServices::fetchAllLoansofBorrower($borrower_numeric_id)) {
                        $no = count($all_loans);
                        $no += 1;
                    if ($no < 10) {
                        $no = "0$no";
                        }
                    }

                    $loanid = "$borrower_alphanumeric_id-$no";
                    $loan->loanid = $loanid;
                    $loan->save();

                    if(!$log = LoanLog::where('ippisnumber',$user->ippisnumber)->first()){
                        $log = new LoanLog();
                    }
                    $log->loandisk_borrowerid = $borrower_numeric_id;
                    $log->ippisnumber = $user->ippisnumber;
                    $log->numberofloan = '1';
                    $log->uniquenumber = $borrower_alphanumeric_id;
                    $log->save();
                }
            }else{
                $borrower_numeric_id = $borrower['borrower_id'];
                $borrower_alphanumeric_id = $borrower['borrower_unique_number'];
                // $loanid = LoanDiskServices::getLoanUniqueNumber(0, $borrower_alphanumeric_id);
                $no = "01";
                if ($all_loans = LoanDiskServices::fetchAllLoansofBorrower($borrower_numeric_id)) {
                    $no = count($all_loans);
                    $no += 1;
                    if ($no < 10) {
                        $no = "0$no";
                    }
                }

                $loanid = "$borrower_alphanumeric_id-$no";
                // return $loanid;
                $loan->loanid = $loanid;
                $loan->save();
                if(!$log = LoanLog::where('ippisnumber',$user->ippisnumber)->first()){
                    $log = new LoanLog();
                }
                $log->ippisnumber = $user->ippisnumber;
                $log->loandisk_borrowerid = $borrower_numeric_id;
                $log->numberofloan = '1';
                $log->uniquenumber = $borrower_alphanumeric_id;
                $log->telephone = $borrower['borrower_mobile'];
                $log->rsptelephone = $borrower['borrower_mobile'];
                $log->save();
            }

            $addLoan = LoanDiskServices::addLoan($loan->id);
            if (!$addLoan) {
                $loan->cron_error = "Error adding Loan";
                // $loan->status = '-3';
                $loan->save();
                continue;
            }
            if ($addLoan['status'] == 'error') {
                $loan->cron_error = "Add loan " . $addLoan['message'][0];
                // $loan->status = '-3';
                $loan->save();
                continue;
            } else {
                $loan->cron_error = "Add loan " . $addLoan['message'];
                $loan->save();
            }

            $deduction_setup = UtilityService::setupDeduction($loan->loanid);
            if(!$deduction_setup){
                $loan->cron_error = "Error setting up deduction";
                $loan->save();
                continue;
            }

            if($deduction_setup['status'] == 'error'){
                $loan->cron_error = "Error setting up deduction";
                $loan->save();
                continue;
            }

        }
        return "done";
    }


    public static function generateUniqueNumber()
    {
        $existingrecord = LoanLog::all();
        $uniquenumbers = array();
        if($existingrecord->isNotEmpty()){
            foreach ($existingrecord as $value) {
                $uniquenumber = $value["uniquenumber"];
                if (Helper::startsWith($uniquenumber, 'S')) {
                } else {
                    $uniquenumbers[] = ltrim($uniquenumber, 'CA'); // remove CA
                }
            }
            sort($uniquenumbers);  //sort array in ascending order
            $newuniquenumber = end($uniquenumbers) + 1;
            $newuniquenumber = "CA" . $newuniquenumber;
        }else{
            $newuniquenumber = "CA" . 1000000;
        }
        return $newuniquenumber;
    }

}
