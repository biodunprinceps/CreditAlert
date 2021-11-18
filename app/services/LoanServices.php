<?php
namespace App\services;

use App\Models\IppisVerification;
use DateTime;
use Carbon\Carbon;
use App\Models\User;
use App\services\Helper;
use App\Models\RemitaPayment;
use App\Models\LoanApplication;
use App\Models\RemitaLoanHistory;

class LoanServices
{
        //status
        // -1 = Incomplete
        // 0= new application
        // 4 = awaiting payment
        // 1 =disbursed
        // 2 =cancelled
        // 3 = rejected

    public static function apply($request)
    {
        $ippis_number = $request->ippis_number;
        $telephone = $request->telephone;
        $email = $request->email;

        //if user exist
        if($user = User::where('email',$email)->where('telephone',$telephone)->first()){
            $user->loan_status = $user->loan_status;
            $user->save();
            if($user->loan_status == "1"){
                return response()->json(['status'=>'error', 'message'=>'You have an existing loan application with us'],400);
            }

            if($user->loan_status == "0"){
                return response()->json(['status'=>'error', 'message'=>'You have a loan application in processing, please try again later'],400);
            }

            if($user->loan_status == "-1"){
                $loan = LoanApplication::where('authid',$user->authid)->where('status',-1)->first();
                return response()->json(['status'=>'success', 'message'=>'Loan Application Successful', 'id'=>$loan->id],200);
            }

            return self::getOffer($user->authid,null,$type=2);


        }else{ //new user
            //get loan offer
            $authid = 'AUTH' . date("Ymdhis").rand(111111,999999).rand(111111,999999);
            $result = self::checkRemita($telephone,$type=1,$email,$ippis_number,$authid);
            return self::getOffer($result["authid"],$result["auth_code"],$type=1);
        }


        // return response()->json(['status'=>'success', 'message'=>'Loan application successful'],200);
    }

    public static function getOffer($authid,$authcode=null,$type)
    {
        $today = Date('Y-m-d');
        $today = date('Y-m-d', strtotime($today));
        if($type == 2){ //existing user
            $user = User::where('authid',$authid)->first();
            $updated_at = $user->updated_at;
            $updated_at = strtotime($updated_at);
            $current_time = strtotime(date('Y-m-d H:i:s'));
            $diff = abs($current_time - $updated_at);
            $years = floor($diff / (365*60*60*24));
            $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
            $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
            $hours = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60));
            if($hours > 24)
            {
                $result = self::checkRemita($user->telephone,$type=2,$user->email,$user->ippis_number,$user->authid);
                if(!$salary = RemitaPayment::where('authcode',$result["authcode"])->first()){
                    return response()->json(['status'=>'error', 'message'=>'You cannot take a loan at this point'],400);
                }
                $authcode = $result['auth_code'];

            }else{
                $loan = LoanApplication::where('authid',$user->authid)->orderBy('id','ASC')->first();
                $authcode = $loan->auth_code;
            }
        }
        $salary = RemitaPayment::where('authcode',$authcode)->first();
        $payment_date = strtotime($salary->paymentdate);
        $last_payment_date = date("Y-m-d",$payment_date);
        $today = date("Y-m-d");

        $current_date = $today;
        $fdate = $last_payment_date;
        $tdate = $current_date;
        $datetime1 = new DateTime($fdate);
        $datetime2 = new DateTime($tdate);
        $interval = $datetime1->diff($datetime2);
        $days = $interval->format('%a');
        if($days > 30){
            return response()->json(['status'=>'error', 'message'=>'You cannot take a loan at this point'],400);
        }

        $loan_amount = 0.5 * $salary->amount;
        $loan_amount = round($loan_amount, -3);
        $today = Carbon::now()->format('d');
        if($today < 15){
            $due_date = date('Y-m-5',strtotime("+1 month"));
        }else{
            $due_date = date('Y-m-5',strtotime("+2 month"));
        }
        $giro_reference = date('YmdHis'). Helper::randomString(20, true);
        $user = User::where('authid',$authid)->first();

        $interest = 0.15 * $loan_amount;
        $total_repayment = $interest + $loan_amount;
        $loan = new LoanApplication();
        $loan->giro_reference = $giro_reference;
        $loan->authid = $authid;
        $loan->due_date = $due_date;
        $loan->repayment = $total_repayment;
        $loan->loan_amount = $loan_amount;
        $loan->auth_code = $authcode;
        $loan->preferred_bank_code = $user->bankcode;
        $loan->preferred_bank_account_no = $user->accountnumber;
        $user->loan_status = "-1";
        $user->save();
        $loan->save();
        return response()->json(['status'=>'success', 'message'=>'Loan Application Successful', 'id'=>$loan->id],200);

    }

    public static function createNewUser($fullname,$telephone,$customer_id,$email,$organization,$bvn,$salary_account,$bank_name,$ippis_number = null,$authid)
    {
        $user = new User();
        $user->authid = $authid;
        $user->fullname = $fullname;
        $user->telephone = $telephone;
        $user->email = $email;
        $user->place_of_work = $organization;
        $user->bvn = $bvn;
        $user->accountnumber = $salary_account;
        $user->bankcode = $bank_name;
        $user->ippisnumber = $ippis_number;
        $user->customer_id = $customer_id;
        $user->save();
    }

    public static function checkRemita($telephone,$type,$email,$ippis_number=null,$authid)
    {
        $authcode = date("YmdHis") . Helper::randomString(20, true);
        if($result = RemitaServices::remitaRetrievedDeductions($telephone, $authcode)){
            if($result['status'] == 'success'){
                $customer_id = $result['data']['customerId'];
                $fullname = $result['data']['customerName'];
                $accountnumber = $result['data']['accountNumber'];
                $bankcode = $result['data']['bankCode'];
                $bvn = $result['data']['bvn'];
                $place_of_work = $result['data']['companyName'];
                $salary_details = $result['data']['salaryPaymentDetails'];
                $loan_payments = $result['data']['loanHistoryDetails'];
                if($type == 1){
                    self::createNewUser($fullname,$telephone,$customer_id,$email,$place_of_work,$bvn,$accountnumber,$bankcode,$ippis_number,$authid);
                }
                self::saveRemitaSalary($salary_details,$authcode);
                self::saveRemitaPayment($loan_payments,$authcode);
                return [
                    "authid"=>$authid,
                    "auth_code"=>$authcode
                ];

            }else{
                return response()->json(['status'=>'error','message'=>'We cannot offer you a loan at this point, please try again laterrrrr'],400);
            }
        }else{
            return response()->json(['status'=>'error','message'=>'We cannot offer you a loan at this point, please try again later'],400);
        }
    }

    public static function saveRemitaSalary($salary_details,$authcode)
    {
        foreach($salary_details as $salary_detail){
            $salary = new RemitaPayment();
            $salary->authcode = $authcode;
            $salary->paymentdate = $salary_detail['paymentDate'];
            $salary->amount = $salary_detail['amount'];
            $salary->save();
        }

    }

    public static function saveRemitaPayment($loan_payments,$authcode)
    {
        foreach($loan_payments as $payment){
            $loan = new RemitaLoanHistory();
            $loan->authcode = $authcode;
            $loan->status = $payment['status'];
            $loan->loanprovider = $payment['loanProvider'];
            $loan->outstandingamount = $payment['outstandingAmount'];
            $loan->loandisbursementdate = $payment['loanDisbursementDate'];
            $loan->repaymentamount = $payment['repaymentAmount'];
            $loan->repaymentfreq = $payment['repaymentFreq'];
            $loan->loanamount = $payment['loanAmount'];
            $loan->save();
        }
    }

    public static function oneLoan($request)
    {
        $id = $request->id;
        $sum = 0;
        $ippis_details = null;
        $existing_customer = false;
        if(!$loan = LoanApplication::where('id',$id)->with('user','loanHistory','remitapayments')->first()){
            return response()->json(['status'=>'error','message'=>'Loan Id not found'],400);
        }
        $user = User::where('authid',$loan->authid)->first();

        if($loandisk_data_telephone = LoanDiskServices::fetchBorrowerWithTelephone($user->telephone)){
            $existing_customer = true;
        }
        if($loandisk_data_email = LoanDiskServices::fetchBorrowerFromEmail($user->email)){
            $existing_customer = true;
        }
        $ippis_number = $user->ippisnumber;
        if($ippis_number != null){
            $ippis_details = IppisVerification::where('ippisnumber',$ippis_number)->first();
        }
        $loan_history = RemitaLoanHistory::where('authcode',$loan->auth_code)->get();
        foreach($loan_history as $value){
            $sum += $value['repaymentamount'];
        }
        $total_repayment_amount = $sum;
        $loan_amount = $loan->loan_amount;
        // $salary_count = RemitaPayment::where('authcode',$loan->auth_code)->orderBy('id','ASC')->count();
        $last_salary_entry = RemitaPayment::where('authcode',$loan->auth_code)->orderBy('id','ASC')->first();
        $net_pay = $last_salary_entry->amount;
        $available_balance = $net_pay - $total_repayment_amount -$loan_amount - 5000;
        $affordability_status = false;
        if($available_balance > 0){
            $affordability_status = true;
        }
        $loan->affordability_status = $affordability_status;
        $loan->save();
        return response()->json(['status'=>'success','data'=>$loan, 'total_repayment_amount'=>$total_repayment_amount,'netpay'=>$net_pay, 'affordability_status'=>$affordability_status, 'Ippis_details'=>$ippis_details,'existing_customer'=>$existing_customer],200);
    }

    public static function submitApplication($request)
    {
        $loan_amount = $request->loan_amount;
        $id = $request->id;
        if(!$loan = LoanApplication::where('id',$id)->first()){
            return response()->json(['status'=>'error','message'=>'Loan Id not found'],400);
        }
        if(!$user = User::where('authid',$loan->authid)->first()){
            return response()->json(['status'=>'error','message'=>'User not found'],400);
        }
        $today = Carbon::now()->format('d');
        if($today < 15){
            $due_date = date('Y-m-5',strtotime("+1 month"));
        }else{
            $due_date = date('Y-m-5',strtotime("+2 month"));
        }

        $interest = 0.15 * $loan_amount;
        $total_repayment = $interest + $loan_amount;
        $loan->loan_amount = $loan_amount;
        $loan->repayment = $total_repayment;
        $loan->due_date = $due_date;
        $loan->status = "0";
        $user->loan_status = "0";
        $user->save();
        $loan->save();
        return response()->json(['status'=>'success','message'=>"Loan application submitted successfully"],200);
    }

    public static function changeLoanStatus($request)
    {
        $id = $request->id;
        // $status = $request->status;
        $loan_amount = $request->loan_amount;
        $type = $request->type;

        //types--- 1-> approve   2->cancel  3->reject


        if(!$loan = LoanApplication::where('id',$id)->first()){
            return response()->json(['status'=>'error','message'=>'Loan Id not found'],400);
        }

        $user = User::where('authid',$loan->authid)->first();

        if($type == 1){ //approve
            $sum = 0;
            $status = "4";
            if(!$loan = LoanApplication::where('id',$id)->with('user','loanHistory','remitapayments')->first()){
                return response()->json(['status'=>'error','message'=>'Loan Id not found'],400);
            }

            if($user->place_of_work == null){
                return response()->json(['status'=>'error','message'=>'Place of work not found'],400);
            }
            $loan_history = RemitaLoanHistory::where('authcode',$loan->auth_code)->get();
            foreach($loan_history as $value){
                $sum += $value['repaymentamount'];
            }
            $total_repayment_amount = $sum;
            $last_salary_entry = RemitaPayment::where('authcode',$loan->auth_code)->orderBy('id','ASC')->first();
            $net_pay = $last_salary_entry->amount;
            $available_balance = $net_pay - $total_repayment_amount - $loan_amount  - 5000;
            if($available_balance <= 0){
                return response()->json(['status'=>'error','message'=>'You cannot approve this loan at this point'],400);
            }

            $today = Carbon::now()->format('d');
            if($today < 15){
                $due_date = date('Y-m-5',strtotime("+1 month"));
            }else{
                $due_date = date('Y-m-5',strtotime("+2 month"));
            }

            $interest = 0.15 * $loan_amount;
            $total_repayment = $interest + $loan_amount;
            $loan->loan_amount = $loan_amount;
            $loan->repayment = $total_repayment;
            $loan->due_date = $due_date;
            $loan->monthly_interest = $total_repayment - $loan_amount;
        }

        if($type == 3){
            //reject
            $status = "3";
        }

        if($type == 2){
            //cancel
            $status = "2";
        }


        $loan->status = $status;
        $user->loan_status = $status;
        $user->save();
        $loan->save();
        return response()->json(['status'=>'success','message'=>'Loan Application status changed successfully'],200);
    }

    public static function allLoansByStatus($request)
    {
        $loans = LoanApplication::query();
        $status = $request->status;
        if($request->search_text){
            $search_text = $request->search_text;
            $loans = $loans->where('loanid',$search_text);
        }
        $loans = $loans->where('status',$status)->paginate($request->page_size);
        return response()->json(['status'=>'success','loans'=>$loans],200);
    }

    public static function storePassport($request)
    {
        $loan = null;
        if(!$loan = LoanApplication::where('id',$request->id)->first()){
            return response()->json(['status'=>'error','message'=>'Loan Id not found'],400);
        }
        if ($file = $request->file('passport_file')) {
            $mime = $file->getMimeType();
            $extension = explode('/', $mime);
            $extension = end($extension);
            $imageName = $loan->id . 'offer_passport.' . $extension;
            $file->move('storage/documents/loans/' . $loan->id, $imageName);
            $loan->offer_passport_file = $imageName;
            $loan->save();
        }
        return response()->json(['status'=>'success','message'=>"Passport uploaded successfully"],200);
    }

    public static function editLoan($request)
    {
        if(!$loan = LoanApplication::where('id',$request->id)->first()){
            return response()->json(['status'=>'error','message'=>'Loan Id not found'],400);
        }

        if(!$user = User::where('authid',$loan->authid)->first()){
            return response()->json(['status'=>'error','message'=>'User not found'],400);
        }

        //edit in Loandisk
        $user->place_of_work = $request->place_of_work;
        $user->save();


    }

}
