<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppAccountPeriod extends Model
{
    //
    protected $table = 'app_accounts_periods';

    public function account(){
        return $this->belongsTo('App\AppAccount');
    }
    
}
