<?php
namespace App\services;

use App\services\Helper;

class RemitaServices{

    public static function remitaRetrievedDeductionsssss($telephone=null, $authcode,$bankcode,$accountnumber)
    {
        $telephone = trim($telephone);
        $parameter = array(
            'authorisationCode' => $authcode,
            'phoneNumber' => $telephone,
            'authorisationChannel' => "USSD",
        );
        $request_id = date("YmdHis") . Helper::randomString(20, true);
        $check_salary = self::checkSalary($request_id, $parameter);
        if ($check_salary) {
            if (array_key_exists('data', $check_salary)) {
                if ($check_salary['data']) {
                    return $check_salary;
                } else {
                    return 0;
                }
            }
        } else {
            return 0;
        }
    }

    public static function remitaRetrievedDeductions($authcode,$bankcode,$accountnumber)
    {
        // $telephone = trim($telephone);
        $parameter = array(
            'authorisationCode' => $authcode,
            'accountNumber' => $accountnumber,
            'bankCode' => $bankcode,
            'authorisationChannel' => "USSD",
        );
        $request_id = date("YmdHis") . Helper::randomString(20, true);
        $check_salary = self::checkSalaryByAccountNumber($request_id, $parameter);
        if ($check_salary) {
            if (array_key_exists('data', $check_salary)) {
                if ($check_salary['data']) {
                    return $check_salary;
                } else {
                    return 0;
                }
            }
        } else {
            return 0;
        }
    }

    public static function checkSalary($request_id, $parameter)
    {
        $hash = hash('sha512', env('API_KEY') . $request_id . env('API_TOKEN'));
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
                "authorization: remitaConsumerKey=" . env('API_KEY') . ", remitaConsumerToken=" . $hash,
                "merchant_id: " . env('MERCHANT_ID'),
                "request_id: " . $request_id,
                "api_key: " . env('API_KEY'),
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        $data = json_decode(curl_exec($curl), true);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        return $data;
    }

    public static function checkSalaryByAccountNumber($request_id, $parameter)
    {
        $hash = hash('sha512', env('API_KEY') . $request_id . env('API_TOKEN'));
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
                "AUTHORIZATION: remitaConsumerKey=" . env('API_KEY') . ", remitaConsumerToken=" . $hash,
                "MERCHANT_ID: " . env('MERCHANT_ID'),
                "REQUEST_ID: " . $request_id,
                "API_KEY: " . env('API_KEY'),
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        $data = json_decode(curl_exec($curl), true);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        return $data;
    }
}
