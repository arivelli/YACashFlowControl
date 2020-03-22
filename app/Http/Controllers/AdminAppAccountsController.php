<?php namespace App\Http\Controllers;

use Session;
use DB;
use CRUDBooster;
use DateTime;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use App\Helpers\Format;
use Symfony\Component\Console\Helper\Helper;

class AdminAppAccountsController extends \arivelli\crudbooster\controllers\CBController {

	public function cbInit() {

		# START CONFIGURATION DO NOT REMOVE THIS LINE
		$this->title_field = "Banco";
		$this->limit = "20";
		$this->orderby = "name,desc";
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
		$this->table = "app_accounts";
		# END CONFIGURATION DO NOT REMOVE THIS LINE

		# START COLUMNS DO NOT REMOVE THIS LINE
		$this->col = [];
		$this->col[] = ["label"=>"Nombre","name"=>"name"];
		$this->col[] = ["label"=>"Banco","name"=>"bank"];
		$this->col[] = ["label"=>"Tipo","name"=>"type","callback_php"=>'$this->getAccountType($row->type)'];
		$this->col[] = ["label"=>"Moneda","name"=>"currency"];
		$this->col[] = ["label"=>"Última Revisión","name"=>"id","callback_php"=>'$this->getLastUpdateDate($row->id)'];
		$this->col[] = ["label"=>"Saldo remoto","name"=>"id","callback_php"=>'$this->getLastUpdateAmount($row->id)'];
		$this->col[] = ["label"=>"Saldo en sistema","name"=>"id","callback_php"=>'$this->getBalanceReal($row->id, $row->name)'];
		$this->col[] = ["label"=>"Activo","name"=>"is_active","callback_php"=>'($row->is_active ==1)? "Sí" : "No"'];
		# END COLUMNS DO NOT REMOVE THIS LINE

		# START FORM DO NOT REMOVE THIS LINE
		$this->form = [];
		$this->form[] = ['label'=>'Nombre','name'=>'name','type'=>'text','validation'=>'required|min:5|max:25','width'=>'col-sm-10'];
		$this->form[] = ['label'=>'Tipo','name'=>'type','type'=>'select','validation'=>'required|integer|min:0','width'=>'col-sm-10','dataenum'=>'1|Caja de ahorro;2|Cuenta corriente;3|Efectivo;4|Tarjeta;5|Pasivo','default'=>'-- Tipo de cuenta --'];
		$this->form[] = ['label'=>'Banco','name'=>'bank','type'=>'text','width'=>'col-sm-10'];
		$this->form[] = ['label'=>'CBU','name'=>'cbu','type'=>'text','width'=>'col-sm-10'];
		$this->form[] = ['label'=>'Número','name'=>'number','type'=>'text','width'=>'col-sm-10'];
		$this->form[] = ['label'=>'Moneda','name'=>'currency','type'=>'radio','validation'=>'required|min:1|max:3','width'=>'col-sm-1','dataenum'=>'$;U$S'];
		$this->form[] = ['label'=>'Notas','name'=>'notes','type'=>'wysiwyg','width'=>'col-sm-4'];
		$this->form[] = ['label'=>'Activa?','name'=>'is_active','type'=>'radio','validation'=>'required|integer','width'=>'col-sm-1','dataenum'=>'1|Sí;0|No'];
		# END FORM DO NOT REMOVE THIS LINE

		# OLD START FORM
		//$this->form = [];
		//$this->form[] = ['label'=>'Nombre','name'=>'name','type'=>'text','validation'=>'required|min:5|max:25','width'=>'col-sm-10'];
		//$this->form[] = ['label'=>'Tipo','name'=>'type','type'=>'select','validation'=>'required|integer|min:0','width'=>'col-sm-10','dataenum'=>'1|Caja de ahorro;2|Cuenta corriente;3|Efectivo;4|Tarjeta;5|Pasivo','default'=>'-- Tipo de cuenta --'];
		//$this->form[] = ['label'=>'Banco','name'=>'bank','type'=>'text','width'=>'col-sm-10'];
		//$this->form[] = ['label'=>'CBU','name'=>'cbu','type'=>'text','width'=>'col-sm-10'];
		//$this->form[] = ['label'=>'Número','name'=>'number','type'=>'text','width'=>'col-sm-10'];
		//$this->form[] = ['label'=>'Moneda','name'=>'currency','type'=>'text','validation'=>'required|min:1|max:3','width'=>'col-sm-10'];
		//$this->form[] = ['label'=>'Notas','name'=>'notes','type'=>'wysiwyg','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
		//$this->form[] = ['label'=>'Activa?','name'=>'is_active','type'=>'radio','validation'=>'required|integer','width'=>'col-sm-10','dataenum'=>'1|si;0|no'];
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
		$this->load_js = array(
			asset('/js/accounts.js')
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

	public function getAccountType($type){
		switch ($type) { 
			case 1 : 
				$res = "Caja de ahorro"; 
				break; 
			case 2 : 
				$res = "Cuenta corriente"; 
				break; 
			case 3 : 
				$res = "Efectivo"; 
				break; 
			case 4 : 
				$res = "Tarjeta"; 
				break; 
			case 5 : 
				$res = "Pasivo"; 
				break; 
			default : 
				$res = ""; 
				break; 
		}
			return $res;
	}

	public function getLastUpdateDate($id) {
		return \App\AppBalanceAccount::where('account_id', '=', $id)->orderby('id', 'desc')->first()->created_at;
	}

	public function getLastUpdateAmount($id) {
		$amount = \App\AppBalanceAccount::where('account_id', '=', $id)->orderby('id', 'desc')->first()->amount;
		$html = "<input type='text' style='text-align:right' class='accountAmount' value='{$amount}' onchange='setLastUpdateAmount({$id}, this.value);' onfocus='this.select();'>";
		return $html;
	}

	public function getBalanceReal($id, $name) {
		$amount = \App\AppBalanceReal::where([
			['settlement_date', '=', (new Datetime())->format('Ym')],
			['grouped_by', '=', 'account_id'],
			['foreign_id', '=', $id]
		])->orderby('id', 'desc')->first()->amount;
		$filter = http_build_query([
			'status' => ['Realizados'],
			'view' => 'account_name'
		]);
		$link = '<a href="/admin/cashFlow/' . $filter . '#' . urlencode($name) . '">' . Format::int2money($amount) . '</a>';
		return $link;
	}

	public function setLastUpdateAmount(Request $request) {
		$validatedData = $request->validate([
			'account_id' => 'required|integer',
			'amount' => 'required|integer',
		]);
		$balance = new \App\AppBalanceAccount;
		$balance->account_id = $validatedData['account_id'];
		$balance->amount = $validatedData['amount'];
		$balance->save();
		return $balance;
	}		

}