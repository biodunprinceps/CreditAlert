<?php
namespace App\services;

use Illuminate\Support\Facades\Http;

class PaystackServices{

    public static function validateAccountNumber($accountNumber, $bankCode)
    {
        $url = "https://api.paystack.co/bank/resolve?account_number=".$accountNumber."&bank_code=".$bankCode;
        $data = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' =>  'Bearer sk_live_efd68a918240c9547f01cca56c26929ab4038cfc',
            'Content-Type' => 'application/json'
        ])->get($url)->json();
        if ($data['status'] == false) {
            return false;
        } else {
            $newdata['status']="success";
            $body['accountnumber'] = $data['data']['account_number'];
            $body['account_name'] = $data['data']['account_name'];
            $newdata['data'] = $body;
            return $newdata;
        }
    }
}
