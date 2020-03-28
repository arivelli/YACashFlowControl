<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppAccount extends Model
{
    //
    public function entry()
    {
        return $this->belongsTo('App\AppEntry');
    }
    
    public function plan()
    {
        return $this->belongsTo('App\AppPlan');
    }
}
