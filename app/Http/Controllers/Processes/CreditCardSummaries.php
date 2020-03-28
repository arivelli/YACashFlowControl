<?php namespace App\Http\Controllers\Processes;

use Session;
use DB;
use CRUDBooster;
use DateTime;
use App\Helpers\Format;
use App\Http\Controllers\ManageDollarValue;

class CreditCardSummaries {
    private $account;
    public function __construct(\App\AppAccount $account)
    {
        $this->account = $account;
    }
    public function summarizePeriod($period){
        
        $account_id = $this->account->id;

        $summary = AppOperation::where([
            ['account_id', '=', $account_id],
            ['operation_date', '>', $period->from],
            ['operation_date', '<', $period->to]
        ])->sum('operation_amount');

        \App\AppOperation::updateOrCreate([
            'entry_id' => $this->account->entry_id,
            'plan_id'=> $this->account->plan_id,
            'settlement_date' => $period['settlement_date'],
            'account_id' => $this->account->id
        ],[
            
            'entry_type' => $this->account->entry->entry_type,
            'area_id' => $this->account->entry->area_id,
            'category_id' => $this->account->entry->category_id,
            'estimated_amount' => $summary,
            'estimated_date'=> $period['estimated_date'],

            'settlement_week'=> Format::get_week_of_month($period['estimated_date']),
            'plan_id'=> $this->account->plan_id,
            'number'=> $period['estimated_date']->format('n'),
            'currency'=> $this->account->currency,
            
            'is_done'=> 0,
            'detail'=> strtoupper(strftime('%B %Y', $period['estimated_date']->format('U'))),
            
       
        ]) ;
    }

    public function getPeriodFromOperation($operation_date){
        $account_id = $this->account->id;
        $from = \App\AppAccountPeriod::where([['account_id', '=', $account_id], ['closed_date', '<', $operation_date]])->orderby('settlement_date', 'DESC')->first();
        $to = \App\AppAccountPeriod::where([['account_id', '=', $account_id], ['closed_date', '>=', $operation_date]])->orderby('settlement_date', 'ASC')->first();
        return [
            'from' => $from->closed_date,
            'to' => $to->closed_date,
            'settlement_date' => $to->settlement_date,
            'estimated_date' => $to->estimated_date,
        ];
    }

}