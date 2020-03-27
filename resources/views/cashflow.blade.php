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


@extends('cashflow_concreteOperation')

@endsection
@push('bottom')
<script type="text/javascript" src="/js/cashFlow.js"></script>
<script type="text/javascript">
  window.cashFlow = {};
  window.cashFlow.filter = {!!$filter!!};
  window.cashFlow[{!!$settlement_date!!}] = {!!$cashFlow!!};
  
  window.onload=function(){
      filterData();
  };
</script>
@endpush