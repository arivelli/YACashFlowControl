@extends('crudbooster::admin_template')
@push('top')
<style rel="stylesheet" type="text/css" href="https://adminlte.io/themes/AdminLTE/plugins/iCheck/flat/_all.css" />
<style rel="stylesheet" type="text/css" href="/public/css/cashflow.css" />
<style>
    #filterEntryType .btn {
        width: 150px;
    }

    #filterStatus .btn {
        width: 150px;
    }
    .right {
        text-align: right;
    }
 

// Checkbox and radio options
//
// In order to support the browser's form validation feedback, powered by the
// `required` attribute, we have to "hide" the inputs via `clip`. We cannot use
// `display: none;` or `visibility: hidden;` as that also hides the popover.
// Simply visually hiding the inputs via `opacity` would leave them clickable in
// certain cases which is prevented by using `clip` and `pointer-events`.
// This way, we ensure a DOM element is visible to position the popover from.
//
// See https://github.com/twbs/bootstrap/pull/12794 and
// https://github.com/twbs/bootstrap/pull/14559 for more information.

[data-toggle="buttons"] {
  > .btn,
  > .btn-group > .btn {
    input[type="radio"],
    input[type="checkbox"] {
      position: absolute;
      clip: rect(0, 0, 0, 0);
      pointer-events: none;
    }
  }
}
</style>
<style type="text/css">
/*MARU*/
.grouper { padding:3px; display:table; width:100% }
.group1 { float:left; margin-right:20px }
.group2 { float:left;}
.group3 { float:left; clear:left; margin: 10px 20px 0 0; }
.group4 { float:left; margin-top: 10px; }
.filterTitle { font-size: 34px; line-height: 34px; font-weight: bold; }
.filterSubtitle { font-size: 24px; line-height: 34px; }
</style>
@endpush

@section('content')

<!-- Maru Code -->
<br />
<div class="grouper">
  <div class="group1">
    <div class="btn-group">
      <button type="button" class="btn btn-default">2015</button>
      <button type="button" class="btn btn-default">2016</button>
      <button type="button" class="btn btn-default">2017</button>
      <button type="button" class="btn btn-default">2018</button>
      <button type="button" class="btn btn-default">2019</button>
      <button type="button" class="btn btn-info">2020</button>
      <button type="button" class="btn btn-default">2021</button>
      <button type="button" class="btn btn-default">2022</button>
    </div>
  </div>
  <div class="group2">
    <div class="btn-group">
      <button type="button" class="btn btn-default">ENE</button>
      <button type="button" class="btn btn-default">FEB</button>
      <button type="button" class="btn btn-info">MAR</button>
      <button type="button" class="btn btn-default">ABR</button>
      <button type="button" class="btn btn-default">MAY</button>
      <button type="button" class="btn btn-default">JUN</button>
      <button type="button" class="btn btn-default">JUL</button>
      <button type="button" class="btn btn-default">AGO</button>
      <button type="button" class="btn btn-default">SEP</button>
      <button type="button" class="btn btn-default">OCT</button>
      <button type="button" class="btn btn-default">NOV</button>
      <button type="button" class="btn btn-default">DIC</button>
    </div>
  </div>
  
  <div class="group3">
  <div class="btn-group">
    <button type="button" class="btn btn-info">Ingresos</button>
    <button type="button" class="btn btn-default">Egresos</button>
    <button type="button" class="btn btn-default">Pasivos</button>
    <button type="button" class="btn btn-default">Movimientos</button>
    <button type="button" class="btn btn-default">Ajustes</button>
  </div>
</div>
<div class="group4">
  <div class="btn-group">
    <button type="button" class="btn btn-info">Pendientes</button>
    <button type="button" class="btn btn-default">Realizados</button>
  </div>
</div>

</div>
<div style="clear:both"></div>
<br />
<div class="grouper">
  <div class="group2">
    <div class="btn-group">
      <button type="button" class="btn btn-danger">Cashflow</button>
      <button type="button" class="btn btn-default">Tipo de Entrada</button>
      <button type="button" class="btn btn-default">Cuenta</button>
      <button type="button" class="btn btn-default">Área</button>
      <button type="button" class="btn btn-default">Categoría</button>
    </div>
  </div>
</div>
<div style="clear:both"></div>
<br /><br />
<div class="filterTitle">MARZO 2020</div>
<div class="filterSubtitle">Semana: <a href="#">Tercera</a> | <a href="#">Cuarta</a></div>


<br />
<br />
<br />
<br />
<br />
<br />
<br />

<!-- End Maru Code -->




<div class='test'>
    <div class='title'><?php echo e($page_title); ?></div>
</div>

<form name="filter" id="filter">
    <div id="filterYears">
        <label><input name="year" type="radio" onclick="filterData()" value="2015"{{ ($settlement_year == '2015') ? ' checked':''}}>2015</label>
        <label><input name="year" type="radio" onclick="filterData()" value="2016"{{ ($settlement_year == '2016') ? ' checked':''}}>2016</label>
        <label><input name="year" type="radio" onclick="filterData()" value="2017"{{ ($settlement_year == '2017') ? ' checked':''}}>2017</label>
        <label><input name="year" type="radio" onclick="filterData()" value="2018"{{ ($settlement_year == '2018') ? ' checked':''}}>2018</label>
        <label><input name="year" type="radio" onclick="filterData()" value="2019"{{ ($settlement_year == '2019') ? ' checked':''}}>2019</label>
        <label><input name="year" type="radio" onclick="filterData()" value="2020"{{ ($settlement_year == '2020') ? ' checked':''}}>2020</label>
        <label><input name="year" type="radio" onclick="filterData()" value="2021"{{ ($settlement_year == '2021') ? ' checked':''}}>2021</label>
        <label><input name="year" type="radio" onclick="filterData()" value="2022"{{ ($settlement_year == '2022') ? ' checked':''}}>2022</label>
    </div>
    <div id="filterMonths">
        <label><input name="month" type="radio" onclick="filterData()" value="01"{{ ($settlement_month == '01') ? ' checked':''}}>ENE</label>
        <label><input name="month" type="radio" onclick="filterData()" value="02"{{ ($settlement_month == '02') ? ' checked':''}}>FEB</label>
        <label><input name="month" type="radio" onclick="filterData()" value="03"{{ ($settlement_month == '03') ? ' checked':''}}>MAR</label>
        <label><input name="month" type="radio" onclick="filterData()" value="04"{{ ($settlement_month == '04') ? ' checked':''}}>ABR</label>
        <label><input name="month" type="radio" onclick="filterData()" value="05"{{ ($settlement_month == '05') ? ' checked':''}}>MAY</label>
        <label><input name="month" type="radio" onclick="filterData()" value="06"{{ ($settlement_month == '06') ? ' checked':''}}>JUN</label>
        <label><input name="month" type="radio" onclick="filterData()" value="07"{{ ($settlement_month == '07') ? ' checked':''}}>JUL</label>
        <label><input name="month" type="radio" onclick="filterData()" value="08"{{ ($settlement_month == '08') ? ' checked':''}}>AGO</label>
        <label><input name="month" type="radio" onclick="filterData()" value="09"{{ ($settlement_month == '09') ? ' checked':''}}>SEP</label>
        <label><input name="month" type="radio" onclick="filterData()" value="10"{{ ($settlement_month == '10') ? ' checked':''}}>OCT</label>
        <label><input name="month" type="radio" onclick="filterData()" value="11"{{ ($settlement_month == '11') ? ' checked':''}}>NOV</label>
        <label><input name="month" type="radio" onclick="filterData()" value="12"{{ ($settlement_month == '12') ? ' checked':''}}>DIC</label>
    </div>
    <div id="filterEntryType">
        
        <label><input name="entryType" type="checkbox" onclick="filterData()" value="1" checked>Ingresos</label>
        <label><input name="entryType" type="checkbox" onclick="filterData()" value="2" checked>Egresos</label>
        <label><input name="entryType" type="checkbox" onclick="filterData()" value="3" checked>Pasivos</label>
        <label><input name="entryType" type="checkbox" onclick="filterData()" value="4" checked>Movimientos</label>
        <label><input name="entryType" type="checkbox" onclick="filterData()" value="5" checked>Ajustes</label>
    </div>
    <div id="filterStatus">
        <label><input name="status" type="checkbox" onclick="filterData()" value="Pendientes" checked>Pendientes</label>
        <label><input name="status" type="checkbox" onclick="filterData()" value="Realizados" checked>Realizados</label>
    </div>
    <div id="filterGroupBy">
        <label><input name="view" type="radio" onclick="filterData()" value="settlement_week" checked>CashFlow</label>
        <label><input name="view" type="radio" onclick="filterData()" value="entry_type">Tipo de entrada</label>
        <label><input name="view" type="radio" onclick="filterData()" value="account_name">Cuenta</label>
        <label><input name="view" type="radio" onclick="filterData()" value="area">Area</label>
        <label><input name="view" type="radio" onclick="filterData()" value="category">Categoría</label>
    </div>
</form>


<!--

-Vistas
-Opciones generales para todas las vistas
    *Año
    *Mes
    *Egresos / Ingresos / Pasivo / Movimiento / Ajuste (Selección Multiple)
    *Pendientes / realizados (Selección Multiple)

-CashFlow
-Agrupar por Cuenta
-Agrupar por Categoría

Columnas
-Fecha estimada
-Concepto de entrada
-Detalle de operación ?
-Cuenta
-Monto estimado
-Monto concretado
-Fecha de concretado
-Botón de editar
-Boton de concretar
-Boton para ver todas las operaciones de esa entrada

-->
<script type="text/javascript">
    window.cashFlow = {};
    window.cashFlow[{!!$settlement_date!!}] = {!!$cashFlow!!};
    window.cashFlow.settlement_date = {!!$settlement_date!!};
    window.onload=function(){
        filterData();
    };
</script>

<form id='form-table' method='post' action='{{CRUDBooster::mainpath("action-selected")}}'>
    <input type='hidden' name='button_name' value='' />
    <input type='hidden' name='_token' value='{{csrf_token()}}' />
    <div id="tables_group">
    </div>
</form>
<!--END FORM TABLE-->


<div id='modal-datamodal-cancel-operation' class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog large " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title"><i class='fa fa-dollar'></i> Concretar Operación: <b id="cancel-operation_concept"></b>
                </h3>
                <h4 id="cancel-operation_detail"></h4>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <div class="col-sm-12">
                    <label class="control-label col-sm-3">Fecha
                        <span class='text-danger' title='{!! trans('crudbooster.this_field_is_required') !!}'>*</span>
                    </label>
                    <!--div class="{{$col_width?:'col-sm-10'}}">
                        <input type="text" title="{{$form['label']}}" class="form-control "
                        name="cancel-operation_operation_date" id="cancel-operation_operation_date" value="">
                    </div-->
                    <div class="input-group col-sm-9">
                        <span class="input-group-addon open-datetimepicker"><a><i class='fa fa-calendar '></i></a></span>
                        <input type='text' title="Fecha" readonly class='form-control notfocus input_date cancel-operation'
                            name="cancel-operation_operation_date" id="cancel-operation_operation_date" value='{{ $col['value'] }}'/> 
                    </div>
                    </div>
                </div>

                     
                            
                    @push('bottom')
                    @if (App::getLocale() != 'en')
                        <script src="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/datepicker/locales/bootstrap-datepicker.'.App::getLocale().'.js') }}"
                                charset="UTF-8"></script>
                    @endif
                    <script type="text/javascript">
                        var lang = '{{App::getLocale()}}';
                        $(function () {
                            $('.input_date').datepicker({
                                format: 'yyyy-mm-dd',
                                @if (in_array(App::getLocale(), ['ar', 'fa']))
                                rtl: true,
                                @endif
                                language: lang
                            }).on("change", function() {
                                set_dollar_value()
                            });

                            $('.open-datetimepicker').click(function () {
                                $(this).next('.input_date').datepicker('show');
                            });

                        });

                    </script>
                    @endpush

                <div class="form-group">
                    <div class="col-sm-12">
                    <label class="control-label col-sm-3">Cuenta
                        <span class='text-danger' title='{!! trans('crudbooster.this_field_is_required') !!}'>*</span>
                    </label>
                    <div class="input-group col-sm-9">
                    <select id='cancel-operation_account_id' name='cancel-operation_account_id' class='form-control select cancel-operation'>
                        @foreach ($accounts as $account)
                            <option value='{!! $account->id !!}'>{{$account->name}}</option>
                        @endforeach
                    </select>
                    </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="col-sm-12">
                    <label class="control-label col-sm-3">Monto
                        <span class='text-danger' title='{!! trans('crudbooster.this_field_is_required') !!}'>*</span>
                    </label>
                    <div class="input-group col-sm-9">
                        <input type="text" title="{{$form['label']}}" class="form-control inputMoney cancel-operation"
                               name="cancel-operation_operation_amount" id="cancel-operation_operation_amount" value="">
                        <!-- <div class="text-danger">{!! $errors->first($name)?'<i class="fa fa-info-circle"></i> '.$errors->first($name):'' !!}</div>
                        <p class="help-block">{{ @$form['help'] }}</p> -->
                    </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                    <label class="control-label col-sm-3">Cotización Dolar
                        <span class='text-danger' title='{!! trans('crudbooster.this_field_is_required') !!}'>*</span>
                    </label>
                    <div class="input-group col-sm-9">
                        <input type="text" title="{{$form['label']}}" class="form-control cancel-operation"
                        name="cancel-operation_dollar_value" id="cancel-operation_dollar_value" value="">
                    </div>
                    </div>
                </div>
                @push('bottom')
                <script type="text/javascript">
                    $('#cancel-operation_dollar_value').priceFormat({
                        'prefix' : "$ ",
                        'centsSeparator' : ",",
                        'thousandsSeparator' : ".",
                        'centsLimit'  : 2
                    });
                </script>
                @endpush
                <input type="hidden" id="cancel-operation_operation_id" value="">
                <button class='btn btn-sm btn-default' class="close" data-dismiss="modal" >Cancelar</button>
                <button class='btn btn-sm btn-success' onclick="executeOperation( $('#cancel-operation_operation_id').val() ); return false">Concretar</button>
              </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@endsection
<script type="text/javascript" src="/js/cashFlow.js"></script>
