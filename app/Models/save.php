// $loanid =  "CA" . date("Ymdhis");
        // $today = Carbon::now()->format('d');
        // $giro_reference = date('YmdHis'). Helper::randomString(20, true);
        // $offer_code = Helper::randomString(4, true);
        // $borrower_id = "AUTH" . date("Ymdhis");
        // if($today < 15){
        //     $due_date = date('Y-m-5',strtotime("+1 month"));
        // }else{
        //     $due_date = date('Y-m-5',strtotime("+2 month"));
        // }


        // if($request->ippis_number){
        //     $event = new LoanApplication();
        //     $event->ippis_number = $request->ippis_number;
        //     $event->telephone = $request->telephone;
        //     $event->tenor = $request->tenor;
        //     $event->loan_amount = $request->loan_amount;
        //     $event->email  = $request->email;
        //     $event->type = "ippis";
        // }else{
        //     $event = new LoanApplication();
        //     $event->telephone = $request->telephone;
        //     $event->email  = $request->email;
        //     // $event->loan_amount = $request->loan_amount;
        //     $event->tenor = $request->tenor;
        //     $event->type = 'remita';
        // }

        // //remita customer
        // if($event->type == "remita"){
        //     $telephone = $event->telephone;
        //     $tenor = $event->tenor;
        //     $email = $event->email;
        //     $authcode = date("YmdHis") . Helper::randomString(20, true);
        //     //verify remita customer and get details
        //     if($result = RemitaServices::remitaRetrievedDeductions($telephone, $authcode)){
        //         if($result['status'] == 'success'){
        //                 $salary_count = $result['data']['salaryCount'];
        //                 if($salary_count < 1){
        //                     return response(['status' => 'error', 'message' => 'Salary Details is empty'], 400);
        //                 }
        //                 $sum = 0;
        //                 $customerId = $result['data']['customerId'];
        //                 $salary_details = $result['data']['salaryPaymentDetails'];
        //                 $loan_history = $result['data']['loanHistoryDetails'];
        //                 $accountnumber = $result['data']['accountNumber'];
        //                 $bankcode = $result['data']['bankCode'];
        //                 $bvn = $result['data']['bvn'];
        //                 $organization = $result['data']['companyName'];
        //                 $name = $result['data']['customerName'];
        //                 $names = explode(" ", $name);
        //                 $first_name = $names[0];
        //                 unset($names[0]);
        //                 $last_name = implode(" ", $names);

        //                 //existing user
        //                 if($user = User::where('telephone',$telephone)->first()){
        //                     if($user->loan_status != "0"){
        //                         return response()->json(['status'=>'error','message'=>'You have an existing loan with us'],400);
        //                     }
        //                 //store salary details
        //                 foreach($salary_details as $value){
        //                     $sum += $value['amount'];
        //                     $field = [
        //                         'authcode' => $authcode,
        //                         'amount' => $value['amount'],
        //                         'paymentdate' => $value['paymentDate']
        //                     ];
        //                     RemitaPayment::create($field);
        //                 }
        //                 $loan_amount = $sum/$salary_count;
        //                 //6% interest
        //                 $repayment = ((0.2* $loan_amount) + $loan_amount );
        //                 //store loan history
        //                 foreach($loan_history as $value){
        //                     $field = [
        //                         'authcode' => $authcode,
        //                         'status' => $value['status'],
        //                         'loanprovider' => $value['loanProvider'],
        //                         'outstandingamount' => $value['outstandingAmount'],
        //                         'loandisbursementdate' => $value['loanDisbursementDate'],
        //                         'repaymentamount' => $value['repaymentAmount'],
        //                         'repaymentfreq' => $value['repaymentFreq'],
        //                         'loanamount' => $value['loanAmount'],
        //                     ];
        //                     RemitaLoanHistory::create($field);
        //                 }
        //                 $bvn = $user->bvn;
        //                 $organization = $user->place_of_work;
        //                 $borrower_id = $user->borrower_id;
        //                 $bankcode = $user->bankcode;
        //                 $accountnumber = $user->accountnumber;
        //                 $field = [
        //                             'loanid' => $loanid,
        //                             'auth_code' => $authcode,
        //                             'borrower_id' => $borrower_id,
        //                             'telephone' => $telephone,
        //                             'loan_amount' => $loan_amount,
        //                             'tenor' => $tenor,
        //                             'giro_reference' => $giro_reference ,
        //                             'offer_code' => $offer_code ,
        //                             'due_date'=> $due_date,
        //                             'repayment' => $repayment,
        //                             'bank_code' => $bankcode,
        //                             'remita_authcode' => $authcode,
        //                             'remita_customer_id'=> $customerId,
        //                             'remita_mda_name' => $organization,
        //                             'remita_name' => $name,
        //                             'remita_salary_bank_account' => $accountnumber,
        //                             'remita_salary_bank_name' => $bankcode,
        //                             'acc_no' => $accountnumber,
        //                             'bvn' => $bvn,
        //                             'place_of_work' => $organization,
        //                             'remita_status' => "1",
        //                             'type' => $event->type
        //                         ];
        //                         LoanApplication::create($field);
        //             } else{
        //                 //create new user and loan
        //                 $newuser = new User();
        //                 $newuser->accountnumber = $accountnumber;
        //                 $newuser->bankcode = $bankcode;
        //                 $newuser->telephone = $telephone;
        //                 $newuser->bvn = $bvn;
        //                 $newuser->place_of_work = $organization;
        //                 $newuser->firstname = $first_name;
        //                 $newuser->lastname = $last_name;
        //                 $newuser->email = $email;
        //                 $password = Helper::randomString(10, true);
        //                 $newuser->password = bcrypt($password);
        //                 $newuser->passcode = $password;
        //                 $newuser->loan_status = "1";
        //                 $newuser->borrower_id = $borrower_id;

        //                 //store salary details
        //                 foreach($salary_details as $value){
        //                     $sum += $value['amount'];
        //                     $field = [
        //                         'authcode' => $authcode,
        //                         'amount' => $value['amount'],
        //                         'paymentdate' => $value['paymentDate']
        //                     ];
        //                     RemitaPayment::create($field);
        //                 }

        //                 $loan_amount = $sum/$salary_count;
        //                 //6% interest
        //                 $repayment = ((0.2* $loan_amount) + $loan_amount );
        //                 //store loan history
        //                 foreach($loan_history as $value){
        //                     $field = [
        //                         'authcode' => $authcode,
        //                         'status' => $value['status'],
        //                         'loanprovider' => $value['loanProvider'],
        //                         'outstandingamount' => $value['outstandingAmount'],
        //                         'loandisbursementdate' => $value['loanDisbursementDate'],
        //                         'repaymentamount' => $value['repaymentAmount'],
        //                         'repaymentfreq' => $value['repaymentFreq'],
        //                         'loanamount' => $value['loanAmount'],
        //                     ];
        //                     RemitaLoanHistory::create($field);
        //                 }
        //                 $field = [
        //                             'loanid' => $loanid,
        //                             'auth_code' => $authcode,
        //                             'borrower_id' => $borrower_id,
        //                             'telephone' => $telephone,
        //                             'loan_amount' => $loan_amount,
        //                             'tenor' => $tenor,
        //                             'giro_reference' => $giro_reference ,
        //                             'offer_code' => $offer_code ,
        //                             'due_date'=> $due_date,
        //                             'repayment' => $repayment,
        //                             'bank_code' => $bankcode,
        //                             'remita_authcode' => $authcode,
        //                             'remita_customer_id'=> $customerId,
        //                             'remita_mda_name' => $organization,
        //                             'remita_name' => $name,
        //                             'remita_salary_bank_account' => $accountnumber,
        //                             'remita_salary_bank_name' => $bankcode,
        //                             'acc_no' => $accountnumber,
        //                             'bvn' => $bvn,
        //                             'place_of_work' => $organization,
        //                             'type' => $event->type,
        //                             'remita_status' => "1",
        //                         ];
        //                         LoanApplication::create($field);
        //                         $newuser->save();
        //             }
        //         }else{
        //             return response(['status' => 'error', 'message' => 'Error verifying Telphone number'], 400);
        //         }
        //     }else{
        //         return response(['status' => 'error', 'message' => 'Customer not found'], 400);
        //     }
        // }

        // //ippis customer
        // if($event->type == "ippis"){
        //     $telephone = $event->telephone;
        //     $ippis_number = $event->ippis_number;
        //     $loan_amount = $event->loan_amount;
        //     $tenor = $event->tenor;
        //     $email = $event->email;
        //     //if user exist
        //     if($user = User::where('telephone',$telephone)->first()){
        //         if($user->ippisnumber == $ippis_number){
        //             //verify ippis number
        //             if(!$verification = VerificationData::where('ippisnumber',$ippis_number)->first()){
        //                 return response()->json(['status'=>'error','message'=>'Customer not found on ippis'],400);
        //             }
        //             //if user has a pending loan
        //             if($user->loan_status != "0"){
        //                 return response()->json(['status'=>'error','message'=>'You have an existing loan waiting for approval'],400);
        //             }
        //             //get the data from verification
        //             $name = $verification->fullname;
        //             $names = explode(" ", $name);
        //             $first_name = $names[0];
        //             unset($names[0]);
        //             $last_name = implode(" ", $names);
        //             $bankname = $verification->bankname;
        //             $accountnumber = $verification->accountnumber;
        //             $place_of_work = $verification->organization;
        //             $netpay = $verification->netpay;
        //             //6% interest
        //             $repayment = ((0.2* $loan_amount) + $loan_amount );
        //             $bankcode = Helper::getBankCode(strtoupper($bankname));
        //             $borrower_id = $user->borrower_id;
        //             $field = [
        //                 'loanid' => $loanid,
        //                 'borrower_id' => $borrower_id,
        //                 'telephone' => $telephone,
        //                 'ippis_number'=>$ippis_number,
        //                 'loan_amount' => $loan_amount,
        //                 'tenor' => $tenor,
        //                 'giro_reference' => $giro_reference ,
        //                 'offer_code' => $offer_code ,
        //                 'due_date'=> $due_date,
        //                 'repayment' => $repayment,
        //                 'bank_code' => $bankcode,
        //                 'acc_no' => $accountnumber,
        //                 'place_of_work' => $place_of_work,
        //                 'net_pay' => $netpay,
        //                 'type' => $event->type
        //             ];
        //             //update existing user details
        //             $user->firstname = $first_name;
        //             $user->lastname = $last_name;
        //             $user->email = $email;
        //             $user->bankcode = $bankcode;
        //             $user->accountnumber = $accountnumber;
        //             $user->place_of_work = $place_of_work;
        //             $user->netpay = $netpay;
        //             $user->bankcode = $bankcode;
        //             $user->telephone = $telephone;
        //             $user->ippisnumber = $ippis_number;
        //             $user->loan_status = "1";
        //             LoanApplication::create($field);
        //             $user->save();
        //         }else{
        //             return response()->json(['status'=>'error','message'=>'ippis no do not match'],400);
        //         }
        //     }else{
        //         //if new user
        //         //verify ippis, create new user and store loan
        //         if(!$verification = VerificationData::where('ippisnumber',$ippis_number)->first()){
        //             return response()->json(['status'=>'error','message'=>'Customer not found on ippis'],400);
        //         }
        //         $name = $verification->fullname;
        //         $names = explode(" ", $name);
        //         $first_name = $names[0];
        //         unset($names[0]);
        //         $last_name = implode(" ", $names);
        //         $bankname = $verification->bankname;
        //         $accountnumber = $verification->accountnumber;
        //         $place_of_work = $verification->organization;
        //         $netpay = $verification->netpay;
        //         $repayment = ((0.2* $loan_amount) + $loan_amount );
        //         $bankcode = Helper::getBankCode(strtoupper($bankname));

        //         $field = [
        //             'loanid' => $loanid,
        //             'borrower_id' => $borrower_id,
        //             'telephone' => $telephone,
        //             'ippis_number'=>$ippis_number,
        //             'loan_amount' => $loan_amount,
        //             'tenor' => $tenor,
        //             'giro_reference' => $giro_reference ,
        //             'offer_code' => $offer_code ,
        //             'due_date'=> $due_date,
        //             'repayment' => $repayment,
        //             'bank_code' => $bankcode,
        //             'acc_no' => $accountnumber,
        //             'place_of_work' => $place_of_work,
        //             'net_pay' => $netpay,
        //             'type' => $event->type
        //         ];
        //         //create new user
        //         $user = new User();
        //         $user->email = $email;
        //         $user->borrower_id = $borrower_id;
        //         $user->firstname = $first_name;
        //         $user->lastname = $last_name;
        //         $user->bankcode = $bankcode;
        //         $user->accountnumber = $accountnumber;
        //         $user->place_of_work = $place_of_work;
        //         $user->netpay = $netpay;
        //         $user->telephone = $telephone;
        //         $user->ippisnumber = $ippis_number;
        //         $password = Helper::randomString(10, true);
        //         $user->password = bcrypt($password);
        //         $user->passcode = $password;
        //         $user->loan_status = "1";
        //         $user->save();
        //         LoanApplication::create($field);
        //     }

        // }
