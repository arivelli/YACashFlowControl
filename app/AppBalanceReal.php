<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppBalanceReal extends Model
{
    protected $table = 'app_balances_real';
    protected $fillable = ['id','settlement_date', 'grouped_by', 'foreign_id', 'amount', 'last_operation_id'];
}
