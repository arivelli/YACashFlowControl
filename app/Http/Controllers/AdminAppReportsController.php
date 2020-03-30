<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use CRUDBooster;

class AdminAppReportsController extends \arivelli\crudbooster\controllers\CBController
{
    public function cbInit()
	{
        setlocale(LC_ALL, 'es_AR.utf8');
    }

    public function balances(){
        $balance = \App\AppBalanceReal::where([
            ['grouped_by', '=', 'entry_type']
        ])
        ->whereBetween('settlement_date', [202000, 202100])
        ->orderBy('settlement_date', 'ASC')
        ->get();

        print_r($balance->toArray());
        $data = ['balance' => $balance];
        $this->cbView('reports',$data);
    }
}
