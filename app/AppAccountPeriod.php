<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppAccountPeriod extends Model
{
    protected $fillable = ['account_id', 'settlement_date', 'closed_date', 'estimated_date', 'is_checked', 'is_paid'];

    protected $table = 'app_accounts_periods';

    public function account(){
        return $this->belongsTo('App\AppAccount');
    }
    
}
