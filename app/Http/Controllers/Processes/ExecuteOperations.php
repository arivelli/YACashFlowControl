<?php namespace App\Http\Controllers\Processes;

use App\AppAccount;
use App\AppAccountPeriod;
use App\AppOperation;
use App\AppBalanceAccount;
use App\AppBalanceReal;
use App\AppBalanceInSync;
use App\Http\Controllers\ManageDollarValue;
use App\Http\Controllers\Processes\CreditCardSummaries;

class ExecuteOperations {

    /**
     * Mark the operation as done, update fields and trigger some calculations
     * 
     * @param int $id
     * @param \Illuminate\Http\Request $request
     */
    static public function run($id, \Illuminate\Http\Request $request) {
        
        $validatedData = $request->validate([
            'account_id' => 'required|integer',
            'operation_date' => 'required',
            'operation_amount' => '',
            'dollar_value' => 'required|integer',
            'notes' => ''
        ]);

        /*if ($validator->fails()) {
            return redirect('post/create')
                        ->withErrors($validator)
                        ->withInput();
        }*/
        $res['validation'] = true;
        //1) Mark the operation as done
        $operation = AppOperation::find($id);
        $operation->account_id = $validatedData['account_id'];
        $operation->operation_date = $validatedData['operation_date'];
        $operation_date_parts = explode('-', $validatedData['operation_date']);
        $operation->settlement_date = $operation_date_parts[0] . $operation_date_parts[1];
        $operation->operation_amount = (null !== $validatedData['operation_amount']) ? $validatedData['operation_amount'] : 0 ;
        $operation->dollar_value = $validatedData['dollar_value'];
        $operation->in_dollars = (null !== $validatedData['operation_amount']) ? round($validatedData['operation_amount'] / $validatedData['dollar_value'] * 100) : 0;
        $operation->is_done = 1;
        $operation->notes = $validatedData['notes'] ? $validatedData['notes'] : '';
        $operation->save();
        $res['save_operation'] = true;
        //If the operation paid a passive of creditcard mark the period as paid and paid all the passive operation on it
        
        
        if( $account = AppAccount::where('entry_id', '=', $operation->entry_id)->first() ) {
            //2) Mark the period as paid
            $accountPeriod = AppAccountPeriod::where([
                ['settlement_date', '=', $operation->settlement_date],
                ['account_id', '=', $account->id]
            ])->first();
            $accountPeriod->is_paid = 1;
            $accountPeriod->save();
            $res['save_account_period'] = true;

            //3) Mark all the passive operation of the period as paid
            $CCSummary = new CreditCardSummaries($account);
            $period = $CCSummary->getPeriodFromId($accountPeriod->id);
            $periodOperations = $CCSummary->getOperationsOfPeriod($period);
            foreach($periodOperations as $PO) {
                $PO->operation_date = $PO->estimated_date;
                $PO->operation_amount = $PO->estimated_amount;
                $PO->dollar_value = ManageDollarValue::get_value_of($PO->estimated_date);
                $PO->in_dollars =  (null !== $PO->operation_amount) ? round($PO->operation_amount / $PO->dollar_value * 100) : 0;
                $PO->is_done = 1;
                $PO->save();
            }
            $res['save_operations_of_period'] = count($periodOperations);

        }
        
        
        $res['update_balances'] = ExecuteOperations::updateBalance($operation);
        $res['accounts_balance_in_sync'] = ExecuteOperations::checkAccountBalance($operation);
        return $res;
    }

    /**
     * Update montly balances by group on app_balanbe_real table
     * 
     * @param $operation
     */
    static public function updateBalance(AppOperation $operation){
        $groups = ['settlement_week','account_id', 'entry_type', 'area_id', 'category_id'];
        foreach($groups as $group) {
            $new_amount = AppOperation::where([
                ['settlement_date', '=', $operation->settlement_date],
                [$group, '=', $operation->$group]
            ])->sum('operation_amount');
            
            \App\AppBalanceReal::updateOrCreate([
                'settlement_date' => $operation->settlement_date,
                'grouped_by' => $group,
                'foreign_id' => $operation->$group
            ],[
                'amount' => $new_amount,
                'last_operation_id' => $operation->id
            ]);
            $balance[$group] = $new_amount;
        }
        return $balance;
    }
    /**
     * If the operation date in on currently month compare amount to follow differences
     * 
     * @param $operation
     */
    static public function checkAccountBalance(AppOperation $operation){
        //Get the latest balance of the account
        $balanceAccount = AppBalanceAccount::where([
            ['account_id', '=', $operation->account_id]
        ])->orderby('id','DESC')->first();

        //if the date of the balance match with the operation date persist both amounts
        if($balanceAccount) {
            if($balanceAccount->created_at->format('Ym') == $operation->settlement_date) {
                $balanceReal = AppBalanceReal::where([
                    ['settlement_date', '=', $operation->settlement_date],
                    ['grouped_by', '=', 'account_id'],
                    ['foreign_id', '=', $operation->account_id]
                ])->first();
                    
                $balanceInSync = new AppBalanceInSync;
                $balanceInSync->account_id = $operation->account_id;
    
                $balanceInSync->operation_id = $operation->id;
                $balanceInSync->balance_real_id = $balanceReal->id;
                $balanceInSync->balance_real_amount = $balanceReal->amount;
    
                $balanceInSync->balance_account_id = $balanceAccount->id;
                $balanceInSync->balance_account_amount = $balanceAccount->amount;
    
                $balanceInSync->in_sync = ($balanceAccount->amount/100 == $balanceReal->amount/100);
                $balanceInSync->save();
                return $balanceInSync->in_sync;
            }
            return 'not executed';
        }
        return 'not executed (no balance account record)';
    }
    
}