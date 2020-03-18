<?php 
namespace App\Http\Controllers;

use DB;
use Session;
use Request;
use App\Http\Controllers\ManageDollarValue;

class CBHook extends Controller {

	/*
	| --------------------------------------
	| Please note that you should re-login to see the session work
	| --------------------------------------
	|
	*/
	public function afterLogin() {
		
		$dollar = new ManageDollarValue();
		$dollar->update_table();
	}
}