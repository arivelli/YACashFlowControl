<?php

namespace App\Http\Controllers;

use Session;
use Request;
use DB;
use CRUDBooster;
use DateTime;
use DatePeriod;
use DateInterval;

class AdminEntriesController extends \crocodicstudio\crudbooster\controllers\CBController
{

	public function cbInit()
	{

		# START CONFIGURATION DO NOT REMOVE THIS LINE
		$this->title_field = "id";
		$this->limit = "20";
		$this->orderby = "id,desc";
		$this->global_privilege = false;
		$this->button_table_action = true;
		$this->button_bulk_action = true;
		$this->button_action_style = "button_icon";
		$this->button_add = true;
		$this->button_edit = true;
		$this->button_delete = true;
		$this->button_detail = true;
		$this->button_show = true;
		$this->button_filter = true;
		$this->button_import = false;
		$this->button_export = false;
		$this->table = "app_entries";
		# END CONFIGURATION DO NOT REMOVE THIS LINE

		# START COLUMNS DO NOT REMOVE THIS LINE
		$this->col = [];
		$this->col[] = ["label" => "Fecha", "name" => "date"];
		$this->col[] = ["label" => "Tipo", "name" => "entry_type", "callback_php" => '$this->getEntryType($row->entry_type)'];
		$this->col[] = ["label" => "Categoría", "name" => "category_id", "join" => "app_categories,category"];
		$this->col[] = ["label" => "Área", "name" => "area_id", "join" => "app_areas,area"];
		$this->col[] = ["label" => "Concepto", "name" => "concept"];
		$this->col[] = ["label" => "Moneda", "name" => "currency"];
		$this->col[] = ["label" => "Monto real", "name" => "real_amount", "callback_php" => '$row->real_amount/100'];
		$this->col[] = ["label" => "Afecta capital?", "name" => "affect_capital", "callback_php" => '($row->affect_capital ==1)?"si" : "no"'];
		$this->col[] = ["label" => "Es extraordinario?", "name" => "is_extraordinary", "callback_php" => '($row->is_extraordinary ==1)?"si" : "no"'];
		$this->col[] = ["label" => "Hecho?", "name" => "is_done", "callback_php" => '($row->is_done ==1)?"si" : "no"'];
		# END COLUMNS DO NOT REMOVE THIS LINE
		$this->col[1]['callback_php'] = '$this->getEntryType($row->entry_type)';

		$queryBuilder = DB::table('app_accounts')
			->select('id', 'name AS title', 'type', 'currency')
			->orderby('type')
			->orderby('currency')
			->where('is_active', '=', '1');
		$now = new Datetime();
		$columns[] = ['label' => 'Moneda', 'name' => 'currency_plan', 'type' => 'radio', 'validation' => 'required|min:1|max:255', 'width' => 'col-sm-10', 'dataenum' => '$;U$S', 'value' => '$'];
		$columns[] = ['label' => 'Tipo', 'name' => 'account_type', 'type' => 'radio', 'width' => 'col-sm-10', 'dataenum' => '1|Caja de ahorro;2|Cuenta corriente;3|Efectivo;4|Tarjeta;5|Pasivo', 'value' => '3'];
		$columns[] = ['label' => 'Cuenta', 'name' => 'account_id', 'type' => 'select3', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-10', 'queryBuilder' => $queryBuilder, 'default' => '-- Cuenta --', 'value' => 1];
		$columns[] = ['label' => 'Plan', 'name' => 'plan', 'type' => 'select', 'validation' => 'required|min:1|max:255', 'width' => 'col-sm-10', 'dataenum' => ['-1|Recurrente', 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 18, 24, 36, 60, 120, 240], 'default' => '-- Plan --', 'value' => 1];
		$columns[] = ['label' => 'Frecuencia', 'name' => 'frequency', 'type' => 'select', 'validation' => 'required|min:1|max:255', 'width' => 'col-sm-10', 'dataenum' => ['1|Semanal', '2|Mensual', '3|Bimestral', '4|Trimestral', '5|Cuatrimestral', '6|Semestral', '7|Anual'], 'default' => '-- Frecuencia --'];
		$columns[] = ['label' => 'Monto por operación', 'name' => 'amount', 'type' => 'money', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-10'];
		$columns[] = ['label' => 'Primera ejecución', 'name' => 'first_execution', 'type' => 'date', 'validation' => 'required|date_format:Y-m-d', 'width' => 'col-sm-10', 'value' => $now->format('Y-m-d')];
		$columns[] = ['label' => 'Completa?', 'name' => 'is_completed', 'type' => 'radio', 'validation' => 'required|integer', 'width' => 'col-sm-10', 'dataenum' => '1|si;0|no', 'value' => 1];
		$columns[] = ['label' => 'Notas', 'name' => 'notes', 'type' => 'textarea', 'width' => 'col-sm-5'];

		# START FORM DO NOT REMOVE THIS LINE
		$this->form = [];
		$this->form[] = ['label' => 'Fecha', 'name' => 'date', 'type' => 'date', 'validation' => 'required|date_format:Y-m-d', 'width' => 'col-sm-10'];
		$this->form[] = ['label' => 'Tipo', 'name' => 'entry_type', 'type' => 'select', 'validation' => 'required', 'width' => 'col-sm-10', 'dataenum' => '1|Ingreso;2|Egreso;3|Pasivo;4|Movimiento;5|Ajuste', 'default' => '-- Tipo --'];
		$this->form[] = ['label' => 'Área', 'name' => 'area_id', 'type' => 'select', 'validation' => 'required', 'width' => 'col-sm-10', 'datatable' => 'app_areas,area', 'datatable_where' => 'is_active=1', 'default' => '-- Área --'];
		$this->form[] = ['label' => 'Categoría', 'name' => 'category_id', 'type' => 'select', 'validation' => 'required', 'width' => 'col-sm-10', 'datatable' => 'app_categories,category', 'datatable_where' => 'is_active=1', 'default' => '-- Categoría --'];
		$this->form[] = ['label' => 'Concepto', 'name' => 'concept', 'type' => 'text', 'validation' => 'required|min:1|max:255', 'width' => 'col-sm-10'];
		$this->form[] = ['label' => 'Moneda', 'name' => 'currency', 'type' => 'radio', 'validation' => 'required|min:1|max:255', 'width' => 'col-sm-10', 'dataenum' => '$;U$S', 'default' => '-- Moneda --'];
		$this->form[] = ['label' => 'Monto real', 'name' => 'real_amount', 'type' => 'money', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-10'];
		$this->form[] = ['label' => 'Monto en un pago', 'name' => 'one_pay_amount', 'type' => 'money', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-10'];
		$this->form[] = ['label' => 'Cotización dolar', 'name' => 'dollar_value', 'type' => 'money', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-10'];
		$this->form[] = ['label' => 'Afecta capital?', 'name' => 'affect_capital', 'type' => 'radio', 'validation' => 'required|integer', 'width' => 'col-sm-10', 'dataenum' => '1|si;0|no'];
		$this->form[] = ['label' => 'Es Extraordinario', 'name' => 'is_extraordinary', 'type' => 'radio', 'validation' => 'required|integer', 'width' => 'col-sm-10', 'dataenum' => '1|si;0|no'];
		$this->form[] = ['label' => 'Hecho?', 'name' => 'is_done', 'type' => 'radio', 'validation' => 'required|integer', 'width' => 'col-sm-10', 'dataenum' => '1|si;0|no'];
		$this->form[] = ['label' => 'Notas', 'name' => 'notes', 'type' => 'textarea', 'width' => 'col-sm-5'];
		$this->form[] = ['label' => 'Plan', 'name' => 'plan', 'type' => 'child2', 'width' => 'col-sm-10', 'table' => 'app_plans', 'foreign_key' => 'entry_id', 'columns' => $columns];
		# END FORM DO NOT REMOVE THIS LINE


		# OLD START FORM
		//$this->form = [];
		//$this->form[] = ['label'=>'Fecha','name'=>'date','type'=>'date','validation'=>'required|date_format:Y-m-d','width'=>'col-sm-10'];
		//$this->form[] = ['label'=>'Tipo','name'=>'entry_type','type'=>'select','validation'=>'required','width'=>'col-sm-10','dataenum'=>'1|Ingreso;2|Egreso;3|Pasivo;4|Movimiento','default'=>'-- Tipo --'];
		//$this->form[] = ['label'=>'Categoría','name'=>'category_id','type'=>'select','validation'=>'required','width'=>'col-sm-10','datatable'=>'app_categories,category','datatable_where'=>'is_active=1','default'=>'-- Categoría --'];
		//$this->form[] = ['label'=>'Área','name'=>'area_id','type'=>'select','validation'=>'required','width'=>'col-sm-10','datatable'=>'app_areas,area','datatable_where'=>'is_active=1','default'=>'-- Área --'];
		//$this->form[] = ['label'=>'Concepto','name'=>'concept','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
		//$this->form[] = ['label'=>'Moneda','name'=>'currency','type'=>'select','validation'=>'required|min:1|max:255','width'=>'col-sm-10','dataenum'=>'$;U$S','default'=>'$'];
		//$this->form[] = ['label'=>'Monto real','name'=>'real_amount','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10','decimals'=>'2','dec_point'=>','];
		//$this->form[] = ['label'=>'Monto en un pago','name'=>'one_pay_amount','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10','decimals'=>'2','dec_point'=>','];
		//$this->form[] = ['label'=>'Cotización dolar','name'=>'dollar_value','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10','decimals'=>'2','dec_point'=>','];
		//$this->form[] = ['label'=>'Afecta capital?','name'=>'affect_capital','type'=>'radio','validation'=>'required|integer','width'=>'col-sm-10','dataenum'=>'1|si;0|no'];
		//$this->form[] = ['label'=>'Es Extraordinario','name'=>'is_extraordinary','type'=>'radio','validation'=>'required|integer','width'=>'col-sm-10','dataenum'=>'1|si;0|no'];
		//$this->form[] = ['label'=>'Hecho?','name'=>'is_done','type'=>'radio','validation'=>'required|integer','width'=>'col-sm-10','dataenum'=>'1|si;0|no'];
		//$this->form[] = ['label'=>'Notas','name'=>'notes','type'=>'text','width'=>'col-sm-5'];
		//$this->form[] = ['label'=>'Plan','name'=>'plan','type'=>'json','width'=>'col-sm-10'];
		# OLD END FORM


		$this->form[0]['value'] = $now->format('Y-m-d');
		$this->form[1]['value'] = 2;
		$this->form[2]['value'] = 6;
		$this->form[3]['value'] = 20;
		$this->form[5]['value'] = '$';
		$this->form[9]['value'] = 0;
		$this->form[10]['value'] = 1;
		$this->form[11]['value'] = 0;

		$data_in = "http://ws.geeklab.com.ar/dolar/get-dolar-json.php";
		$data_json = @file_get_contents($data_in);
		if (strlen($data_json) > 0) {
			$data_out = json_decode($data_json, true);
			$this->form[8]['value'] = $data_out['libre'] * 100;
		}


		$this->sub_module = array();


		/* 
	        | ---------------------------------------------------------------------- 
	        | Add More Action Button / Menu
	        | ----------------------------------------------------------------------     
	        | @label       = Label of action 
	        | @url         = Target URL, you can use field alias. e.g : [id], [name], [title], etc
	        | @icon        = Font awesome class icon. e.g : fa fa-bars
	        | @color 	   = Default is primary. (primary, warning, succecss, info)     
	        | @showIf 	   = If condition when action show. Use field alias. e.g : [id] == 1
	        | 
	        */
		$this->addaction = array();


		/* 
	        | ---------------------------------------------------------------------- 
	        | Add More Button Selected
	        | ----------------------------------------------------------------------     
	        | @label       = Label of action 
	        | @icon 	   = Icon from fontawesome
	        | @name 	   = Name of button 
	        | Then about the action, you should code at actionButtonSelected method 
	        | 
	        */
		$this->button_selected = array();


		/* 
	        | ---------------------------------------------------------------------- 
	        | Add alert message to this module at overheader
	        | ----------------------------------------------------------------------     
	        | @message = Text of message 
	        | @type    = warning,success,danger,info        
	        | 
	        */
		$this->alert        = array();



		/* 
	        | ---------------------------------------------------------------------- 
	        | Add more button to header button 
	        | ----------------------------------------------------------------------     
	        | @label = Name of button 
	        | @url   = URL Target
	        | @icon  = Icon from Awesome.
	        | 
	        */
		$this->index_button = array();



		/* 
	        | ---------------------------------------------------------------------- 
	        | Customize Table Row Color
	        | ----------------------------------------------------------------------     
	        | @condition = If condition. You may use field alias. E.g : [id] == 1
	        | @color = Default is none. You can use bootstrap success,info,warning,danger,primary.        
	        | 
	        */
		$this->table_row_color = array();


		/*
	        | ---------------------------------------------------------------------- 
	        | You may use this bellow array to add statistic at dashboard 
	        | ---------------------------------------------------------------------- 
	        | @label, @count, @icon, @color 
	        |
	        */
		$this->index_statistic = array();



		/*
	        | ---------------------------------------------------------------------- 
	        | Add javascript at body 
	        | ---------------------------------------------------------------------- 
	        | javascript code in the variable 
	        | $this->script_js = "function() { ... }";
	        |
	        */
		$this->script_js = NULL;


		/*
	        | ---------------------------------------------------------------------- 
	        | Include HTML Code before index table 
	        | ---------------------------------------------------------------------- 
	        | html code to display it before index table
	        | $this->pre_index_html = "<p>test</p>";
	        |
	        */
		$this->pre_index_html = null;



		/*
	        | ---------------------------------------------------------------------- 
	        | Include HTML Code after index table 
	        | ---------------------------------------------------------------------- 
	        | html code to display it after index table
	        | $this->post_index_html = "<p>test</p>";
	        |
	        */
		$this->post_index_html = null;



		/*
	        | ---------------------------------------------------------------------- 
	        | Include Javascript File 
	        | ---------------------------------------------------------------------- 
	        | URL of your javascript each array 
	        | $this->load_js[] = asset("myfile.js");
	        |
	        */
		$this->load_js = array(asset("/js/entries.js"));



		/*
	        | ---------------------------------------------------------------------- 
	        | Add css style at body 
	        | ---------------------------------------------------------------------- 
	        | css code in the variable 
	        | $this->style_css = ".style{....}";
	        |
	        */
		$this->style_css = NULL;



		/*
	        | ---------------------------------------------------------------------- 
	        | Include css File 
	        | ---------------------------------------------------------------------- 
	        | URL of your css each array 
	        | $this->load_css[] = asset("myfile.css");
	        |
	        */
		$this->load_css = array();
	}


	/*
	    | ---------------------------------------------------------------------- 
	    | Hook for button selected
	    | ---------------------------------------------------------------------- 
	    | @id_selected = the id selected
	    | @button_name = the name of button
	    |
	    */
	public function actionButtonSelected($id_selected, $button_name)
	{
		//Your code here

	}


	/*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate query of index result 
	    | ---------------------------------------------------------------------- 
	    | @query = current sql query 
	    |
	    */
	public function hook_query_index(&$query)
	{
		//Your code here

	}

	/*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate row of index table html 
	    | ---------------------------------------------------------------------- 
	    |
	    */
	public function hook_row_index($column_index, &$column_value)
	{
		//Your code here
	}

	/*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate data input before add data is execute
	    | ---------------------------------------------------------------------- 
	    | @arr
	    |
	    */
	public function hook_before_add(&$postdata)
	{
		//Your code here
		//unset($postdata['plan']);

	}

	public function hook_before_add_child($postdata, &$childPostdata)
	{
		//Your code here
		//get_object_vars()
		/*print_r($postdata);
print_r($childPostdata);
die();*/ }

	/* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after add public static function called 
	    | ---------------------------------------------------------------------- 
	    | @id = last insert id
	    | 
	    */
	public function hook_after_add($id)
	{
		$query = DB::table('app_plans')
			->select('*', 'app_plans.id AS plan_id', 'app_plans.plan AS plan')
			->join('app_entries', 'app_entries.id', '=', 'app_plans.entry_id')
			->where([
				['app_entries.id', '=', $id],
				['app_plans.is_proccesed', '=', 0]
			])
			->get();

		foreach ($query as $plan) {
			$operations = $this->compute_operations($plan);
			//print_r($operations);
			foreach ($operations as $operation) {
				DB::table('app_operations')->insert($operation);
			}
		}
	}
/*
	public function hook_after_add_child($entry_id)
	{
		$query = DB::table('app_plans')
			->select('*', 'app_plans.id AS plan_id', 'app_plans.plan AS plan')
			->join('app_entries', 'app_entries.id', '=', 'app_plans.entry_id')
			->where([
				['app_entries.id', '=', $entry_id],
				['app_plans.is_proccesed', '=', 0]
			])
			->get();

		foreach ($query as $plan) {
			$operations = $this->compute_operations($plan);
			print_r($operations);
			foreach ($operations as $operation) {
				DB::table('app_operations')->insert($operation);
			}
		}
	}*/
	/* 
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate data input before update data is execute
	    | ---------------------------------------------------------------------- 
	    | @postdata = input post data 
	    | @id       = current id 
	    | 
	    */
	public function hook_before_edit(&$postdata, $id)
	{
		//Your code here
		//unset($postdata['plan']);
	}

	/* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after edit public static function called
	    | ----------------------------------------------------------------------     
	    | @id       = current id 
	    | 
	    */
	public function hook_after_edit($id)
	{
		//Your code here 
		$query = DB::table('app_plans')
			->select('*', 'app_plans.id AS plan_id', 'app_plans.plan AS plan')
			->join('app_entries', 'app_entries.id', '=', 'app_plans.entry_id')
			->where([
				['app_entries.id', '=', $id],
				['app_plans.is_proccesed', '=', 0]
			])
			->get();
print_r($query);
		foreach ($query as $plan) {
			$operations = $this->compute_operations($plan);
			print_r($operations);
			foreach ($operations as $operation) {
				DB::table('app_operations')->insert($operation);
			}
		}
	}

	/* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command before delete public static function called
	    | ----------------------------------------------------------------------     
	    | @id       = current id 
	    | 
	    */
	public function hook_before_delete($id)
	{
		//Your code here

	}

	/* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after delete public static function called
	    | ----------------------------------------------------------------------     
	    | @id       = current id 
	    | 
	    */
	public function hook_after_delete($id)
	{
		//Your code here

	}

	public function compute_operations($data)
	{
		setlocale(LC_ALL, 'es_AR.UTF-8');
		/*
		$frequencyData = [
			'Semanal'		=> ['amount' => '1', 'unit' => 'week'],
			'Mensual'		=> ['amount' => '1', 'unit' => 'month'],
			'Bimestral' 	=> ['amount' => '2', 'unit' => 'month'],
			'Trimestral' 	=> ['amount' => '3', 'unit' => 'month'],
			'Cuatrimestral'	=> ['amount' => '4', 'unit' => 'month'],
			'Semestral' 	=> ['amount' => '6', 'unit' => 'month'],
			'Anual' 		=> ['amount' => '1', 'unit' => 'year'],
			'Binual' 		=> ['amount' => '2', 'unit' => 'year'],
		];*/
		$frequencyData = [
			1 => new DateInterval('P1W'), //Semanal
			2 => new DateInterval('P1M'), //Mensual
			3 => new DateInterval('P2M'), //Bimestral
			4 => new DateInterval('P3M'), //Trimestral
			5 => new DateInterval('P4M'), //Cuatrimestral
			6 => new DateInterval('P6M'), //Semestral
			7 => new DateInterval('P1Y'), //Anual
			8 => new DateInterval('P2Y'), //Bianual
		];
		$ordinalNumbers = [
			1 => 'Primera',
			2 => 'Segunda',
			3 => 'Tercera',
			4 => 'Cuarta',
			5 => 'Quinta'
		];

		$i = 0;
		$operations = [];

		$first_execution = new DateTime($data->first_execution);
		$operation_date = clone $first_execution;
		$now = new DateTime();

		while (true) {
			$cuota = $i + 1;

			$operation['entry_id'] = $data->entry_id;
			$operation['account_id'] = $data->account_id;
			$operation['plan_id'] = $data->plan_id;
			$operation['currency'] = $data->currency;
			$operation['estimated_amount'] = $data->amount;
			$operation['estimated_date'] = $operation_date->format("Y-m-d H:i:s");
			$operation['settlement_date'] = $operation_date->format('Ym');

			//If the operation was in the past is marked as done (with all the required fields)
			if ($operation_date->format('Ymd') <= $now->format('Ymd')) {
				$operation['is_done'] = 1;
				$operation['operation_amount'] = $data->amount;
				$operation['operation_date'] = $operation_date->format("Y-m-d H:i:s");

				$aux_dollar_value = DB::table('aux_dollar_value')
					->select('seller')
					->where('date', '>=', $operation_date->format('Y-m-d'))
					->orderby('date')
					->first();
				$operation['dollar_value'] = $aux_dollar_value->seller ? $aux_dollar_value->seller : 1;

				if ($data->currency == '$') {
					$operation['in_dollars'] = $operation['amount'] / ($operation['dollar_value'] / 100);
				}
			} else {
				$operation['is_done'] = 0;
			}

			//For recursive plan
			if ($data->plan === -1) {
				//Stop the while after generate a year of operations
				if ($first_execution->format('Ym') + 100 <= $operation['settlement_date']) {
					break;
				}

				if ($data->frequency === 1) {
					$dayOfMonth			= $operation_date->format("j");
					$dayOfWeek			= $operation_date->format("N");
					$firstDayOfMonth	= new DateTime($operation_date->format("Y-m-01"));
					$weekOfMonth		= ceil($dayOfMonth / 7);
					//Si el día de la semana del primer día del mes es mayor que el día de la semana de la fecha, incremento la semana del mes
					if ($firstDayOfMonth->format("N") > $dayOfWeek) {
						$weekOfMonth++;
					}
					$operation['detail'] = strftime("{$ordinalNumbers[$weekOfMonth]} semana de %B de %Y", $operation_date->getTimestamp());
				} else {
					$toDate = clone $operation_date;
					$toDate->add($frequencyData[$data->frequency]);
					$operation['detail'] =  strftime("Período desde el %e de %B de %Y", $operation_date->getTimestamp())
						. strftime(" hasta el %e de %B de %Y", $toDate->getTimestamp());
				}
			} elseif ($data->plan === 1) {
				$operation['detail'] = 'Pago único';
			} else {
				$operation['detail'] = "Cuota {$cuota}/{$data->plan}";
			}


			$operation['created_by'] = CRUDBooster::myId();

			$operation['number'] = $cuota;
			array_push($operations, $operation);
			unset($operation);
			$i++;

			//Stop when the operation number == the amount of operations 
			if ($data->plan == $i) {
				break;
			}
			if (241 == $i) {
				break;
			}

			$operation_date->add($frequencyData[$data->frequency]);
		}

		return $operations;
	}

	//By the way, you can still create your own method in here... :) 

	public function getEntryType($type)
	{
		switch ($type) {
			case 1:
				$res = "Ingreso";
				break;
			case 2:
				$res = "Egreso";
				break;
			case 3:
				$res = "Pasivo";
				break;
			case 4:
				$res = "Movimiento";
				break;
			default:
				$res = "Error";
				break;
		}
		return $res;
	}
}
