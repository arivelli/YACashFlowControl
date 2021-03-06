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
use App\Http\Controllers\Processes\PassiveSummaries;
use App\Http\Controllers\Processes\GenerateOperationsFromPlan;
use arivelli\crudbooster\helpers\CRUDBooster as HelpersCRUDBooster;
use Illuminate\Support\Facades\Log;



class AdminAppEntriesController extends \arivelli\crudbooster\controllers\CBController
{
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
		$this->col[] = ["label" => "Tipo", "name" => "entry_type", "callback_php" => '$this->get_entry_type($row->entry_type);'];
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

		//Prepare columns to plans child
		$plans[] = ['label' => 'Moneda', 'name' => 'currency_plan', 'type' => 'radio', 'validation' => 'required|min:1|max:255', 'width' => 'col-sm-10', 'dataenum' => '$;U$S', 'value' => '$'];
		$plans[] = ['label' => 'Tipo', 'name' => 'account_type', 'type' => 'radio', 'width' => 'col-sm-10', 'dataenum' => '1|Caja de ahorro;2|Cuenta corriente;3|Efectivo;4|Tarjeta;5|Pasivo', 'value' => '3'];
		$plans[] = ['label' => 'Cuenta', 'name' => 'account_id', 'type' => 'select3', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-10', 'queryBuilder' => $queryBuilder, 'default' => '-- Cuenta --', 'value' => 1];
		$plans[] = ['label' => 'Plan', 'name' => 'plan', 'type' => 'select', 'validation' => 'required|min:1|max:255', 'width' => 'col-sm-10', 'dataenum' => ['-1|Recurrente', '1|Pago único', '2|2 Cuotas', '3|3 Cuotas', '4|4 Cuotas', '5|5 Cuotas', '6|6 Cuotas', '7|7 Cuotas', '8|8 Cuotas', '9|9 Cuotas', '10|10 Cuotas', '11|11 Cuotas', '12|12 Cuotas', '18|18 Cuotas', '24|24 Cuotas', '36|36 Cuotas', '60|60 Cuotas', '120|120 Cuotas', '240|240 Cuotas'], 'default' => '-- Plan --', 'value' => 1];
		$plans[] = ['label' => 'Frecuencia', 'name' => 'frequency_id', 'type' => 'select', 'validation' => 'required|min:1|max:255', 'width' => 'col-sm-10', 'dataenum' => ['1|Semanal', '2|Mensual', '3|Bimestral', '4|Trimestral', '5|Cuatrimestral', '6|Semestral', '7|Anual'], 'default' => '-- Frecuencia --'];

		$plans[] = ['label' => 'Formato de Detalle', 'name' => 'detail_format', 'type' => 'select', 'validation' => 'required|min:1|max:255', 'width' => 'col-sm-10', 'dataenum' => ['1|Período desde dd/mm hasta dd/mm', '2|Cuota anualizada (Cuota NN/TT)', '3|Rango mensual (MM AA - MM AA)'], 'default' => '-- Formato de detalle --'];
		$plans[] = ['label' => 'Monto por operación', 'name' => 'amount', 'type' => 'money2', 'validation' => 'required|integer', 'width' => 'col-sm-10'];
		$plans[] = ['label' => 'Primera ejecución', 'name' => 'first_execution', 'type' => 'date', 'validation' => 'required|date_format:Y-m-d', 'width' => 'col-sm-10', 'value' => $now->format('Y-m-d')];

		$plans[] = ['label' => 'Fecha Estimada', 'name' => 'estimated_based_on', 'type' => 'radio', 'width' => 'col-sm-10', 'dataenum' => '1|Basada en fecha de comienzo del período;2|Basada en fecha de fin del período', 'value' => '1'];
		$plans[] = ['label' => 'Desplazamiento', 'name' => 'estimated_offset', 'type' => 'text', 'validation' => '', 'width' => 'col-sm-10', 'help' => 'ver DateInterval https://www.php.net/manual/es/class.dateinterval.php Algunos ejemplos sencillos: -|P1Y2M5D|1 es 1 año 2 meses y 5 días hábiles antes', 'value' => '-|P5D|1'];

		$plans[] = ['label' => 'Recordatorio', 'name' => 'notification_to', 'type' => 'checkbox', 'width' => 'col-sm-10', 'dataenum' => '1|Administradores;2|Dueño;3|Cliente'];
		$plans[] = ['label' => 'Anticipación', 'name' => 'notification_offset', 'type' => 'text', 'width' => 'col-sm-10', 'help' => 'ver DateInterval https://www.php.net/manual/es/class.dateinterval.php'];

		$plans[] = ['label' => 'Completa?', 'name' => 'is_completed', 'type' => 'radio', 'validation' => '', 'disabled' => true, 'width' => 'col-sm-10', 'dataenum' => '1|si;0|no', 'value' => 1];
		$plans[] = ['label' => 'Notas', 'name' => 'notes', 'type' => 'textarea', 'width' => 'col-sm-5'];

		$plans[] = ['label' => 'Procesar operaciones?', 'name' => 'is_proccesed', 'type' => 'radio', 'validation' => 'required|integer', 'width' => 'col-sm-10', 'dataenum' => '0|si;1|no', 'value' => 0];

		//Prepare columns to operations child
		$queryBuilderAccount = DB::table('app_accounts')
			->select('id', 'name AS title', 'type', 'currency')
			->orderby('type')
			->orderby('currency')
			->where('is_active', '=', '1');
		$operations[] = ['label' => 'Moneda', 'name' => 'currency', 'type' => 'hidden', 'validation' => 'required'];
		$operations[] = ['label' => 'Cuenta', 'name' => 'account_id', 'type' => 'select3', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-10', 'queryBuilder' => $queryBuilderAccount, 'default' => '-- Cuenta --', 'value' => 1];
		$operations[] = ['label' => 'Detalle', 'name' => 'detail', 'type' => 'text', 'validation' => 'required|min:1|max:255', 'width' => 'col-sm-10'];
		$operations[] = ['label' => 'Fecha Estimada', 'name' => 'estimated_date', 'type' => 'date', 'validation' => 'required|date_format:Y-m-d', 'width' => 'col-sm-10'];
		$operations[] = ['label' => 'Monto Estimado', 'name' => 'estimated_amount', 'type' => 'money2', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-10'];
		$operations[] = ['label' => 'Fecha de Operación', 'name' => 'operation_date', 'type' => 'date', 'validation' => 'date_format:Y-m-d', 'width' => 'col-sm-10'];
		$operations[] = ['label' => 'Monto de Operación', 'name' => 'operation_amount', 'type' => 'money2', 'validation' => 'integer', 'width' => 'col-sm-10'];
		$operations[] = ['label' => 'Cotización Dolar', 'name' => 'dollar_value', 'type' => 'money2', 'validation' => 'integer|min:0', 'width' => 'col-sm-10'];
		$operations[] = ['label' => 'Periodo cubierto', 'name' => 'settlement_date', 'type' => 'text', 'validation' => 'integer|min:0', 'width' => 'col-sm-10'];
		$operations[] = ['label' => 'Notas', 'name' => 'notes', 'type' => 'text', 'width' => 'col-sm-10'];
		$operations[] = ['label' => 'Hecho?', 'name' => 'is_done', 'type' => 'radio', 'validation' => 'required|integer', 'width' => 'col-sm-10', 'dataenum' => '1|Sí;0|No'];

		# START FORM DO NOT REMOVE THIS LINE
		$this->form = [];
		$this->form[0] = ['label' => 'Fecha', 'name' => 'date', 'type' => 'date', 'validation' => 'required|date_format:Y-m-d', 'width' => 'col-sm-10'];
		$this->form[1] = ['label' => 'Tipo', 'name' => 'entry_type', 'type' => 'select', 'validation' => 'required', 'width' => 'col-sm-10', 'dataenum' => '1|Ingreso;2|Egreso;3|Pasivo (y compra con tarjeta);4|Movimiento;5|Ajuste', 'default' => '-- Tipo --'];
		$this->form[2] = ['label' => 'Área', 'name' => 'area_id', 'type' => 'select', 'validation' => 'required', 'width' => 'col-sm-10', 'datatable' => 'app_areas,area', 'datatable_where' => 'is_active=1', 'default' => '-- Área --'];
		$this->form[3] = ['label' => 'Categoría', 'name' => 'category_id', 'type' => 'select', 'validation' => 'required', 'width' => 'col-sm-10', 'datatable' => 'app_categories,category', 'datatable_where' => 'is_active=1', 'default' => '-- Categoría --'];
		$this->form[4] = ['label' => 'Concepto', 'name' => 'concept', 'type' => 'text', 'validation' => 'required|min:1|max:255', 'width' => 'col-sm-10'];
		$this->form[5] = ['label' => 'Moneda', 'name' => 'currency', 'type' => 'radio', 'validation' => 'required|min:1|max:255', 'width' => 'col-sm-10', 'dataenum' => '$;U$S', 'default' => '-- Moneda --'];
		$this->form[6] = ['label' => 'Monto real', 'name' => 'real_amount', 'type' => 'money2', 'validation' => 'required|integer', 'width' => 'col-sm-10'];
		$this->form[7] = ['label' => 'Monto en un pago', 'name' => 'one_pay_amount', 'type' => 'money2', 'validation' => 'required|integer', 'width' => 'col-sm-10'];
		$this->form[8] = ['label' => 'Cotización dolar', 'name' => 'dollar_value', 'type' => 'money2', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-10'];
		$this->form[9] = ['label' => 'Afecta capital?', 'name' => 'affect_capital', 'type' => 'radio', 'validation' => 'required|integer', 'width' => 'col-sm-10', 'dataenum' => '1|si;0|no'];
		$this->form[10] = ['label' => 'Es Extraordinario', 'name' => 'is_extraordinary', 'type' => 'radio', 'validation' => 'required|integer', 'width' => 'col-sm-10', 'dataenum' => '1|si;0|no'];
		$this->form[11] = ['label' => 'Hecho?', 'name' => 'is_done', 'type' => 'radio', 'validation' => 'required|integer', 'width' => 'col-sm-10', 'dataenum' => '1|si;0|no'];
		$this->form[12] = ['label' => 'Notas', 'name' => 'notes', 'type' => 'textarea', 'width' => 'col-sm-5'];
		$this->form[13] = ['label' => 'Planes', 'name' => 'plan', 'type' => 'child2', 'width' => 'col-sm-10', 'table' => 'app_plans', 'foreign_key' => 'entry_id', 'columns' => $plans];
		//$this->form[14] = ['label' => 'Operaciones', 'name' => 'operations', 'type' => 'child2', 'width' => 'col-sm-10', 'table' => 'app_operations', 'foreign_key' => 'entry_id', 'columns' => $operations];

		# END FORM DO NOT REMOVE THIS LINE
		//$this->form[14] = ['label' => 'Operaciones', 'name' => 'operations', 'type' => 'submodule', 'width' => 'col-sm-10', 'controller' => 'AdminAppOperationsController', 'foreign_key' => 'entry_id'];
		//		$this->form[] = array('label'=>'Operaciones','controller'=>'AdminAppOperationsController', 'foreign_key'=>'entry_id');



		$entry_type = \Illuminate\Support\Facades\Request::post('entry_type');
		if (CRUDBooster::getCurrentMethod() == 'postEditSave' || CRUDBooster::getCurrentMethod() == 'postAddSave') {
			if ($entry_type  > '3') {
				unset($this->form[13]);
			}
		}


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
		$this->sub_module[] = [
			'label' => 'Operaciones',
			'path' => 'app_operations',
			'parent_columns' => 'concept,entry_type,area_id,category_id',
			'parent_columns_alias' => 'Entrada,Tipo de entrada,Area,Categoría',
			'foreign_key' => 'entry_id',
			'button_color' => 'success',
			'button_icon' => 'fa fa-bars'
		];

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
		for ($i = 0; $i < count($childPostdata); $i++) {
			unset($childPostdata[$i]['notification_to']);
		}
	}

	public function hook_before_edit_child($postdata, &$childPostdata)
	{
		$this->hook_before_add_child($postdata, $childPostdata);
	}

	public function hook_after_add_child($id, array $childrenIds)
	{
		Log::debug('1) Entries->hook_after_add_child');
		if ($id > 0) {
			Log::debug('2) Entries->hook_after_add_child with id: ' . $id);
			/*$plans = DB::table('app_plans')
				->select('*', 'app_plans.id AS plan_id', 'app_plans.plan AS plan')
				->join('app_entries', 'app_entries.id', '=', 'app_plans.entry_id')
				->where([
					['app_entries.id', '=', $id],
					['app_plans.is_proccesed', '=', 0],
				])
				->get();*/
			$plans = \App\AppPlan::where([
				['entry_id', '=', $id],
				['is_proccesed', '=', 0],
			])
			->get();
			foreach ($plans as $plan) {
				Log::debug('3) Entries->hook_after_add_child Processing plan: ' . $plan->id);
				//Get the account
				$account = \App\AppAccount::find($plan->account_id);
				Log::debug('4) Entries->hook_after_add_child with account: '. $account->id);
				if ($account->type == 4) {
					Log::debug('5)A) Entries->hook_after_add_child processing CreditCardSummaries');
					$CCSummary = new CreditCardSummaries($account);
				} else if ($account->type == 5) {
					Log::debug('5)B) Entries->hook_after_add_child processing PassiveSummaries');
					$PassiveSummary = new PassiveSummaries($account);
				}

				$operations = GenerateOperationsFromPlan::compute_operations($plan);
				//print_r($operations);
				foreach ($operations as $operation) {
					//DB::table('app_operations')->insert($operation);
					$op = \App\AppOperation::create($operation);
					Log::debug('6) Entries->hook_after_add_child created operation: '. $op->id);
					if ($account->type == 4) {
						$CPeriod = $CCSummary->getPeriodFromOperation($op);
						Log::debug('7)A) Entries->hook_after_add_child CCSummary period:');
						Log::debug(print_r($CPeriod, true));
						$CCSummary->updatePeriod($CPeriod);
					} else if ($account->type == 5) {
						$PassiveSummary->updatePeriod($op);
					}
				}
				$plan->update(['is_proccesed' => 1]);
			}
			
		}
	}

	public function hook_after_edit_child($id, array $childrenIds)
	{
		Log::debug('hook_after_edit_child');
		$this->hook_after_add_child($id, $childrenIds);
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

		$operations = GenerateOperationsFromPlan::compute_operations($plan);
		$html = '<table width="100%" style="border-top: solid 1px #E9E9E9"">';
			$html .= '<tr style="border-bottom: solid 1px #E9E9E9; margin-top:5px">';
			$html .= '<th style="padding:5px">Detalle</td>';
			$html .= '<th style="padding:5px; text-align:center">Fecha</td>';
			$html .= '<th style="padding:5px; text-align:right">Monto</td>';
			$html .= '</tr>';
		foreach ($operations as $operation) {
			$html .= '<tr style="border-bottom: solid 1px #E9E9E9; margin-top:5px">';
			$html .= "<td style='padding:5px'>{$operation['detail']}</td>";

			$html .= '<td style="padding:5px; text-align:center">' . strftime('%d-%m-%Y', (new Datetime($operation['estimated_date']))->format('U')) . '</td>';
			$html .= "<td style='padding:5px; text-align:right'>{$operation['estimated_amount']}</td>";
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
