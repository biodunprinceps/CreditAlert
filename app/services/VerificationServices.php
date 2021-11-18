<?php
namespace App\services;

use App\Models\VerificationData;

class VerificationServices{

    public static function verifyIppisNumber($ippis_number)
    {
        if(!$verification = VerificationData::where('ippisnumber',$ippis_number)->first()){
            return response()->json(['status'=>'error','message'=>'Customer not found on ippis'],400);
        }
        return $verification;
    }
}
