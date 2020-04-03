<?php namespace App\Http\Controllers\Processes;

use Session;
use DB;
use CRUDBooster;
use DateTime;
use App\Helpers\Format;
use App\Http\Controllers\ManageDollarValue;

class PassiveSummaries {
    private $account;
    public function __construct(\App\AppAccount $account)
    {
        $this->account = $account;
    }
    /**
     * Create or Update the related operation for the payment
     */
    public function updatePeriod(\App\AppOperation $passiveOperation ){

        //Mark the passive operation as paid
        $passiveOperation->operation_date = $passiveOperation->estimated_date;
        $passiveOperation->operation_amount = $passiveOperation->estimated_amount;
        $passiveOperation->dollar_value = ManageDollarValue::get_value_of( $passiveOperation->estimated_date );
        if($passiveOperation->currency == '$') {
            $passiveOperation->in_dollars = (null !== $passiveOperation->operation_amount) ? round($passiveOperation->operation_amount / $passiveOperation->dollar_value * 100) : 0;
        } else {
            $passiveOperation->in_dollars = $passiveOperation->operation_amount;
        }
        $passiveOperation->is_done = 1;
        $passiveOperation->save();

        //Create or update the related payment operation
        \App\AppOperation::updateOrCreate([
            'entry_id' => $this->account->entry_id,
            'plan_id'=> $this->account->plan_id,
            'settlement_date' => $passiveOperation->settlement_date
        ],[
            'account_id' => $this->account->plan->account_id,
            'entry_type' => $this->account->entry->entry_type,
            'area_id' => $this->account->entry->area_id,
            'category_id' => $this->account->entry->category_id,
            'estimated_amount' => $passiveOperation->estimated_amount,
            'estimated_date'=> $passiveOperation->estimated_date,

            'settlement_week'=> Format::get_week_of_month( new Datetime($passiveOperation->estimated_date)),
            'number'=> $passiveOperation->number,
            'currency'=> $this->account->currency,
            
            'is_done'=> 0,
            'detail'=> $passiveOperation->detail,
       
        ]) ;
    }
    

}