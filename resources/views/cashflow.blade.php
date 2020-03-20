@extends('crudbooster::admin_template')

@section('content')
<!-- Maru Code -->
<br />
<div id="filterForm">
<div class="grouper">
  <div class="group1">
    <div class="btn-group">
      <button type="button" name="year" value="2015" class="filterField btn btn-default">2015</button>
      <button type="button" name="year" value="2016" class="filterField btn btn-default">2016</button>
      <button type="button" name="year" value="2017" class="filterField btn btn-default">2017</button>
      <button type="button" name="year" value="2018" class="filterField btn btn-default">2018</button>
      <button type="button" name="year" value="2019" class="filterField btn btn-default">2019</button>
      <button type="button" name="year" value="2020" class="filterField btn btn-default">2020</button>
      <button type="button" name="year" value="2021" class="filterField btn btn-default">2021</button>
      <button type="button" name="year" value="2022" class="filterField btn btn-default">2022</button>
    </div>
  </div>
  <div class="group2">
    <div class="btn-group">
      <button type="button" name="month" value="01" class="filterField btn btn-default">ENE</button>
      <button type="button" name="month" value="02" class="filterField btn btn-default">FEB</button>
      <button type="button" name="month" value="03" class="filterField btn btn-default">MAR</button>
      <button type="button" name="month" value="04" class="filterField btn btn-default">ABR</button>
      <button type="button" name="month" value="05" class="filterField btn btn-default">MAY</button>
      <button type="button" name="month" value="06" class="filterField btn btn-default">JUN</button>
      <button type="button" name="month" value="07" class="filterField btn btn-default">JUL</button>
      <button type="button" name="month" value="08" class="filterField btn btn-default">AGO</button>
      <button type="button" name="month" value="09" class="filterField btn btn-default">SEP</button>
      <button type="button" name="month" value="10" class="filterField btn btn-default">OCT</button>
      <button type="button" name="month" value="11" class="filterField btn btn-default">NOV</button>
      <button type="button" name="month" value="12" class="filterField btn btn-default">DIC</button>
    </div>
  </div>
  
  <div class="group3">
  <div class="btn-group">
    <button type="button" name="entryType" value="1" class="filterField btn btn-default">Ingresos</button>
    <button type="button" name="entryType" value="2" class="filterField btn btn-default">Egresos</button>
    <button type="button" name="entryType" value="3" class="filterField btn btn-default">Pasivos</button>
    <button type="button" name="entryType" value="4" class="filterField btn btn-default">Movimientos</button>
    <button type="button" name="entryType" value="5" class="filterField btn btn-default">Ajustes</button>
  </div>
</div>
<div class="group4">
  <div class="btn-group">
    <button type="button" name="status" value="Realizados" class="filterField btn btn-default">Realizados</button>
    <button type="button" name="status" value="Pendientes" class="filterField btn btn-default">Pendientes</button>
  </div>
</div>

</div>
<div style="clear:both"></div>
<br />
<div class="grouper">
  <div class="group2">
    <div class="btn-group">
      <button type="button" name="view" value="settlement_week" class="filterField btn btn-default">Cashflow</button>
      <button type="button" name="view" value="entry_type" class="filterField btn btn-default">Tipo de Entrada</button>
      <button type="button" name="view" value="account_name" class="filterField btn btn-default">Cuenta</button>
      <button type="button" name="view" value="area" class="filterField btn btn-default">Área</button>
      <button type="button" name="view" value="category" class="filterField btn btn-default">Categoría</button>
    </div>
  </div>
</div>
</div>
<div style="clear:both"></div>
<br /><br />
<div class="filterTitle">Cargando ...</div>
<div class="filterSubtitle"></div>
<!-- End Maru Code -->

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
@push('bottom')
<script type="text/javascript" src="/js/cashFlow.js"></script>
<script type="text/javascript">
  window.cashFlow = {};
  window.cashFlow[{!!$settlement_date!!}] = {!!$cashFlow!!};
  window.cashFlow.settlement_date = {!!$settlement_date!!};
  window.cashFlow.filter = {!!$filter!!};

  window.onload=function(){
      filterData();
  };
</script>
@endpush