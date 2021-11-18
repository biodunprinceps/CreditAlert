<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanApplication extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = "loan_applications";

    public function user(){
        return $this->belongsTo(User::class,'authid','authid');
    }

    public function loanHistory(){
        return $this->hasMany(RemitaLoanHistory::class,'authcode','auth_code');
    }

    public function remitapayments(){
        return $this->hasMany(RemitaPayment::class,'authcode','auth_code');
    }
}
