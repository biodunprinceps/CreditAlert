<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanLog extends Model
{
    use HasFactory;
    protected $table = "loan_logs";
    protected $guarded = [];
}
