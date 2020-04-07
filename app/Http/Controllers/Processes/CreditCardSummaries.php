<?php namespace App\Http\Controllers\Processes;

use Session;
use DB;
use CRUDBooster;
use DateTime;
use DateInterval;
use App\Helpers\Format;
use App\Helpers\DatetimeOperations;
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

        $res = \App\AppOperation::updateOrCreate([
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
            'detail'=> 'Período ' . strtoupper(strftime('%B %Y ', (new Datetime($period['estimated_date']))->sub(new DateInterval('P1M'))->format('U'))).
                strftime('(se abona en %B %Y)', (new Datetime($period['estimated_date']))->format('U'))
        ]) ;

        //dd($res);
    }

    public function getPeriodFromOperation($operation_date){
        
        $to = $this->getPeriodOf(new Datetime($operation_date));
        $from = $this->getPeriodOf((new Datetime($operation_date))->sub( new DateInterval('P1M') ) );
        //$from = \App\AppAccountPeriod::where([['account_id', '=', $account_id], ['closed_date', '<', $operation_date]])->orderby('settlement_date', 'DESC')->first();
        //$to = \App\AppAccountPeriod::where([['account_id', '=', $account_id], ['closed_date', '>=', $operation_date]])->orderby('settlement_date', 'ASC')->first();
        return [
            'from' => $from->closed_date,
            'to' => $to->closed_date,
            'settlement_date' => $to->settlement_date,
            'estimated_date' => $to->estimated_date
        ];
    }

    public function getPeriodFromId($id){
        $to = \App\AppAccountPeriod::find($id);
        //$from = \App\AppAccountPeriod::where([['account_id', '=', $to->account_id], ['settlement_date', '<', $to->settlement_date]])->orderby('settlement_date', 'DESC')->first();
        //Puede que no esté bien... no me gusta, debería basarse en settlement_date
        $from = $this->getFromPartOfPeriod($to);
        return [
            'from' => $from->closed_date,
            'to' => $to->closed_date,
            'settlement_date' => $to->settlement_date,
            'estimated_date' => $to->estimated_date
        ];
    }
    public function getPeriodOf($operation_date){
        
        $closed_pattern = $this->account->close_pattern;
        $closed_date = DatetimeOperations::$closed_pattern($operation_date->format('Ym'));
        //dd($closed_date->format('Y-m-d'));
        $due_pattern = $this->account->due_pattern;
        $estimated_date = DatetimeOperations::$due_pattern($closed_date);
        $settlement_date = Format::date2settlement_date($estimated_date);
        $AP = \App\AppAccountPeriod::firstOrCreate([
            'account_id' => $this->account->id,
            'settlement_date' => $settlement_date
        ],[
            'closed_date' => $closed_date->format('Y-m-d'),
            'estimated_date' => $estimated_date->format('Y-m-d'),
            'closed_amount' => 0,
            'is_checked' => 0,
            'is_paid' => 0,
            'created_at' => new DateTime(),
            'updated_at' => new DateTime()
        ]);
        return $AP;
    }
    /**
     * 
     */
    public function getFromPartOfPeriod(\App\AppAccountPeriod $to){
        $date = Format::settlement_date2date( $to->settlement_date );
        $settlement_date = Format::date2settlement_date( $date->sub( new DateInterval('P1M') ) );
        
        $closed_pattern = $this->account->close_pattern;
        $closed_date = DatetimeOperations::$closed_pattern($settlement_date);
        $due_pattern = $this->account->due_pattern;
        $estimated_date = DatetimeOperations::$due_pattern($closed_date);

        $from = \App\AppAccountPeriod::firstOrCreate([
            'account_id' => $to->account_id,
            'settlement_date' => $settlement_date
        ],[
            'closed_date' => $closed_date,
            'estimated_date' => $estimated_date,
            'closed_amount' => 0,
            'is_checked' => 0,
            'is_paid' => 0,
            'created_at' => new DateTime(),
            'updated_at' => new DateTime()
        ]);
        //dd($from);
        return $from;
    }

}