<?php
namespace App\services;

use App\Models\User;
use App\Models\LoanApplication;
use Illuminate\Support\Facades\Http;

class UtilityService {

    public static function getOrganizationCode($place_of_work)
    {
        $code = null;
        $url = "https://testapi.creditwallet.ng/api/v2/creditalert/organizationalcode/get?place_of_work=". $place_of_work;
        $response = Http::get($url);
        $data = $response->json();
        if (!$data) {
            $code = null;
        }

        if($data['status'] == 'success'){
            $code = $data['organization']['code'];
        }else{
            $code = null;
        }
        return $code;
    }

    public static function setupDeduction($loanid)
    {
        $loan = LoanApplication::where('loanid',$loanid)->first();
        $user = User::where('authid',$loan->authid)->first();
        $url = "https://testapi.creditwallet.ng/api/v2/creditalert/deduction/setup";
        $response = Http::post($url,[
            'loanid' => $loan->loanid,
            'loan_amount' => $loan->loan_amount,
            'amount' => $loan->repayment,
            'telephone' => $user->telephone,
            'date_of_disbursement' => date('Y-m-d'),
            'date_of_collection' => $loan->collection_date,
            'tenor' => 1
        ])->json();

        if($response['status'] == 'success'){
            return $response;
        }else{
            return false;
        }

    }
}
