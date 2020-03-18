<?php namespace App\Http\Controllers;

	use Session;
	use Illuminate\Http\Request;
	use DB;
	use CRUDBooster;
	use DateTime;
	use DatePeriod;
	use DateInterval;
	use App\Http\Controllers\ManageDollarValue;
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
			
			$this->col[] = ["label"=>"Fecha Estimada","name"=>"estimated_date"];
			$this->col[] = ["label"=>"Cuenta","name"=>"account_id","join"=>"app_accounts,name"];
			$this->col[] = ["label"=>"Entrada","name"=>"entry_id","join"=>"app_entries,concept"];
			$this->col[] = ["label"=>"Detalle","name"=>"detail"];
			$this->col[] = ["label"=>"Monto","name"=>"operation_amount"];
			$this->col[] = ["label"=>"Hecho?","name"=>"is_done","callback_php"=>'($row->is_done ==1)?"si" : "no"'];
			# END COLUMNS DO NOT REMOVE THIS LINE
			
			$queryBuilderPlan = DB::table('app_plans')
			->join('aux_frequencies','app_plans.frequency','=','aux_frequencies.id')
			//->join('app_plans','app_plans.frequency','=','aux_frequencies.id')
			->selectRAW('IF( plan = 1, "Pago único", 
			CONCAT(
				  IF( plan = -1, 
					 CONCAT("Recurrente en partes ", LOWER(aux_frequencies.frequency), "es"), 
					 CONCAT("Dividido en ",plan," partes ", LOWER(aux_frequencies.frequency), "es")
					) 
			) 
		   )AS text ');
			
			$queryBuilderEntry = DB::table('app_entries')
			->select('concept AS text');
			
			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Entrada','name'=>'entry_id','type'=>'fixed','validation'=>'required|integer|min:0','width'=>'col-sm-10','queryBuilder' => $queryBuilderEntry];
			$this->form[] = ['label'=>'Plan','name'=>'plan_id','type'=>'fixed','validation'=>'required|integer|min:0','width'=>'col-sm-10', 'queryBuilder' => $queryBuilderPlan, 'queryBuilder_remote_key' => 'app_plans.id'];
			$this->form[] = ['label'=>'Tipo de Cuenta','name'=>'account_type','type'=>'hidden','validation'=>'required'];
			$this->form[] = ['label'=>'Cuenta','name'=>'account_id','type'=>'select','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'app_accounts,name','datatable_where'=>'is_active=1'];
			
			$this->form[] = ['label'=>'Detalle','name'=>'detail','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Fecha Estimada','name'=>'estimated_date','type'=>'datetime','validation'=>'required|date_format:Y-m-d H:i:s','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Monto Estimado','name'=>'estimated_amount','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			
			$this->form[] = ['label'=>'Hecho?','name'=>'is_done','type'=>'radio','validation'=>'required|integer','width'=>'col-sm-10','dataenum'=>'1|si;0|no'];
			$this->form[] = ['label'=>'Fecha de Operación','name'=>'operation_date','type'=>'datetime','validation'=>'required|date_format:Y-m-d H:i:s','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Monto de Operación','name'=>'operation_amount','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Cotización Dolar','name'=>'dollar_value','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			
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

		public function cashFlow ($settlement_date = null) {
			$now = new Datetime();
			$settlement_date = $settlement_date ? $settlement_date : $now->format('Ym');
			
			$data['settlement_date'] = $settlement_date;
			$data['settlement_year'] = substr($settlement_date,0,4);
			$data['settlement_month'] = substr($settlement_date,4);

			$data['cashFlow'] = $this->cashFlowData($settlement_date, true);
			
			$data['accounts'] = AppAccount::where('is_active', '=', 1)->orderby('name', 'ASC')->get();

			$this->cbView('cashflow',$data);
		}

		public function cashFlowData ($settlement_date = null, $is_internal = false) {
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
		public function execute_operation($id, Request $request){

			$validatedData = $request->validate([
				
				'account_id' => 'required|integer',
                'operation_date' => 'required',
				'operation_amount' => 'required|integer',
				'dollar_value' => 'required|integer',
            ]);

            /*if ($validator->fails()) {
                return redirect('post/create')
                            ->withErrors($validator)
                            ->withInput();
			}*/
			
			//print_r($validatedData);
			//DB::enableQueryLog();
			$res = \App\AppOperation::where('id', $id)
            ->update([
				'account_id' => $validatedData['account_id'],
				'operation_date' => $validatedData['operation_date'],
				'operation_amount' => $validatedData['operation_amount'],
				'dollar_value' => $validatedData['dollar_value'],
				'in_dollars' => round($validatedData['operation_amount'] / $validatedData['dollar_value'] * 100),
				'is_done' => 1
			]);
			//dd(DB::getQueryLog());
			print_r($res);
		}
	}

	