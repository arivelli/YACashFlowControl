<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppOperation extends Model
{
    //
    protected $fillable = ['entry_id','plan_id', 'settlement_date', 'account_id', 'entry_type', 'area_id', 'category_id', 'estimated_amount', 'estimated_date', 'settlement_week', 'number', 'currency', 'is_done', 'detail'];
}
