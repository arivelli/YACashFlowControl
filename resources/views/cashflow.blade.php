@extends('crudbooster::admin_template')
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
</style>
@section('content')
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


@endsection
<script type="text/javascript" src="/js/operations.js"></script>