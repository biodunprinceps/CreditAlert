<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\services\LoanServices;
use App\Http\Requests\Loan\OneLoanRequest;
use App\Http\Requests\Loan\ApplyForLoanRequest;
use App\Http\Requests\Loan\StorePassportRequest;
use App\Http\Requests\Loan\AllLoansByStatusRequest;
use App\Http\Requests\Loan\ChangeLoanStatusRequest;
use App\Http\Requests\Loan\SubmitApplicationRequest;

class LoanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin', ['except' => ['apply','submitApplication','storePassport']]);
    }

    public function apply(ApplyForLoanRequest $request)
    {
        return LoanServices::apply($request);
    }

    public function oneLoan(OneLoanRequest $request)
    {
        return LoanServices::oneLoan($request);
    }

    public function submitApplication(SubmitApplicationRequest $request)
    {
        return LoanServices::submitApplication($request);
    }

    public function storePassport(StorePassportRequest $request)
    {
        return LoanServices::storePassport($request);
    }

    public function changeLoanStatus(ChangeLoanStatusRequest $request)
    {
        return LoanServices::changeLoanStatus($request);
    }

    public static function allLoansByStatus(AllLoansByStatusRequest $request)
    {
        return LoanServices::allLoansByStatus($request);
    }
}
