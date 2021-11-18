<?php
namespace App\Http\Helpers;

use App\Models\AuditLog;
use App\Models\Loan;
use App\Models\LoanHistory;
use App\Models\Permission;
use App\Models\RemitaPayment;
use Illuminate\Support\Facades\Http;
use Validator;

class Helper
{
    public static function randomString($length = 32, $numeric = false)
    {
        $random_string = "";
        while (strlen($random_string)<$length && $length > 0) {
            if ($numeric === false) {
                $randnum = mt_rand(0, 61);
                $random_string .= ($randnum < 10) ?
                    chr($randnum+48) : ($randnum < 36 ?
                        chr($randnum+55) : $randnum+61);
            } else {
                $randnum = mt_rand(0, 9);
                $random_string .= chr($randnum+48);
            }
        }
        return $random_string;
    }

    public static function generateToken()
    {
        $curl = curl_init();
        $url = "https://api.mygiro.co/v1/login";
        $parameter = array(
            "email" => "adedeji.awolola@princepsfinance.com",
            "password" => "King1234!"
        );

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS =>json_encode($parameter),
        CURLOPT_HTTPHEADER => array(
            "content-type: application/json",
            "x-tag: ZjE0ZjU4ZWQ2MzU2YmU1MGY2YzNiYjIyYTBkYjIzYWMyZWE3OWMxMzJmZThhZWFiMzc0ZjAwZTFlNDg0ZjkzNy8vLy8vLzM1MDE="
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        $data = json_decode(curl_exec($curl), true);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $girotoken = $data["data"]["jwt"];
        return $girotoken;
    }

    public static function validateAccountNumberFromGiro($account_no, $bank_code)
    {
        $token = Helper::generateToken();
        $url = "https://api.mygiro.co/v1/products/5a9297612032791140ff390f/validate?accountId=5d133b0af4ff8400247823fa";
        $parameter = array(
            "account_number"=>$account_no,
            "bank_code"=>$bank_code
        );

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($parameter),
            CURLOPT_HTTPHEADER => array(
                "content-type:application/json",
                "authorization:Bearer ".$token,
                "x-tag: NDdmMjFjNGU1MjYzNmM0YmUwNTRkOTJhZTA3ZTQxYWNjNTVmNGI0NTk5ZDNmYTc0YjdmZWFiMTU1OTI2MzQ5Ny8vLy8vLzU0MzU="
                ),
        ));

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        $data = json_decode(curl_exec($curl), true);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return [$data,'1'];
    }


    public static function sendSMS($to, $sms)
    {
        $data = array("to" => $to,"from" => "wallet9292","sms"=>$sms,"type" => "plain","channel" => "generic","api_key" => "TLJUKv4gTg81nRzQTteEmrGFtdqWkP4LhV6FR7E6fABQYftfLqggu6PXQFOpRf");

        return $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
             ])->post("https://termii.com/api/sms/send", $data);
    }

    public static function sendWhatsApp($to, $sms)
    {
        $data = array("to" => $to,"from" => "wallet9292","sms"=>$sms,"type" => "plain","channel" => "whatsapp","api_key" => "TLJUKv4gTg81nRzQTteEmrGFtdqWkP4LhV6FR7E6fABQYftfLqggu6PXQFOpRf");

        return $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
             ])->post("https://termii.com/api/sms/send", $data);
    }

    public static function remitaRetrievedDeductions($telephone, $authcode)
    {
        $telephone = trim($telephone);
        $parameter = array(
            'authorisationCode'=>$authcode,
            'phoneNumber'=>$telephone,
            'authorisationChannel'=>"USSD"
        );
        $request_id = date("YmdHis") . Helper::randomString(20, true);
        $check_salary = Helper::checkSalary($request_id, $parameter);
        if ($check_salary['data']) {
            // Helper::save_payment_history($check_salary['data'], $authcode, $id, $ippisnumber);
            return $check_salary['data'];
        } else {
            return 0;
        }
    }

    public static function startsWith($string, $startString)
    {
        $length = strlen($startString);
        return (substr($string, 0, $length) === $startString);
    }

    // public static function insertAudit($authid, $description)
    // {
    //     $audit = new AuditLog;
    //     $audit->authid = $authid;
    //     $audit->description = $description;
    //     $audit->datecreated = date("Y-m-d H:i:s");
    //     $audit->save();
    // }

    public static function checkSalary($request_id, $parameter)
    {
        $hash = hash('sha512', env('API_KEY') .$request_id. env('API_TOKEN'));
        $curl = curl_init();
        $url = "https://login.remita.net/remita/exapp/api/v1/send/api/loansvc/data/api/v2/payday/salary/history/ph";
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($parameter),
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "cache-control: no-cache",
                "content-type: application/json",
                "authorization: remitaConsumerKey=". env('API_KEY') .", remitaConsumerToken=".$hash,
                "merchant_id: ". env('MERCHANT_ID'),
                "request_id: ".$request_id,
                "api_key: ". env('API_KEY')
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        $data = json_decode(curl_exec($curl), true);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        return $data;
    }

    // public static function save_payment_history($data, $authcode)
    // {

    //     $data = (object)$data;
    //     $netpay = [];
    //     for ($i=0; $i <= count($data->salaryPaymentDetails) - 1; $i++) {
    //         $sr = $data->salaryPaymentDetails[$i];

    //         $rem = new RemitaPayment;
    //         $rem->auth_code = $authcode;
    //         $rem->amount = $sr['amount'];
    //         $netpay[]=$sr['amount'];
    //         $rem->payment_date = $sr['paymentDate'];
    //         $rem->save();
    //     };

    //     $customerId = $data->customerId;
    //     $accountNumber = $data->accountNumber;
    //     $bankCode = $data->bankCode;

    //     $companyName =$data->companyName;

    //     $bvn = $data->bvn;
    //     $customerName = $data->customerName;

    //     $loan = Loan::where('remita_authcode', $authcode)->first();
    //     $loan->remita_status = '1';
    //     $loan->auth_code = $authcode;
    //     $loan->remita_customer_id = $customerId;
    //     $loan->acc_no = $accountNumber;
    //     $loan->remita_salary_bank_account = $accountNumber;
    //     $loan->bank_code = $bankCode;
    //     $loan->remita_salary_bank_name = $bankCode;
    //     $loan->remita_mda_name = $companyName;
    //     $loan->remita_name = $customerName;
    //     $loan->net_pay = $netpay[0];
    //     $loan->bvn = $bvn;
    //     $loan->place_of_work = $companyName;
    //     $loan->save();

    //     if ($data->loanHistoryDetails) {
    //         for ($i=0; $i <= count($data->loanHistoryDetails) - 1; $i++) {
    //             $sr = $data->loanHistoryDetails[$i];

    //             $loan_history = new LoanHistory();
    //             $loan_history->loan_provider=$sr['loanProvider'] ?? '';
    //             $loan_history->loan_amount=$sr['loanAmount'] ?? '';
    //             $loan_history->outstanding_amount=$sr['outstandingAmount'] ?? '';
    //             $loan_history->loan_disbursement_date=$sr['loanDisbursementDate'] ?? '';
    //             $loan_history->status=$sr['status'] ?? '';
    //             $loan_history->repayment_amount=$sr['repaymentAmount'] ?? '';
    //             $loan_history->repayment_freq=$sr['repaymentFreq'] ?? '';
    //             $loan_history->auth_code=$authcode;
    //             $loan_history->save();
    //         };
    //     }

    //     $user = $loan->user;
    //     $names = explode(" ", $customerName);
    //     $user->first_name = $names[0];
    //     unset($names[0]);
    //     $user->last_name = implode(" ", $names);
    //     $user->bankname = $bankCode;
    //     $user->authcode = $authcode;
    //     $user->accountnumber = $accountNumber;
    //     $user->netpay = $netpay[0];
    //     $user->organization = $companyName;
    //     $user->save();
    // }


    public static function validateAccountNumberFromMonnify($account_no, $bank_code)
    {
        $newdata = [];
        $body = [];
        $data = Http::get("https://api.monnify.com/api/v1/disbursements/account/validate?accountNumber=$account_no&bankCode=$bank_code");
        if ($data->successful()) {
            return $data;
            $newdata['status']="success";
            $body['accountnumber'] = $data['responseBody']['accountNumber'];
            $body['account_name'] = $data['responseBody']['accountName'];
            $newdata['data'] = $body;
            return $newdata;
        } else {
            return false;
        }
    }

    public static function getBankCode($bankname)
    {
        $bankcode = "000";
        if ($bankname == "STANDARD CHARTERED BANK NIGERIA PLC") {
            $bankcode = "068";
        }

        if ($bankname == "DIAMOND BANK NIGERIA PLC") {
            $bankcode = "063";
        }

        if ($bankname == "FIRST CITY MONUMENT BANK PLC") {
            $bankcode = "214";
        }

        if ($bankname == "UNITY BANK PLC") {
            $bankcode = "215";
        }

        if ($bankname == "STANBIC - IBTC BANK PLC") {
            $bankcode = "221";
        }

        if ($bankname == "STERLING BANK PLC") {
            $bankcode = "232";
        }

        if ($bankname == "JAIZ BANK") {
            $bankcode = "301";
        }

        if ($bankname == "JAIZ BANK PLC") {
            $bankcode = "301";
        }

        if ($bankname == "ACCESS BANK NIGERIA PLC") {
            $bankcode = "044";
        }

        if ($bankname == "ECOBANK NIGERIA PLC") {
            $bankcode = "050";
        }

        if ($bankname == "FIDELITY BANK PLC") {
            $bankcode = "070";
        }

        if ($bankname == "FIRST BANK OF NIGERIA PLC") {
            $bankcode = "011";
        }

        if ($bankname == "GUARANTY TRUST BANK PLC") {
            $bankcode = "058";
        }

        if ($bankname == "HERITAGE BANK") {
            $bankcode = "030";
        }

        if ($bankname == "KEYSTONE BANK PLC") {
            $bankcode = "082";
        }

        if ($bankname == "SKYE BANK PLC") {
            $bankcode = "076";
        }

        if ($bankname == "UNION BANK OF NIGERIA PLC") {
            $bankcode = "032";
        }

        if ($bankname == "UNITED BANK FOR AFRICA PLC") {
            $bankcode = "033";
        }

        if ($bankname == "WEMA BANK PLC") {
            $bankcode = "035";
        }

        if ($bankname == "ZENITH BANK PLC") {
            $bankcode = "057";
        }

        return $bankcode;
    }
}
