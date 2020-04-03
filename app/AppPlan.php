<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppPlan extends Model
{
    protected $fillable = ['is_proccesed'];
    //    
    public function frequency()
    {
        return $this->belongsTo('App\AuxFrequency');
    }
}
