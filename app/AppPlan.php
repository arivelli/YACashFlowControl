<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppPlan extends Model
{
    //    
    public function frequency()
    {
        return $this->belongsTo('App\AuxFrequency');
    }
}
