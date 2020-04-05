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

        $summary = \App\AppOperation::where([
            ['account_id', '=', $account_id],
            ['estimated_date', '>', $period['from']],
            ['estimated_date', '<=', $period['to']]
        ])->sum('estimated_amount');

        return $summary;
    }
    public function getOperationsOfPeriod($period){
        $account_id = $this->account->id;
        $operations = \App\AppOperation::where([
            ['account_id', '=', $account_id],
            ['estimated_date', '>', $period['from']],
            ['estimated_date', '<=', $period['to']]
        ])->get();

        return $operations;
    }
    public function updatePeriod($period){
        $summary = $this->summarizePeriod($period);

        \App\AppOperation::updateOrCreate([
            'entry_id' => $this->account->entry_id,
            'plan_id'=> $this->account->plan_id,
            'settlement_date' => $period['settlement_date']
        ],[
            'account_id' => $this->account->plan->account_id,
            'entry_type' => $this->account->entry->entry_type,
            'area_id' => $this->account->entry->area_id,
            'category_id' => $this->account->entry->category_id,
            'estimated_amount' => $summary,
            'estimated_date'=> $period['estimated_date'],

            'settlement_week'=> Format::get_week_of_month( new Datetime($period['estimated_date'])),
            'number'=> (new Datetime($period['estimated_date']))->format('n'),
            'currency'=> $this->account->currency,
            
            'is_done'=> 0,
            'detail'=> strtoupper(strftime('%B %Y', (new Datetime($period['estimated_date']))->format('U'))),
            
       
        ]) ;
    }

    public function getPeriodFromOperation($operation_date){
        $account_id = $this->account->id;
        $from = \App\AppAccountPeriod::where([['account_id', '=', $account_id], ['closed_date', '<', $operation_date]])->orderby('settlement_date', 'DESC')->first();
        $to = \App\AppAccountPeriod::where([['account_id', '=', $account_id], ['closed_date', '>=', $operation_date]])->orderby('settlement_date', 'ASC')->first();
        return [
            'from' => null !== $from ? $from->closed_date : '2000-01-01',
            'to' => null !== $to ? $to->closed_date : '2100-12-31',
            'settlement_date' => $to ? $to->settlement_date : 210012,
            'estimated_date' => $to ? $to->estimated_date : '2100-12-31'
        ];
    }

    public function getPeriodFromid($id){
        $to = \App\AppAccountPeriod::find($id);
        $from = \App\AppAccountPeriod::where([['account_id', '=', $to->account_id], ['settlement_date', '<', $to->settlement_date]])->orderby('settlement_date', 'DESC')->first();
        return [
            'from' => null !== $from ? $from->closed_date : '2000-01-01',
            'to' => null !== $to ? $to->closed_date : '2100-12-31',
            'settlement_date' => $to ? $to->settlement_date : 210012,
            'estimated_date' => $to ? $to->estimated_date : '2100-12-31'
        ];
    }

}