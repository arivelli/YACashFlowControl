<?php namespace App\Http\Controllers;

	use Session;
	//use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Request;
	use DB;
	use CRUDBooster;
	use DateTime;
	use DatePeriod;
	use DateInterval;
	use App\Http\Controllers\ManageDollarValue;
	use Illuminate\Foundation\Validation\ValidatesRequests;
	use App\AppAccount;
	use App\AppOperation;

	class AdminAppOperationsController extends \arivelli\crudbooster\controllers\CBController {

	    public function cbInit() {
			
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
			$this->table = "app_operations";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			
			$this->col[] = ["label"=>"Entrada","name"=>"entry_id","join"=>"app_entries,concept"];
			$this->col[] = ["label"=>"Plan","name"=>"plan_id","join"=>"app_plans,plan"];

			$this->col[] = ["label"=>"Fecha Estimada","name"=>"estimated_date"];
			$this->col[] = ["label"=>"Fecha Operación","name"=>"operation_date"];
			$this->col[] = ["label"=>"Cuenta","name"=>"account_id","join"=>"app_accounts,name"];
			
			$this->col[] = ["label"=>"Detalle","name"=>"detail"];
			$this->col[] = ["label"=>"Moneda","name"=>"currency"];
			$this->col[] = ["label"=>"Monto Estimado","name"=>"estimated_amount", "callback_php" => 'number_format($row->estimated_amount/100,2,",",".")'];
			$this->col[] = ["label"=>"Monto Operación","name"=>"operation_amount", "callback_php" => 'number_format($row->operation_amount/100,2,",",".")'];
			$this->col[] = ["label"=>"Hecho?","name"=>"is_done","callback_php"=>'($row->is_done ==1)? "Sí" : "No"'];
			# END COLUMNS DO NOT REMOVE THIS LINE
			
			$queryBuilderPlan = DB::table('app_plans')
			->join('aux_frequencies','app_plans.frequency_id','=','aux_frequencies.id')
			//->join('app_plans','app_plans.frequency_id','=','aux_frequencies.id')
			->selectRAW('IF( app_plans.plan = 1, "Única operación", 
			CONCAT(
				  IF( app_plans.plan = -1, 
					 CONCAT("Recurrente (", aux_frequencies.frequency, ")"), 
					 CONCAT("Dividido en ",app_plans.plan," partes ", aux_frequencies.frequency, "es")
					) 
			) 
		   ) AS text ');

			
			$queryBuilderEntry = DB::table('app_entries')
			->select('concept AS text');

			$queryBuilderAccount = DB::table('app_accounts')
			->select('id', 'name AS title', 'type', 'currency')
			->orderby('type')
			->orderby('currency')
			->where('is_active', '=', '1');


			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Entrada','name'=>'entry_id','type'=>'fixed','validation'=>'integer|min:0','width'=>'col-sm-10','queryBuilder' => $queryBuilderEntry];
			$this->form[] = ['label'=>'Plan','name'=>'plan_id','type'=>'fixed','validation'=>'integer|min:0','width'=>'col-sm-10', 'queryBuilder' => $queryBuilderPlan, 'queryBuilder_remote_key' => 'app_plans.id'];
			$this->form[] = ['label'=>'Moneda','name'=>'currency','type'=>'hidden','validation'=>'required'];
			$this->form[] = ['label'=>'Cuenta', 'name' => 'account_id', 'type' => 'select3', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-10', 'queryBuilder' => $queryBuilderAccount, 'default' => '-- Cuenta --', 'value' => 1];
			$this->form[] = ['label'=>'Detalle','name'=>'detail','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Fecha Estimada','name'=>'estimated_date','type' => 'date', 'validation' => 'required|date_format:Y-m-d','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Monto Estimado','name'=>'estimated_amount','type'=>'money2','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			
			$this->form[] = ['label'=>'Hecho?','name'=>'is_done','type'=>'radio','validation'=>'required|integer','width'=>'col-sm-10','dataenum'=>'1|Sí;0|No'];
			$this->form[] = ['label'=>'Fecha de Operación','name'=>'operation_date','type' => 'date', 'validation' => 'date_format:Y-m-d','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Monto de Operación','name'=>'operation_amount','type'=>'money2','validation'=>'integer','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Cotización Dolar','name'=>'dollar_value','type'=>'money2','validation'=>'integer|min:0','width'=>'col-sm-10'];
			
			$this->form[] = ['label'=>'Periodo cubierto','name'=>'settlement_date','type'=>'text','validation'=>'integer|min:0','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Notas','name'=>'notes','type'=>'text','width'=>'col-sm-10'];
			# END FORM DO NOT REMOVE THIS LINE

			# OLD START FORM
			//$this->form = [];
			//$this->form[] = ['label'=>'Plan','name'=>'plan_id','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Tipo de Cuenta','name'=>'account_type','type'=>'select','validation'=>'required','width'=>'col-sm-9','datatable'=>'app_accounts,name','datatable_where'=>'is_active=1'];
			//$this->form[] = ['label'=>'Cuenta','name'=>'account_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Entrada','name'=>'entry_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Detalle','name'=>'detail','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Fecha Estimada','name'=>'estimated_date','type'=>'datetime','validation'=>'required|date_format:Y-m-d H:i:s','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Monto Estimado','name'=>'estimated_amount','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Fecha','name'=>'operation_date','type'=>'datetime','validation'=>'required|date_format:Y-m-d H:i:s','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Monto','name'=>'operation_amount','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Cotización Dolar','name'=>'dollar_value','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Hecho?','name'=>'is_done','type'=>'radio','validation'=>'required|integer','width'=>'col-sm-10','dataenum'=>'1|si;0|no'];
			//$this->form[] = ['label'=>'Periodo cubierto','name'=>'settlement_date','type'=>'text','validation'=>'integer|min:0','width'=>'col-sm-10'];
			//$this->form[] = ['label'=>'Notas','name'=>'notes','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
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
			if(is_array(Request::get('table_row_color'))) {
				$this->table_row_color = Request::get('table_row_color');
			}
			

	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | You may use this bellow array to add statistic at dashboard 
	        | ---------------------------------------------------------------------- 
	        | @label, @count, @icon, @color 
	        |
	        */
			$this->index_statistic = array();
			if(is_array(Request::get('index_statistic'))) {
				$this->index_statistic = Request::get('index_statistic');
			}
			

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
	        if(null !== Request::get('pre_index_html')) {
				$this->pre_index_html = Request::get('pre_index_html');
			}
	        
	        
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
			asset("/js/operations.js")
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
	        $this->load_css = array(
				//'https://adminlte.io/themes/AdminLTE/plugins/iCheck/flat/_all.css', 
				//'https://adminlte.io/themes/AdminLTE/plugins/iCheck/all.css',
				asset("/css/cashFlow.css")
			);
	        
	        
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
			if($postdata['is_done'] == true) {
				$this->operation = $postdata;
			}
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
			if(null !== $this->operation){
				$this->execute_operation($id, $this->operation);
			}
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

		public function cashFlow ($filter = null) {
			$data = [];
			$data['filter'] = [
				'year' => (new Datetime())->format('Y'),
				'month' => (new Datetime())->format('m'),
				'entryType' => [1,2,3,4,5],
				'status' => ['Pendientes', 'Realizados'],
				'view' => 'settlement_week',
				'settlementDate' => (new Datetime())->format('Ym')
			];
			//Prepare the filter
			if(null !== $filter){
				$localFilter = [];
				parse_str($filter, $localFilter );
				if(null !== $localFilter['year']){
					$data['filter']['year'] = $localFilter['year'];
					$data['filter']['settlementDate'] = $localFilter['year'] . $localFilter['month'];
				}
				if(null !== $localFilter['month']){
					$data['filter']['month'] = $localFilter['month'];
					$data['filter']['settlementDate'] = $localFilter['year'] . $localFilter['month'];
				}
				if(null !== $localFilter['entryType']){
					$data['filter']['entryType'] = array_map('intval', $localFilter['entryType']);
				}
				if(null !== $localFilter['status']){
					$data['filter']['status'] = $localFilter['status'];
				}
				if(null !== $localFilter['view']){
					$data['filter']['view'] = $localFilter['view'];
				}
			} 
			$data['settlement_date'] = $data['filter']['settlementDate'];

			//Get the data
			$data['cashFlow'] = $this->cashFlowData($data['settlement_date'], true);

			//Get account list
			$data['accounts'] = AppAccount::where('is_active', '=', 1)->orderby('name', 'ASC')->get();

			$data['page_title'] = 'CashFlow';
			$data['page_icon'] = 'fa fa-dollar';
			//$data['module'] = $this;

			$data['filter'] = json_encode($data['filter']);

			$this->cbView('cashflow',$data);
		}

		public function cashFlowData (int $settlement_date = null, bool $is_internal = false) {
			$now = new Datetime();
			$settlement_date = $settlement_date ? $settlement_date : $now->format('Ym');
			
			$query = DB::table('app_operations')
			->join('app_entries', 'app_operations.entry_id','=','app_entries.id')
			->join('app_accounts', 'app_operations.account_id','=','app_accounts.id')
			->leftjoin('app_categories', 'app_entries.category_id','=','app_categories.id')
			->leftjoin('app_areas', 'app_entries.area_id','=','app_areas.id')
			->select(
				'app_entries.id AS entry_id', 'app_entries.entry_type AS entry_type', 'app_entries.concept AS concept',
				'app_entries.area_id AS area_id',
				'app_areas.area AS area',
				'app_accounts.id AS account_id', 'app_accounts.name AS account_name',
				'app_categories.id AS category_id', 'app_categories.category AS category', 
				'app_operations.id as operation_id', 'app_operations.is_done AS is_done', 'app_operations.estimated_date AS estimated_date', 
				'app_operations.operation_date AS operation_date', 'app_operations.settlement_date AS settlement_date',
				'app_operations.settlement_week AS settlement_week', 'app_operations.detail AS detail', 'app_operations.currency AS currency',
				'app_operations.estimated_amount AS estimated_amount', 'app_operations.operation_amount AS operation_amount'
				)
			->where('settlement_date', '=', $settlement_date)
			->orderby('app_operations.estimated_date')
			->get();
			if($is_internal) {
				return json_encode($query);
			} else {
				header('Content-Type: application/json');	
				echo json_encode($query);
				die();
			}
		}

		/**
		 * Mark the operation as done, update fields and trigger some calculations
		 * 
		 * 
		 */
		public function execute_operation($id, \Illuminate\Http\Request $request) {
			
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
			
			
			$operation = \App\AppOperation::find($id);
			
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

			//dd(DB::getQueryLog());
			
			$this->updateBalance($operation);
			$this->checkAccountBalance($operation);

		}
		public function updateBalance(AppOperation $operation){
			$groups = ['settlement_week','account_id', 'entry_type', 'area_id', 'category_id'];
			foreach($groups as $group) {
				$new_amount = \App\AppOperation::where([
					['settlement_date', '=', $operation->settlement_date],
					[$group, '=', $operation->$group]
				])->sum('operation_amount');
				
				$balance = \App\AppBalanceReal::updateOrCreate([
					'settlement_date' => $operation->settlement_date,
					'grouped_by' => $group,
					'foreign_id' => $operation->$group
				],[
					'amount' => $new_amount,
					'last_operation_id' => $operation->id
				]);
			}
			
			
		}
/**
 * If the operation date in on currently month compare amount to follow differences
 * 
 * @param $operation
 */
		public function checkAccountBalance(AppOperation $operation){
			//Get the latest balance of the account
			$balanceAccount = \App\AppBalanceAccount::where([
				['account_id', '=', $operation->account_id]
			])->orderby('id','DESC')->first();

			//if the date of the balance match with the operation date persist both amounts
			if($balanceAccount->created_at->format('Ym') == $operation->settlement_date) {
				$balanceReal = \App\AppBalanceReal::where([
					['settlement_date', '=', $operation->settlement_date],
					['grouped_by', '=', 'account_id'],
					['foreign_id', '=', $operation->account_id]
				])->first();
					print_r([ $balanceReal->id ]);
				$balanceInSync = new \App\AppBalanceInSync;
				$balanceInSync->account_id = $operation->account_id;

				$balanceInSync->operation_id = $operation->id;
				$balanceInSync->balance_real_id = $balanceReal->id;
				$balanceInSync->balance_real_amount = $balanceReal->amount;

				$balanceInSync->balance_account_id = $balanceAccount->id;
				$balanceInSync->balance_account_amount = $balanceAccount->amount;

				$balanceInSync->in_sync = ($balanceAccount->amount/100 == $balanceReal->amount/100);
				$balanceInSync->save();
			}
		}
	}

	