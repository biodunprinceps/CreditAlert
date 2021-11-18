<?php
namespace App\services;

use App\Models\User;
use App\Models\LoanLog;
use App\Models\LoanApplication;
use App\services\UtilityService;

class LoanDiskServices{

    public static function fetchBorrowerWithTelephone($telephone,$branch = null)
    {
        if ($branch == null) {
            $branch = env('BRANCH_ID');
        }
        $url = "https://api-main.loandisk.com/" . env('PUBLIC_KEY') . "/" . $branch . "/borrower/borrower_mobile/$telephone";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "cache-control: no-cache",
                "content-type: application/json",
                "Authorization: Basic AkMbezWYERkE5NcDsXAM7YzkxDySG9amAKvajU9d",
            ),

        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        $data = json_decode(curl_exec($curl), true);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if (!$data) {
            return false;
        }
        if (!empty($data['error'])) {
            return false;
        } else {
            $result = $data["response"]["Results"]["0"][0];
            return $result;
        }
    }

    public static function fetchBorrowerFromEmail($email, $branch = null)
    {
        if ($branch == null) {
            $branch = env('BRANCH_ID');
        }
        $url = "https://api-main.loandisk.com/" . env('PUBLIC_KEY') . "/" . $branch . "/borrower/borrower_email/$email";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "cache-control: no-cache",
                "content-type: application/json",
                "Authorization: Basic AkMbezWYERkE5NcDsXAM7YzkxDySG9amAKvajU9d",
            ),

        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        $data = json_decode(curl_exec($curl), true);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if (!$data) {
            return false;
        }
        if (!empty($data['error'])) {
            return false;
        } else {
            $result = $data["response"]["Results"]["0"][0];
            return $result;
        }
    }

    public static function searchTelephoneLoanDisk($telephone)
    {
        $url = "https://api-main.loandisk.com/" . env('PUBLIC_KEY') . "/" . env('BRANCH_ID') . "/borrower/borrower_mobile/$telephone";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "cache-control: no-cache",
                "content-type: application/json",
                "Authorization: Basic AkMbezWYERkE5NcDsXAM7YzkxDySG9amAKvajU9d",
            ),

        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        $data = json_decode(curl_exec($curl), true);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if (!$data) {
            return false;
        }
        if (!empty($data['error'])) {
            return 0;
        } else {
            return $data['response']['Results'][0][0];
        }

    }

    public static function searchEmailLoanDisk($telephone)
    {
        $url = "https://api-main.loandisk.com/" . env('PUBLIC_KEY') . "/" . env('BRANCH_ID') . "/borrower/borrower_email/$telephone";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "cache-control: no-cache",
                "content-type: application/json",
                "Authorization: Basic AkMbezWYERkE5NcDsXAM7YzkxDySG9amAKvajU9d",
            ),

        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        $data = json_decode(curl_exec($curl), true);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if (!$data) {
            return false;
        }
        if (!empty($data['error'])) {
            return 0;
        } else {
            return $data['response']['Results'][0][0];
        }
    }

    public static function getLoanUniqueNumber($num, $uniquenumber)
    {
        $result = $currentloan = "";
        if ($num < 10) {
            $num = $num + 1;
            $currentloan = str_pad($num, 2, "0", STR_PAD_LEFT);
        } else {
            $currentloan = $num;
        }

        $result = $uniquenumber . "-" . $currentloan;
        return $result;
    }

    public static function addBorrower($id, $reserved = null)
    {
        $loan = LoanApplication::where("id", "=", "$id")->first();
        $user = User::where('authid',$loan->authid)->first();


        $records = LoanLog::where("ippisnumber", "=", "$user->ippisnumber")->first();

        // $title = $loan->title;
        // if (ucfirst($loan->title) == "Mr") {
        //     $title = 1;
        // }

        // if (ucfirst($loan->title) == "Mrs") {
        //     $title = 2;
        // }

        // if (ucfirst($loan->title) == "Miss") {
        //     $title = 3;
        // }

        // if (ucfirst($loan->title) == "Mrs") {
        //     $title = 4;
        // }

        $post_array = array();
        $post_array['borrower_unique_number'] = "$records->uniquenumber";
        $post_array['borrower_fullname'] = ucwords(strtolower($user->fullname));
        $post_array['borrower_business_name'] = ucwords(strtolower($user->place_of_work));
        $post_array['borrower_country'] = 'NG';
        // $post_array['borrower_title'] = $title;
        $post_array['borrower_working_status'] = 'Employee';
        $post_array['borrower_gender'] = 'Male';
        $post_array['borrower_mobile'] = $user->telephone;
        $post_array['borrower_dob'] = $user->d_o_b;
        $post_array['borrower_description'] = 'New Customer from Credit Wallet';
        $post_array['borrower_email'] = $user->email;
        // $post_array['borrower_address'] = ucwords(strtolower($user->home_address));
        // $post_array['borrower_city'] = ucwords(strtolower($loan->city));
        // $post_array['borrower_province'] = ucwords(strtolower($loan->state));

        //RSP Linked No or RRR
        $post_array['custom_field_2610'] = $user->telephone;

        //DD Start Date
        // $post_array['custom_field_2367'] = $loan->ddstartdate;

        //DD End Date
        // $post_array['custom_field_2366'] = $loan->ddenddate;

        //IPPIS (or Staff) ID no.
        $post_array['custom_field_1135'] = $user->ippisnumber;

        if ($loan->preferred_bank_code == '044') {
            $post_array['custom_field_1168'] = "Access Bank Plc";
        }

        if ($loan->preferred_bank_code == '023') {
            $post_array['custom_field_1168'] = "Citi Bank";
        }

        if ($loan->preferred_bank_code == '063') {
            $post_array['custom_field_1168'] = "Diamond Bank Plc";
        }

        if ($loan->preferred_bank_code == '050') {
            $post_array['custom_field_1168'] = "Ecobank Plc";
        }

        if ($loan->preferred_bank_code == '070') {
            $post_array['custom_field_1168'] = "Fidelity Bank Plc";
        }

        if ($loan->preferred_bank_code == '011') {
            $post_array['custom_field_1168'] = "First Bank of Nigeria PLC";
        }

        if ($loan->preferred_bank_code == '214') {
            $post_array['custom_field_1168'] = "First City Monument Bank PLC";
        }

        if ($loan->preferred_bank_code == '058') {
            $post_array['custom_field_1168'] = "Guaranty Trust Bank PLC";
        }

        if ($loan->preferred_bank_code == '030') {
            $post_array['custom_field_1168'] = "Heritage Bank";
        }

        if ($loan->preferred_bank_code == '301') {
            $post_array['custom_field_1168'] = "Jaiz Bank";
        }

        if ($loan->preferred_bank_code == '082') {
            $post_array['custom_field_1168'] = "Keystone Bank";
        }

        if ($loan->preferred_bank_code == '076') {
            $post_array['custom_field_1168'] = "Skye Bank PLC";
        }

        if ($loan->preferred_bank_code == '221') {
            $post_array['custom_field_1168'] = "Stanbic IBTC Bank PLC";
        }

        if ($loan->preferred_bank_code == '232') {
            $post_array['custom_field_1168'] = "Sterling Bank PLC";
        }

        if ($loan->preferred_bank_code == '100') {
            $post_array['custom_field_1168'] = "SUNTRUST BANK";
        }

        if ($loan->preferred_bank_code == '032') {
            $post_array['custom_field_1168'] = "Union Bank of Nigeria PLC";
        }

        if ($loan->preferred_bank_code == '033') {
            $post_array['custom_field_1168'] = "United Bank for Africa PLC";
        }

        if ($loan->preferred_bank_code == '215') {
            $post_array['custom_field_1168'] = "Unity Bank PLC";
        }

        if ($loan->preferred_bank_code == '035') {
            $post_array['custom_field_1168'] = "Wema Bank PLC";
        }

        if ($loan->preferred_bank_code == '057') {
            $post_array['custom_field_1168'] = "Zenith Bank PLC";
        }

        //Bank Acc No.
        $post_array['custom_field_1169'] = $loan->preferred_bank_account_no;

        //Org. Code
        $post_array['custom_field_1616'] = "";

        //Direct Debit (DD)
        $post_array['custom_field_2362'] = "Remita Salary";

        //Org Code
        $place_of_work = trim($user->place_of_work);
        $organizationalcode = UtilityService::getOrganizationCode($place_of_work);
        if ($organizationalcode != null) {
            $post_array['custom_field_1616'] = $organizationalcode;
        }
        // $organizationalcode = OrganizationCode::where("mdaname", "", "$place_of_work")->first();
        // $showorganizationalcode = true;
        // if ($organizationalcode != null) {
            // $post_array['custom_field_1616'] = $organizationalcode->code;
        // }

        //Loan or Deposit
        $post_array['custom_field_1175'] = 'Loan';
        if ($reserved) {
            $post_array['custom_field_5443'] = $reserved['bankName'];
            $post_array['custom_field_5444'] = $reserved['accountNumber'];
        }

        $url = "https://api-main.loandisk.com/" . env('PUBLIC_KEY') . "/" . env('BRANCH_ID') . "/borrower";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($post_array),
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "cache-control: no-cache",
                "content-type: application/json",
                "Authorization: Basic AkMbezWYERkE5NcDsXAM7YzkxDySG9amAKvajU9d",
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        $data_array = json_decode(curl_exec($curl), true);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if (!$data_array) {
            return false;
        }

        if (!empty($data_array['response']['Errors'])) {
            $records->delete();

            $loan->loanid = "";
            $loan->save();
            return ['status' => 'error', "message" => $data_array['response']['Errors']];
        } else {
            $status = $data_array['http']['code'];
            if ($status == 200) {
                if (Helper::startsWith($user->ippisnumber, "R")) {
                    $borrower_id = $data_array['response']['borrower_id'];
                    $records->loandisk_borrowerid = $borrower_id;
                    $records->rsptelephone = $user->telephone;
                    $records->telephone = $user->telephone;
                    $records->save();
                    return ['status' => 'success', "message" => $data_array['response']['borrower_id']];
                } else {
                    $borrower_id = $data_array['response']['borrower_id'];
                    $records->loandisk_borrowerid = $borrower_id;
                    $records->save();
                    return ['status' => 'success', "message" => $data_array['response']['borrower_id']];
                }
            } else {
                return false;
            }
        }
    }

    public static function addLoan($id)
    {
        $loan = LoanApplication::where("id", "=", "$id")->first();
        $user = User::where('authid',$loan->authid)->first();
        $records = LoanLog::where('ippisnumber',$user->ippisnumber)->first();
        $due_date = $loan->due_date;
        $due_date = date("d/m/Y", strtotime($due_date));

        $post_array = array();
        $post_array['loan_product_id'] = 50259; // Change to your loan product id
        $post_array['borrower_id'] = $records->loandisk_borrowerid;
        $post_array['loan_id'] = date("YmdHis") . rand(11111, 99999);
        $post_array['loan_application_id'] = $loan->loanid;
        $post_array['loan_disbursed_by_id'] = 13909; // Change to your loan disbursed by id
        $post_array['loan_principal_amount'] = $loan->loan_amount;
        $post_array['loan_released_date'] = date("d/m/Y");
        $post_array['loan_interest_method'] = 'flat_rate';
        $post_array['loan_interest_type'] = 'fixed';
        $post_array['loan_interest_period'] = 'Month';
        $post_array['loan_interest'] = $loan->monthly_interest;
        $post_array['loan_duration_period'] = 'Months';
        $post_array['loan_duration'] = 1;
        $post_array['loan_payment_scheme_id'] = '3';
        $post_array['loan_num_of_repayments'] = 1;
        $post_array['loan_decimal_places'] = 'round_off_to_two_decimal';
        $post_array['loan_interest_start_date'] =  date("d/m/Y");
        $post_array['loan_first_repayment_date'] = $due_date;
        $post_array['first_repayment_amount'] = number_format((float) $loan->repayment, 2, '.', '');
        $post_array['last_repayment_amount'] = number_format((float) $loan->repayment, 2, '.', '');
        $post_array['loan_override_maturity_date'] = $due_date;
        $post_array['loan_interest_schedule'] = "charge_interest_normally";
        $post_array['loan_fee_schedule_928'] = "distribute_fees_evenly";
        $post_array['loan_fee_id_928'] = 0;
        $post_array['loan_status_id'] = "1";
        //IPPIS (or Staff) ID no.
        $post_array['custom_field_1288'] = $user->ippisnumber;

        //Monthly Re-Pmt
        $post_array['custom_field_1294'] = number_format((float) $loan->repayment, 2, '.', ''); //

        //Direct Debit (DD)
        $post_array['custom_field_2413'] = "REMITA SALARY";

        if ($loan->preferred_bank_code == '044') {
            $post_array['custom_field_8482'] = "Access Bank Plc";
        }

        if ($loan->preferred_bank_code == '023') {
            $post_array['custom_field_8482'] = "Citi Bank";
        }

        if ($loan->preferred_bank_code == '063') {
            $post_array['custom_field_8482'] = "Diamond Bank Plc";
        }

        if ($loan->preferred_bank_code == '050') {
            $post_array['custom_field_8482'] = "Ecobank Plc";
        }

        if ($loan->preferred_bank_code == '070') {
            $post_array['custom_field_8482'] = "Fidelity Bank Plc";
        }

        if ($loan->preferred_bank_code == '011') {
            $post_array['custom_field_8482'] = "First Bank of Nigeria PLC";
        }

        if ($loan->preferred_bank_code == '214') {
            $post_array['custom_field_8482'] = "First City Monument Bank PLC";
        }

        if ($loan->preferred_bank_code == '058') {
            $post_array['custom_field_8482'] = "Guaranty Trust Bank PLC";
        }

        if ($loan->preferred_bank_code == '030') {
            $post_array['custom_field_8482'] = "Heritage Bank";
        }

        if ($loan->preferred_bank_code == '301') {
            $post_array['custom_field_8482'] = "Jaiz Bank";
        }

        if ($loan->preferred_bank_code == '082') {
            $post_array['custom_field_8482'] = "Keystone Bank";
        }

        if ($loan->preferred_bank_code == '076') {
            $post_array['custom_field_8482'] = "Skye Bank PLC";
        }

        if ($loan->preferred_bank_code == '221') {
            $post_array['custom_field_8482'] = "Stanbic IBTC Bank PLC";
        }

        if ($loan->preferred_bank_code == '232') {
            $post_array['custom_field_8482'] = "Sterling Bank PLC";
        }

        if ($loan->preferred_bank_code == '100') {
            $post_array['custom_field_8482'] = "SUNTRUST BANK";
        }

        if ($loan->preferred_bank_code == '032') {
            $post_array['custom_field_8482'] = "Union Bank of Nigeria PLC";
        }

        if ($loan->preferred_bank_code == '033') {
            $post_array['custom_field_8482'] = "United Bank for Africa PLC";
        }

        if ($loan->preferred_bank_code == '215') {
            $post_array['custom_field_8482'] = "Unity Bank PLC";
        }

        if ($loan->preferred_bank_code == '035') {
            $post_array['custom_field_8482'] = "Wema Bank PLC";
        }

        if ($loan->preferred_bank_code == '057') {
            $post_array['custom_field_8482'] = "Zenith Bank PLC";
        }

        //Payment Account Number
        $post_array['custom_field_8481'] = $loan->preferred_bank_account_no;

        //Org. Code
        $place_of_work = trim($user->place_of_work);
        $organizationalcode = UtilityService::getOrganizationCode($place_of_work);
        if ($organizationalcode != null) {
            $post_array['custom_field_1618'] = $organizationalcode;
        }

        // if (Helper::startsWith($user->ippisnumber, "R")) {
        //     $post_array['custom_field_4559'] = "RSP Only";
        // } elseif (Helper::startsWith($user->ippisnumber, "A")) {
        //     $post_array['custom_field_4559'] = "Others";
        // } else {
        //     if ($loan->remitastatus == "1") {
        //         $post_array['custom_field_4559'] = "IPPIS & RSP";
        //     } else {
        //         $post_array['custom_field_4559'] = "IPPIS Only";
        //     }
        // }

        $url = "https://api-main.loandisk.com/" . env('PUBLIC_KEY') . "/" . env('BRANCH_ID') . "/loan";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($post_array),
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "cache-control: no-cache",
                "content-type: application/json",
                "Authorization: Basic AkMbezWYERkE5NcDsXAM7YzkxDySG9amAKvajU9d",
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        $data_array = json_decode(curl_exec($curl), true);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if (!$data_array) {
            return false;
        }

        if (!empty($data_array['response']['Errors'])) {
            return ['status' => 'error', "message" => $data_array['response']['Errors']];
            exit();
        } else {
            $status = $data_array['http']['code'];
            if ($status == 200) {
                $loan_id = $data_array['response']['loan_id'];
                $records->loandisk_loanid = $loan_id;
                $loan->status = "5";
                $user->loan_status = "5";
                $loan->save();
                $user->save();
                $records->save();
                return ['status' => 'success', "message" => $data_array['response']['loan_id']];
            } else {
                return false;
            }
        }
    }

    public static function fetchAllLoansofBorrower($borrowerid, $branch = null)
    {
        if ($branch == null) {
            $branch = env('BRANCH_ID');
        }
        $url = "https://api-main.loandisk.com/" . env('PUBLIC_KEY') . "/" . $branch . "/loan/borrower/$borrowerid/from/1/count/50";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "cache-control: no-cache",
                "content-type: application/json",
                "Authorization: Basic AkMbezWYERkE5NcDsXAM7YzkxDySG9amAKvajU9d",
            ),

        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        $data = json_decode(curl_exec($curl), true);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if (!$data) {
            return false;
        }
        if (!empty($data['error']) || !empty($data['response']['Errors'])) {
            return false;
        } else {
            $result = $data["response"]["Results"]["0"];
            // $loandisk_id = $result['loan_id'];
            return $result;
        }
    }
}
