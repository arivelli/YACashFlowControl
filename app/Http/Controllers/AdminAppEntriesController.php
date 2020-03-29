<?php

namespace App\Http\Controllers;

use App\AppPlan;
use Session;
use Illuminate\Http\Request;
use DB;
use CRUDBooster;
use DateTime;
use DatePeriod;
use DateInterval;
use App\Http\Controllers\ManageDollarValue;
use App\Helpers\Format;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Http\Request as HttpRequest;
use App\Http\Controllers\Processes\CreditCardSummaries;

class AdminAppEntriesController extends \arivelli\crudbooster\controllers\CBController
{
	private $compute_operations_flag = false;

	public function cbInit()
	{
		setlocale(LC_ALL, 'es_AR.utf8');

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
		$this->col[] = ["label" => "Tipo", "name" => "entry_type", "callback_php" => '$this->get_entry_type($row->entry_type)'];
		$this->col[] = ["label" => "Categoría", "name" => "category_id", "join" => "app_categories,category"];
		$this->col[] = ["label" => "Área", "name" => "area_id", "join" => "app_areas,area"];
		$this->col[] = ["label" => "Concepto", "name" => "concept"];
		$this->col[] = ["label" => "Moneda", "name" => "currency"];
		$this->col[] = ["label" => "Monto real", "name" => "real_amount", "callback_php" => 'number_format($row->real_amount/100,2,",",".")'];
		$this->col[] = ["label" => "Afecta capital?", "name" => "affect_capital", "callback_php" => '($row->affect_capital ==1)?"Sí" : "No"'];
		$this->col[] = ["label" => "Es extraordinario?", "name" => "is_extraordinary", "callback_php" => '($row->is_extraordinary ==1)?"Sí" : "No"'];
		$this->col[] = ["label" => "Hecho?", "name" => "is_done", "callback_php" => '($row->is_done ==1)?"Sí" : "No"'];
		# END COLUMNS DO NOT REMOVE THIS LINE
		$this->col[1]['callback_php'] = '$this->get_entry_type($row->entry_type)';

		$queryBuilder = DB::table('app_accounts')
			->select('id', 'name AS title', 'type', 'currency')
			->orderby('type')
			->orderby('currency')
			->where('is_active', '=', '1');
		$now = new Datetime();
		$columns[] = ['label' => 'Moneda', 'name' => 'currency_plan', 'type' => 'radio', 'validation' => 'required|min:1|max:255', 'width' => 'col-sm-10', 'dataenum' => '$;U$S', 'value' => '$'];
		$columns[] = ['label' => 'Tipo', 'name' => 'account_type', 'type' => 'radio', 'width' => 'col-sm-10', 'dataenum' => '1|Caja de ahorro;2|Cuenta corriente;3|Efectivo;4|Tarjeta;5|Pasivo', 'value' => '3'];
		$columns[] = ['label' => 'Cuenta', 'name' => 'account_id', 'type' => 'select3', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-10', 'queryBuilder' => $queryBuilder, 'default' => '-- Cuenta --', 'value' => 1];
		$columns[] = ['label' => 'Plan', 'name' => 'plan', 'type' => 'select', 'validation' => 'required|min:1|max:255', 'width' => 'col-sm-10', 'dataenum' => ['-1|Recurrente', '1|Pago único', '2|2 Cuotas', '3|3 Cuotas', '4|4 Cuotas', '5|5 Cuotas', '6|6 Cuotas', '7|7 Cuotas', '8|8 Cuotas', '9|9 Cuotas', '10|10 Cuotas', '11|11 Cuotas', '12|12 Cuotas', '18|18 Cuotas', '24|24 Cuotas', '36|36 Cuotas', '60|60 Cuotas', '120|120 Cuotas', '240|240 Cuotas'], 'default' => '-- Plan --', 'value' => 1];
		$columns[] = ['label' => 'Frecuencia', 'name' => 'frequency_id', 'type' => 'select', 'validation' => 'required|min:1|max:255', 'width' => 'col-sm-10', 'dataenum' => ['1|Semanal', '2|Mensual', '3|Bimestral', '4|Trimestral', '5|Cuatrimestral', '6|Semestral', '7|Anual'], 'default' => '-- Frecuencia --'];

		$columns[] = ['label' => 'Formato de Detalle', 'name' => 'detail_format', 'type' => 'select', 'validation' => 'required|min:1|max:255', 'width' => 'col-sm-10', 'dataenum' => ['1|Período desde dd/mm hasta dd/mm', '2|Cuota anualizada (Cuota NN/TT)', '3|Rango mensual (MM AA - MM AA)'], 'default' => '-- Formato de detalle --'];
		$columns[] = ['label' => 'Monto por operación', 'name' => 'amount', 'type' => 'money2', 'validation' => 'required|integer', 'width' => 'col-sm-10'];
		$columns[] = ['label' => 'Primera ejecución', 'name' => 'first_execution', 'type' => 'date', 'validation' => 'required|date_format:Y-m-d', 'width' => 'col-sm-10', 'value' => $now->format('Y-m-d'), 'help' => 'Si la fecha de primera ejecución es el día 1ro se tomará el mes completo'];

		$columns[] = ['label' => 'Fecha Estimada', 'name' => 'estimated_based_on', 'type' => 'radio', 'width' => 'col-sm-10', 'dataenum' => '1|Basada en fecha de comienzo del período;2|Basada en fecha de fin del período', 'value' => '1'];
		$columns[] = ['label' => 'Desplazamiento', 'name' => 'estimated_offset', 'type' => 'text', 'validation' => '', 'width' => 'col-sm-10', 'help' => 'ver DateInterval https://www.php.net/manual/es/class.dateinterval.php Algunos ejemplos sencillos: -|P1Y2M5D|1 es 1 año 2 meses y 5 días hábiles antes', 'value' => '-|P5D|1'];

		$columns[] = ['label' => 'Recordatorio', 'name' => 'notification_to', 'type' => 'checkbox', 'width' => 'col-sm-10', 'dataenum' => '1|Administradores;2|Dueño;3|Cliente'];
		$columns[] = ['label' => 'Anticipación', 'name' => 'notification_offset', 'type' => 'text', 'width' => 'col-sm-10', 'help' => 'ver DateInterval https://www.php.net/manual/es/class.dateinterval.php'];

		$columns[] = ['label' => 'Completa?', 'name' => 'is_completed', 'type' => 'radio', 'validation' => '', 'disabled'=>true, 'width' => 'col-sm-10', 'dataenum' => '1|si;0|no', 'value' => 1];
		$columns[] = ['label' => 'Notas', 'name' => 'notes', 'type' => 'textarea', 'width' => 'col-sm-5'];

		$columns[] = ['label' => 'Procesar operaciones?', 'name' => 'compute_operations_flag', 'type' => 'radio', 'ethereal'=>true,  'validation' => 'required|integer', 'width' => 'col-sm-10', 'dataenum' => '1|si;0|no', 'value' => 0];

		# START FORM DO NOT REMOVE THIS LINE
		$this->form = [];
		$this->form[] = ['label' => 'Fecha', 'name' => 'date', 'type' => 'date', 'validation' => 'required|date_format:Y-m-d', 'width' => 'col-sm-10'];
		$this->form[] = ['label' => 'Tipo', 'name' => 'entry_type', 'type' => 'select', 'validation' => 'required', 'width' => 'col-sm-10', 'dataenum' => '1|Ingreso;2|Egreso;3|Pasivo (y compra con tarjeta);4|Movimiento;5|Ajuste', 'default' => '-- Tipo --'];
		$this->form[] = ['label' => 'Área', 'name' => 'area_id', 'type' => 'select', 'validation' => 'required', 'width' => 'col-sm-10', 'datatable' => 'app_areas,area', 'datatable_where' => 'is_active=1', 'default' => '-- Área --'];
		$this->form[] = ['label' => 'Categoría', 'name' => 'category_id', 'type' => 'select', 'validation' => 'required', 'width' => 'col-sm-10', 'datatable' => 'app_categories,category', 'datatable_where' => 'is_active=1', 'default' => '-- Categoría --'];
		$this->form[] = ['label' => 'Concepto', 'name' => 'concept', 'type' => 'text', 'validation' => 'required|min:1|max:255', 'width' => 'col-sm-10'];
		$this->form[] = ['label' => 'Moneda', 'name' => 'currency', 'type' => 'radio', 'validation' => 'required|min:1|max:255', 'width' => 'col-sm-10', 'dataenum' => '$;U$S', 'default' => '-- Moneda --'];
		$this->form[] = ['label' => 'Monto real', 'name' => 'real_amount', 'type' => 'money2', 'validation' => 'required|integer', 'width' => 'col-sm-10'];
		$this->form[] = ['label' => 'Monto en un pago', 'name' => 'one_pay_amount', 'type' => 'money2', 'validation' => 'required|integer', 'width' => 'col-sm-10'];
		$this->form[] = ['label' => 'Cotización dolar', 'name' => 'dollar_value', 'type' => 'money2', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-10'];
		$this->form[] = ['label' => 'Afecta capital?', 'name' => 'affect_capital', 'type' => 'radio', 'validation' => 'required|integer', 'width' => 'col-sm-10', 'dataenum' => '1|si;0|no'];
		$this->form[] = ['label' => 'Es Extraordinario', 'name' => 'is_extraordinary', 'type' => 'radio', 'validation' => 'required|integer', 'width' => 'col-sm-10', 'dataenum' => '1|si;0|no'];
		$this->form[] = ['label' => 'Hecho?', 'name' => 'is_done', 'type' => 'radio', 'validation' => 'required|integer', 'width' => 'col-sm-10', 'dataenum' => '1|si;0|no'];
		$this->form[] = ['label' => 'Notas', 'name' => 'notes', 'type' => 'textarea', 'width' => 'col-sm-5'];
		$this->form[] = ['label' => 'Planes', 'name' => 'plan', 'type' => 'child2', 'width' => 'col-sm-10', 'table' => 'app_plans', 'foreign_key' => 'entry_id', 'columns' => $columns];
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
		$this->form[8]['value'] = ManageDollarValue::get_value_of();
		$this->form[9]['value'] = 0;
		$this->form[10]['value'] = 1;
		$this->form[11]['value'] = 0;


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
		$this->load_js = array(
			asset("/js/entries.js"),
			asset("/js/helpers/numbers.js"),
		);



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
	
	}

	public function hook_before_add_child($postdata, &$childPostdata)
	{
		
		if(null !== $childPostdata[0]){
			$childPostdata = $childPostdata[0];
		}
		$this->compute_operations_flag = $childPostdata['compute_operations_flag'];
		unset($childPostdata['compute_operations_flag']);
		unset($childPostdata['notification_to']);
	}

	public function hook_before_edit_child($postdata, &$childPostdata) {
		$this->hook_before_add_child($postdata, $childPostdata);
	}

	public function hook_after_add_child($id, $childId)
	{
		
		if($this->compute_operations_flag) {
			$plans = DB::table('app_plans')
				->select('*', 'app_plans.id AS plan_id', 'app_plans.plan AS plan')
				->join('app_entries', 'app_entries.id', '=', 'app_plans.entry_id')
				->where([
					['app_entries.id', '=', $id],
					['app_plans.is_proccesed', '=', 0]
				])
				->get();
	
			foreach ($plans as $plan) {
				//Get the accout
				$account = \App\AppAccount::find( $plan->account_id );
				if($account->type == 4) {
					$CCSumary = new CreditCardSummaries($account);
				}
					
				$operations = $this->compute_operations($plan);
				//print_r($operations);
				foreach ($operations as $operation) {
					DB::table('app_operations')->insert($operation);
					if($account->type == 4) {
						$CPeriod = $CCSumary->getPeriodFromOperation($operation['estimated_date']);
						$CCSumary->updatePeriod($CPeriod);
					}

				}
			}
		}
	}

	public function hook_after_edit_child($id, $childId) {
		$this->hook_after_add_child($id, $childId);
	}
	/* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after add public static function called 
	    | ---------------------------------------------------------------------- 
	    | @id = last insert id
	    | 
	    */
	public function hook_after_add($id)
	{
		

	}
	/*
	public function hook_after_add_child($entry_id)
	{

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
		$this->hook_before_add($postdata, $id);
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
		$this->hook_after_add($id);
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
	//By the way, you can still create your own method in here... :) 
	public function compute_operations($data)
	{
		setlocale(LC_ALL, 'es_AR.utf8');
		$frequency_data = [
			1 => new DateInterval('P1W'), //Semanal
			2 => new DateInterval('P1M'), //Mensual
			3 => new DateInterval('P2M'), //Bimestral
			4 => new DateInterval('P3M'), //Trimestral
			5 => new DateInterval('P4M'), //Cuatrimestral
			6 => new DateInterval('P6M'), //Semestral
			7 => new DateInterval('P1Y'), //Anual
			8 => new DateInterval('P2Y'), //Bianual
		];
		$ordinal_numbers = [
			1 => 'Primera',
			2 => 'Segunda',
			3 => 'Tercera',
			4 => 'Cuarta',
			5 => 'Quinta'
		];

		//Get Holidays
		$holidays = \App\AuxHoliday::where('date', '>', $data->first_execution)->pluck('date')->all();

		$i = 0;
		$operations = [];

		$first_execution = new DateTime($data->first_execution);
		$operation_date = new DateTime($data->first_execution);
		$now = new DateTime();

		while (true) {
			$installment = $i + 1;

			$operation['entry_id'] = $data->entry_id;
			$operation['account_id'] = $data->account_id;
			$operation['entry_type'] = $data->entry_type;
			$operation['area_id'] = $data->area_id;
			$operation['category_id'] = $data->category_id;
			$operation['plan_id'] = $data->plan_id;
			$operation['currency'] = $data->currency;
			$operation['estimated_amount'] = $data->amount;

			//Estimated date based on the begining of the period
			$estimated_based_on = clone ($operation_date);

			//Estimated date based on the end of the period
			if ($data->estimated_based_on == 2) {
				$estimated_based_on->add($frequency_data[$data->frequency]);
			}

			//Apply offset
			if ($data->estimated_offset != "") {
				$estimated_offset = explode('|', $data->estimated_offset);
				//Add or substract period
				if ($estimated_offset[0] == '+') {
					$method = 'add';
				} else {
					$method = 'sub';
				}
				//Apply the offset for correlated days
				if ($estimated_offset[2] == 0) {
					$estimated_based_on->$method(new DateInterval($estimated_offset[1]));
				} else {
					//Apply the days of offset just on working days (Only allowed for days periods)
					$days = (int) str_replace('P', '', str_replace('D', '', $estimated_offset[1]));
					for ($j = 0; $j < $days; $j++) {
						$estimated_based_on->$method(new DateInterval('P1D'));
						if ($estimated_based_on->format('N') > 5 || in_array($estimated_based_on->format('Y-m-d'), $holidays)) {
							$j--;
						}
					}
				}
			}

			$operation['estimated_date'] = $estimated_based_on->format("Y-m-d H:i:s");
			$operation['settlement_date'] = $estimated_based_on->format('Ym');
			$operation['settlement_week'] = Format::get_week_of_month($estimated_based_on);


			//If the operation was in the past is marked as done (with all the required fields)
			/*if ($operation_date->format('Ymd') <= $now->format('Ymd')) {
				$operation['is_done'] = 1;
				$operation['operation_amount'] = $data->amount;
				$operation['operation_date'] = $operation_date->format("Y-m-d H:i:s");
				$operation['dollar_value'] = ManageDollarValue::get_value_of($operation_date->format('Y-m-d'));

				if ($data->currency == '$') {
					$operation['in_dollars'] = $operation['operation_amount'] / $operation['dollar_value'] * 100;
				}
			} else {
				$operation['is_done'] = 0;
			}*/
			$operation['is_done'] = 0;

			//For recursive plan
			if ($data->plan === -1) {
				//Stop the while after generate a year of operations
				if ($first_execution->format('Ym') + 100 <= $operation['settlement_date']) {
					break;
				}

				if ($data->frequency_id === 1) {

					$week_of_month = Format::get_week_of_month($operation_date);
					$operation['detail'] = strftime("{$ordinal_numbers[$week_of_month]} semana de %B de %Y", $operation_date->getTimestamp());
				} else {
					//Calculate the end of the period
					$toDate = clone $operation_date;
					$toDate->add($frequency_data[$data->frequency_id]);


					//Apply format to the detail
					if ($data->detail_format === 1) {

						$operation['detail'] =  strftime("Período desde el %e de %B de %Y", $operation_date->getTimestamp())
							. strftime(" hasta el %e de %B de %Y", $toDate->getTimestamp());
						//Cuota anualizada (Cuota NN/TT)
					} else if ($data->detail_format === 2) {
						
						switch ($data->frequency_id) {
							case 2; //12 installments for monthly 
								$installments = 12;
								break;
							case 3; //6 installments for bimonthly 
								$installments = 6;
								break;
							case 4; //4 installments for three months
								$installments = 4;
								break;
							case 5; //3 installments for a quarter
								$installments = 3;
								break;
							case 6; //2 installments for Semestral
								$installments = 2;
								break;
						}
						$operation['detail'] = "CUOTA {$installment}/{$installments} " . $operation_date->format('Y');
						//Rango mensual (MM AA - MM AA)
					} else {
						if ($data->frequency_id === 2) {
							$operation['detail'] = strtoupper(strftime('%B %Y', $operation_date->format('U')));
						} else {
							$operation['detail'] = strtoupper(strftime('%B %Y', $operation_date->format('U'))) . ' - ' . strtoupper(strftime('%B %Y', $toDate->format('U')));
						}
					}
				}
			} elseif ($data->plan === 1) {
				$operation['detail'] = 'Pago único';
			} else {
				$operation['detail'] = "CUOTA {$installment}/{$data->plan}";
			}


			$operation['created_by'] = CRUDBooster::myId();

			$operation['number'] = $installment;
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

			$operation_date->add($frequency_data[$data->frequency_id]);
		}

		return $operations;
	}


	public function get_entry_type($type)
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

	public function preview_plan(Request $request)
	{
		$plan = new AppPlan();
		$plan->first_execution = $request->input('child-first_execution');

		$plan->entry_id = 0;
		$plan->account_id = (int) $request->input('child-account_id');
		$plan->entry_type = (int) $request->input('entry_type');
		$plan->area_id = (int) $request->input('area_id');
		$plan->category_id = (int) $request->input('category_id');
		$plan->plan = (int) $request->input('child-plan');
		$plan->currency = $request->input('child-currency_plan');
		$plan->amount = $request->input('child-amount');
		$plan->detail_format = (int) $request->input('child-detail_format');
		$plan->estimated_based_on = (int) $request->input('child-estimated_based_on');
		$plan->estimated_offset = $request->input('child-estimated_offset');
		$plan->frequency_id = (int) $request->input('child-frequency_id');

		$operations = $this->compute_operations($plan);
		$html = '<table>';

		foreach ($operations as $operation) {
			$html .= '<tr>';
			$html .= "<td>{$operation['detail']}</td>";

			$html .= '<td>' . strftime('%d-%m-%Y', (new Datetime($operation['estimated_date']))->format('U')) . '</td>';
			$html .= "<td>{$operation['estimated_amount']}</td>";
			$html .= '</tr>';
		}
		$html .= '</table>';

		echo $html;
	}

	public function testDates()
	{
		setlocale(LC_ALL, 'es_AR.UTF-8');
		$date = new DateTime('2019-01-01');
		$ordinal_numbers = [
			1 => 'Primera',
			2 => 'Segunda',
			3 => 'Tercera',
			4 => 'Cuarta',
			5 => 'Quinta'
		];
		$month = 1;
		for ($i = 0; $i < 360; $i++) {
			$week_of_month =  Format::get_week_of_month($date);
			echo strftime("{$ordinal_numbers[$week_of_month]} semana - %A %e de %B de %Y", $date->getTimestamp()) . ' - ' . $date->format('Y-m-d') . '<br>';
			$date->add(new DateInterval('P1D'));
			if ($month != $date->format('m')) {
				echo '<hr>';
			}
			$month = $date->format('m');
		}
	}
}
