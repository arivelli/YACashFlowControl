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

    function filterData() {
        //Get the filter from the form
        let filter = $('#filter').serializeArray();
        
        //define the newFilter variables
        let newFilter = {};
        newFilter.entryType = [];
        newFilter.status = [];
        //Reorganize the data in a better way for arrays entryType and status
        filter.forEach((f) => {
            if (f.name === 'entryType' || f.name === 'status') {
                newFilter[f.name].push(f.value);
            } else {
                newFilter[f.name] = f.value;
            }
        });
        //get the settlementDate
        newFilter.settlementDate = newFilter.year + newFilter.month;

        if(typeof window.cashFlow[newFilter.settlementDate] !== 'undefined'){
            filterData2(newFilter);
        } else {
            $.ajax( "/cashFlowData/" + newFilter.settlementDate )
            .done(function(res) {
                window.cashFlow[newFilter.settlementDate] = [];
                window.cashFlow[newFilter.settlementDate] = res;
                filterData2(newFilter);
            })
            .fail(function() {
                alert( "error" );
            });
        }
    }

    function filterData2(newFilter) {

        //Clone the cashflow table
        data = JSON.parse(JSON.stringify(window.cashFlow[newFilter.settlementDate]))
        //Add the entryType filter only in case the amount of options selected is less than the total
        if (newFilter.entryType.length !== 5) {
            data = data.filter(function(entry) {
                return (newFilter.entryType.indexOf(entry.entry_type.toString()) > -1);
            });
        }
        //Add the status filter only in case the amount of options selected is less than the total
        if (newFilter.status.length !== 2) {
            data = data.filter(function(entry) {
                if (newFilter.status[0] == 'Pendientes') {
                    return entry.operation_amount == null || entry.operation_amount === 0;
                } else {
                    return entry.operation_amount > 0;
                }
            });
        }

        let groupedData = {};
        if(newFilter.view != 'cashFlow') {
            let groupByFunction = groupBy(newFilter.view);
            groupedData = groupByFunction(data);
        }
        
        let html = '';
        let links = '';
        dataKeys = Object.keys(groupedData);
        
        dataKeys.forEach((dataKey) => {
            let caption = dataKey.slice(0);
            if( newFilter.view  === 'entry_type' ){
                caption = getEntryType(caption);
            }
            if( newFilter.view  === 'settlement_week' ){
                caption = getOrdinalNumber(caption);
            }
            links += '<a href="#' + caption + '">' + caption + '</a>'
            html += drawTable(groupedData[dataKey], caption);
            

        });
        html = links + html;
        
        $('#tables_group').html(html);
    }

    const groupBy = key => array =>
        array.reduce((objectsByKeyValue, obj) => {
            const value = obj[key];
            objectsByKeyValue[value] = (objectsByKeyValue[value] || []).concat(obj);
            return objectsByKeyValue;
        }, {});

    function executeOperation(id){
        alert(id);
        $.ajax( "/admin/app_operations/execute/" + id )
            .done(function(res) {
                alert("ok");
            })
            .fail(function() {
                alert( "error" );
            })
    }
    function drawTable(table, caption) {
        
        let total_estimated_amount = 0;
        let total_operation_amount = 0;
        let html = `
        <a id="` + caption + `"></a><a href="#top">Subir</a>
        <table id='table_dashboard' class="table table-hover table-striped table-bordered">
            <caption>` + caption + `</caption>
            <thead>
                <tr class="active">
                    <th style="width:30px">&nbsp;</th>
                    <th><a href="">Fecha estimada</a></th>
                    <th><a href="">Fecha operación</a></th>
                    <th><a href="">Tipo</a></th>
                    <th><a href="">Concepto</a></th>
                    <th><a href="">Detalle</a></th>
                    <th><a href="">Cuenta</a></th>
                    <th style="width:60px;text-align:right;"><a href="">Monto estimado</a></th>
                    <th style="width:60px;text-align:right;"><a href="">Monto operación</a></th>
                    <th style="width:60px">Acciones</th>
                </tr>
            </thead>
            <tbody>`;
        table.forEach((row, i) => {
            operation_date = (row.operation_date != null) ? row.operation_date.substring(0,10) : '&nbsp;'
            estimated_date = (row.estimated_date != null) ? row.estimated_date.substring(0,10) : '&nbsp;'
            estimated_amount = moneyFormat(row.estimated_amount, row.currency);
            operation_amount = moneyFormat(row.operation_amount, row.currency);
            html += `
            <tr>
                <td><input type="checkbox" class="checkbox" name="checkbox[]" value="` + row.operation_id + `"/></td>
                <td>` + estimated_date + `</td>
                <td>` + operation_date + `</td>
                <td>` + getEntryType(row.entry_type) + `</td>
                <td><a href="/admin/app_entries/edit/` + row.entry_id + `?return_url=/cashFlow/` + row.settlement_date + `">` + row.concept + `</a></td>
                <td>` + row.detail + `</td>
                <td>` + row.account_name + `</td>
                <td class="right">` + estimated_amount + `</td>
                <td class="right">` + operation_amount + `</td>
                <td class="right">`;
            if(row.is_done != 1) {
                html += `
                <a href="javascript: void executeOperation(` + row.operation_id + `)" class="btn btn-success btn-xs"><i class="fa fa-money"></i></a>`;
            }
            html += `
                <a href="/admin/app_operations/edit/` + row.operation_id + `?return_url=/cashFlow/` + row.settlement_date + `" class="btn btn-warning btn-xs"><i class="fa fa-pencil"></i></a>
                <a href="javascript:void(0)" onclick="deleteRow{{$name}}(this)" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></a>
            </td>`;
            html += '</tr>';
            total_estimated_amount += row.estimated_amount;
            total_operation_amount += row.operation_amount;
        });
        html += `
            </tbody>
            <tfoot>
                <tr>
                    <td>&nbsp;</td>
                    <td>Fecha estimada</td>
                    <td>Fecha operación</td>
                    <td>Tipo</td>
                    <td>Concepto</td>
                    <td>Detalle</td>
                    <td>Cuenta</td>
                    <td class="right">Monto estimado</td>
                    <td class="right">Monto operación</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td class="right">Total:</td>
                    <td class="right"><b>` + moneyFormat(total_estimated_amount, '$') + `</b></td>
                    <td class="right"><b>` + moneyFormat(total_operation_amount, '$') + `</b></td>
                    <td>&nbsp;</td>
                </tr>
            </tfoot>
        </table>`;
        return html;
    }

function moneyFormat(number, currency) {
    let res = '';
    if (number !== null) {
        let newNumber = [];
        number = number + '';
        str = number.split("").reverse();
        str.forEach((n,i)=>{
            if (i === 2) {
                newNumber.push(',');
            } else if ( ( i - 2 ) / 3 == Math.round( (i - 2) / 3 ) && i > 3 ) {
                newNumber.push('.');
            }
            newNumber.push(n);
        });
        currency = (typeof currency != 'undefined') ? currency : '';
        res =  currency + ' ' + newNumber.reverse().join("");
    }
    return res;
}
function getEntryType(type)
	{
        type = parseInt(type);
        let res = '';
		switch (type) {
			case 1:
				res = "Ingreso";
				break;
			case 2:
				res = "Egreso";
				break;
			case 3:
				res = "Pasivo";
				break;
			case 4:
				res = "Movimiento";
				break;
			default:
				res = "Error";
				break;
		}
		return res;
	}
function getOrdinalNumber(number, genre)
	{
        number = parseInt(number);
        genre = (typeof genre !== 'undefined') ? genre : 'female';
        let maleNumbers = ['-','Primero','Segundo','Tercero','Cuarto','Quinto'];
        let femaleNumbers = ['-','Primera','Segunda','Tercera','Cuarta','Quinta'];
        if(genre === 'male'){
            res = maleNumbers[number];
        } else {
            res = femaleNumbers[number];
        }
		return res;
	}
</script>

<form id='form-table' method='post' action='{{CRUDBooster::mainpath("action-selected")}}'>
    <input type='hidden' name='button_name' value='' />
    <input type='hidden' name='_token' value='{{csrf_token()}}' />
    <div id="tables_group">
    </div>
</form>
<!--END FORM TABLE-->


@endsection
