<?php namespace App\Http\Controllers;

use Session;
use Request;
use DB;
use CRUDBooster;
use App\Helpers\Format;
use DateTime;
use DatePeriod;
use DateInterval;
use App\Http\Controllers\Processes\CreditCardSummaries;
use Illuminate\Support\Facades\Redirect;

class AdminAppAccountsPeriodsController extends \arivelli\crudbooster\controllers\CBController {

	public function cbInit() {

		# START CONFIGURATION DO NOT REMOVE THIS LINE
		$this->title_field = "id";
		$this->limit = "20";
		$this->orderby = "id,desc";
		$this->global_privilege = false;
		$this->button_table_action = true;
		$this->button_bulk_action = false;
		$this->button_action_style = "button_icon";
		$this->button_add = true;
		$this->button_edit = true;
		$this->button_delete = false;
		$this->button_detail = true;
		$this->button_show = true;
		$this->button_filter = false;
		$this->button_import = false;
		$this->button_export = false;
		$this->table = "app_accounts_periods";
		# END CONFIGURATION DO NOT REMOVE THIS LINE

		# START COLUMNS DO NOT REMOVE THIS LINE
		$this->col = [];
		$this->col[] = ["label"=>"Cuenta","name"=>"account_id","join"=>"app_accounts,name"];
		$this->col[] = ["label"=>"Fecha de cierre","name"=>"closed_date"];
		$this->col[] = ["label"=>"Vencimiento","name"=>"estimated_date"];
		
		$this->col[] = ["label"=>"Periodo","name"=>"settlement_date",'callback_php'=>'$this->settlement_date2Period($row->settlement_date);'];
		
		$this->col[] = ["label"=>"Cierre","name"=>"closed_amount", "callback_php" => '\App\Helpers\Format::int2money($row->closed_amount)'];
		$this->col[] = ["label"=>"Resumen","name"=>"closed_amount", 'callback_php'=>'$this->getOperationAmount($row)'];
		$this->col[] = ["label"=>"Confirmado?","name"=>"app_accounts_periods.is_checked","callback_php"=>'($row->is_checked ==1)? "Sí" : "No"'];
		$this->col[] = ["label"=>"Pagado?","name"=>"app_accounts_periods.is_paid","callback_php"=>'($row->is_paid ==1)? "Sí" : "No"'];
		# END COLUMNS DO NOT REMOVE THIS LINE





		$queryBuilderAccount = DB::table('app_accounts')
		->select('id', 'name AS title', 'type', 'currency')
		->orderby('type')
		->orderby('currency')
		->where('is_active', '=', '1');

		# START FORM DO NOT REMOVE THIS LINE
		$this->form = [];
		
		$this->form[] = ['label'=>'Cuenta', 'name' => 'account_id', 'type' => 'select3', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-10', 'queryBuilder' => $queryBuilderAccount, 'default' => '-- Cuenta --', 'value' => 1];
		$this->form[] = ['label'=>'Período','name'=>'settlement_date','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
		$this->form[] = ['label'=>'Fecha de cierre','name'=>'closed_date','type'=>'date','validation'=>'required|date','width'=>'col-sm-10'];
		$this->form[] = ['label'=>'Fecha de vencimiento','name'=>'estimated_date','type'=>'date','validation'=>'required|date','width'=>'col-sm-10'];
		$this->form[] = ['label'=>'Cierre','name'=>'closed_amount','type'=>'money2','validation'=>'integer|min:0','width'=>'col-sm-10'];
		$this->form[] = ['label'=>'Confirmado?','name'=>'is_checked','type'=>'radio','validation'=>'required|integer','width'=>'col-sm-1','dataenum'=>'1|Sí;0|No'];
		$this->form[] = ['label'=>'Pagado?','name'=>'is_paid','type'=>'radio','validation'=>'required|integer','width'=>'col-sm-1','dataenum'=>'1|Sí;0|No'];

		# END FORM DO NOT REMOVE THIS LINE

		# OLD START FORM
		//$this->form = [];
		//$this->form[] = ["label"=>"Account Id","name"=>"account_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"account,id"];
		//$this->form[] = ["label"=>"Settlement Date","name"=>"settlement_date","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
		//$this->form[] = ["label"=>"Closed Date","name"=>"closed_date","type"=>"date","required"=>TRUE,"validation"=>"required|date"];
		//$this->form[] = ["label"=>"Estimated Date","name"=>"estimated_date","type"=>"date","required"=>TRUE,"validation"=>"required|date"];
		# OLD END FORM

		/* 
		| ---------------------------------------------------------------------- 
		| Sub Module
		| ----------------------------------------------------------------------     
		| @label          = Label of action 
		| @path           = Path of sub module
		| @foreign_key 	  = foreign key of sub table/module
		| @button_color   = Bootstrap Class (primary,success,warning,danger)
		| @button_icon    = Font Awesome Class  
		| @parent_columns = Sparate with comma, e.g : name,created_at
		| 
		*/
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
		$this->addaction[] = [
			'label'=>'Operaciones',
			'url'=>CRUDBooster::mainpath('operations/[id]'),
			'icon'=>'fa fa-check',
			'color'=>'info'
		];
		$this->addaction[] = [
			'label'=>'Recalcular',
			'url'=>CRUDBooster::mainpath('updatePeriod/[id]'),
			'icon'=>'fa fa-check',
			'color'=>'info'
		];
		


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
		$this->load_js = array();
		
		
		
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
	public function actionButtonSelected($id_selected,$button_name) {
		//Your code here
			
	}


	/*
	| ---------------------------------------------------------------------- 
	| Hook for manipulate query of index result 
	| ---------------------------------------------------------------------- 
	| @query = current sql query 
	|
	*/
	public function hook_query_index(&$query) {
		//Your code here
		/*$query = $query
			//->join('app_accounts as A', 'app_accounts_periods.account_id', '=', 'A.id')
			->leftJoin('app_operations',function ($join) {
				$join->on('app_operations.account_id', '=', 'app_accounts_periods.account_id')
				->on('app_operations.settlement_date', '=', 'app_accounts_periods.settlement_date')
				->on('app_operations.plan_id', '=', 'app_accounts.plan_id');
			} );*/
	}

	/*
	| ---------------------------------------------------------------------- 
	| Hook for manipulate row of index table html 
	| ---------------------------------------------------------------------- 
	|
	*/    
	public function hook_row_index($column_index,&$column_value) {	        
		//Your code here
	}

	/*
	| ---------------------------------------------------------------------- 
	| Hook for manipulate data input before add data is execute
	| ---------------------------------------------------------------------- 
	| @arr
	|
	*/
	public function hook_before_add(&$postdata) {        
		//Your code here

	}

	/* 
	| ---------------------------------------------------------------------- 
	| Hook for execute command after add public static function called 
	| ---------------------------------------------------------------------- 
	| @id = last insert id
	| 
	*/
	public function hook_after_add($id) {        
		//Your code here

	}

	/* 
	| ---------------------------------------------------------------------- 
	| Hook for manipulate data input before update data is execute
	| ---------------------------------------------------------------------- 
	| @postdata = input post data 
	| @id       = current id 
	| 
	*/
	public function hook_before_edit(&$postdata,$id) {        
		//Your code here

	}

	/* 
	| ---------------------------------------------------------------------- 
	| Hook for execute command after edit public static function called
	| ----------------------------------------------------------------------     
	| @id       = current id 
	| 
	*/
	public function hook_after_edit($id) {
		//Your code here 

	}

	/* 
	| ---------------------------------------------------------------------- 
	| Hook for execute command before delete public static function called
	| ----------------------------------------------------------------------     
	| @id       = current id 
	| 
	*/
	public function hook_before_delete($id) {
		//Your code here

	}

	/* 
	| ---------------------------------------------------------------------- 
	| Hook for execute command after delete public static function called
	| ----------------------------------------------------------------------     
	| @id       = current id 
	| 
	*/
	public function hook_after_delete($id) {
		//Your code here

	}



	//By the way, you can still create your own method in here... :) 
	public function getOperationsByPeriod($id){
		$to = \App\AppAccountPeriod::find($id);
		$from = \App\AppAccountPeriod::where([['account_id', '=', $to->account_id], ['settlement_date', '<', $to->settlement_date]])->orderby('settlement_date', 'DESC')->first();
		
		$period = [
			'from' => null !== $from ? $from->closed_date : '2000-01-01',
			'to' => $to->closed_date,
			'settlement_date' => $to->settlement_date,
			'estimated_date' => $to->estimated_date
		];
		$period['extended_from'] = (new Datetime($period['from']))->sub(new DateInterval('P5D'))->format('Y-m-d');
		$period['extended_to'] = (new Datetime($period['to']))->add(new DateInterval('P5D'))->format('Y-m-d');
		
		//Redirigir a operaciones con los params del filtro
		$pre_index_html = '<h3><b>';
		$pre_index_html .= $to->account->name;
		$pre_index_html .= strtoupper(strftime(' %B %Y', Format::settlement_date2date($to->settlement_date)->format('U') ) );
		$pre_index_html .= '(' . (new Datetime($period['from']))->format('d-m-Y');
		$pre_index_html .= ' - ' . (new Datetime($period['to']))->format('d-m-Y') . ')';
		$pre_index_html .= '</b></h3>';

		$CCSummary = new CreditCardSummaries($to->account);
		
		$filter = [
			"filter_column" => [
				"app_operations.account_id" => [
					"type" => "=",
					"value" => $to->account_id
				],
				"app_operations.estimated_date" => [
					"type" => "between",
					"value" => [$period['extended_from'],$period['extended_to']],
					"sorting"=>"asc"
				],
			],
			'pre_index_html' => $pre_index_html,
			'table_row_color' => [
				['condition'=>" [estimated_date] <= '{$period['from']}'","color"=>"danger"],
				['condition'=>" [estimated_date] > '{$period['to']}'","color"=>"danger"]
			],
			'index_statistic' => [
				[
					'label'=>'Cantidad',
					'count'=>$CCSummary->getOperationsOfPeriod($period)->count(),
					'icon'=>'fa fa-check',
					'color'=>'success'
				],[
					'label'=>'Monto',
					'count'=> Format::int2money($CCSummary->summarizePeriod($period)),
					'icon'=>'fa fa-check',
					'color'=>'success'
				],[
					'label'=>'Monto en Egresos',
					'count'=> Format::int2money(\App\AppOperation::where([
						['plan_id', '=', $to->account->plan_id],
						['settlement_date', '=', $to->settlement_date]
					])->first()->estimated_amount),
					'icon'=>'fa fa-check',
					'color'=>'success'
				]
			]
		];
		return redirect('/admin/app_operations?' . http_build_query($filter) );
	}

	public function settlement_date2Period ($settlement_date){
		return Format::settlement_date2Period ($settlement_date);
	}
	public function updatePeriod($id){
		
		$CCSummary = new CreditCardSummaries( \App\AppAccountPeriod::find($id)->account );
		$period =  $CCSummary->getPeriodFromid($id);
		$CCSummary->updatePeriod($period);
		
		return Redirect::back()->with('message','Operación Actualizada !');
	}

	function getOperationAmount($row){
		$estimated_amount = \App\AppOperation::where([
			['plan_id', '=', $row->app_accounts_plan_id],
			['settlement_date', '=', $row->settlement_date]
		])->first()->estimated_amount;
		return Format::int2money($estimated_amount);
	}
}