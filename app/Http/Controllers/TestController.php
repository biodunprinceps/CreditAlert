<?php

namespace App\Http\Controllers;

use App\Models\LoanLog;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test(Request $request)
    {
        foreach($request->data as $value)
        {
            $uniquenumber = $value['Unique#'];
            $telephone = $value['Mobile'];
            $borrower_id = $value['Borrower Id'];
            $log = new LoanLog();
            $log->uniquenumber = $uniquenumber;
            $log->loandisk_borrowerid = $borrower_id;
            $log->rsptelephone = $telephone;
            $log->telephone = $telephone;
            $log->numberofloan = 1;
            $log->save();
        }

        return "done";
    }
}
