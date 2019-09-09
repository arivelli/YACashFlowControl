@extends('crudbooster::admin_template')
<style>
    #filterEntryType .btn {
        width: 150px;
    }

    #filterStatus .btn {
        width: 150px;
    }
</style>
@section('content')
<div class='test'>
    <div class='title'><?php echo e($page_title); ?></div>
</div>
<form name="filter" id="filter">
    <div id="filterYears">
        <label><input name="year" type="radio" onclick="filterData()" value="2015">2015</label>
        <label><input name="year" type="radio" onclick="filterData()" value="2016">2016</label>
        <label><input name="year" type="radio" onclick="filterData()" value="2017">2017</label>
        <label><input name="year" type="radio" onclick="filterData()" value="2018">2018</label>
        <label><input name="year" type="radio" onclick="filterData()" value="2019">2019</label>
        <label><input name="year" type="radio" onclick="filterData()" value="2020">2020</label>
        <label><input name="year" type="radio" onclick="filterData()" value="2021">2021</label>
        <label><input name="year" type="radio" onclick="filterData()" value="2022">2022</label>
    </div>
    <div id="filterMonths">
        <label><input name="month" type="radio" onclick="filterData()" value="01">ENE</label>
        <label><input name="month" type="radio" onclick="filterData()" value="02">FEB</label>
        <label><input name="month" type="radio" onclick="filterData()" value="03">MAR</label>
        <label><input name="month" type="radio" onclick="filterData()" value="04">ABR</label>
        <label><input name="month" type="radio" onclick="filterData()" value="05">MAY</label>
        <label><input name="month" type="radio" onclick="filterData()" value="06">JUN</label>
        <label><input name="month" type="radio" onclick="filterData()" value="07">JUL</label>
        <label><input name="month" type="radio" onclick="filterData()" value="08">AGO</label>
        <label><input name="month" type="radio" onclick="filterData()" value="09">SEP</label>
        <label><input name="month" type="radio" onclick="filterData()" value="10">OCT</label>
        <label><input name="month" type="radio" onclick="filterData()" value="11">NOV</label>
        <label><input name="month" type="radio" onclick="filterData()" value="12">DIC</label>
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
        <label><input name="view" type="radio" onclick="filterData()" value="cashFlow" checked>CashFlow</label>
        <label><input name="view" type="radio" onclick="filterData()" value="name">Cuenta</label>
        <label><input name="view" type="radio" onclick="filterData()" value="area_id">Area</label>
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
    window.cashFlow = {!!$cashFlow!!}

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
        //Clone the cashflow table
        data = JSON.parse(JSON.stringify(window.cashFlow))
        //Add the entryType filter only in case the amount of options selected is less than the total
        if (newFilter.entryType.length !== 5) {
            data = data.filter(function(entry) {
                return (newFilter.entryType.indexOf(entry.entry_type.toString()) > -1);
            });
        }
        //Add the status filter only in case the amount of options selected is less than the total
        if (newFilter.status.length !== 2) {
            console.log(newFilter.status);
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
            console.log('-----------')
            console.log(groupedData)
            console.log('-----------')
        }
        
        let html = '';
        dataKeys = Object.keys(groupedData);
        dataKeys.forEach((dataKey) => {
            html += drawTable(groupedData[dataKey], dataKey);
        });
        
        $('#tables_group').html(html);
    }

    const groupBy = key => array =>
        array.reduce((objectsByKeyValue, obj) => {
            const value = obj[key];
            objectsByKeyValue[value] = (objectsByKeyValue[value] || []).concat(obj);
            return objectsByKeyValue;
        }, {});

    function drawTable(table, caption) {
        let estimated_amount = 0;
        let operation_amount = 0;
        let html = `<table id='table_dashboard' class="table table-hover table-striped table-bordered">
        <caption>` + caption + `</caption>
            <thead>
                <tr class="active">
                    <th>Input</th>
                    <th><a href="">Fecha estimada</a></th>
                    <th><a href="">Fecha operación</a></th>
                    <th><a href="">Tipo</a></th>
                    <th><a href="">Concepto</a></th>
                    <th><a href="">Detalle</a></th>
                    <th><a href="">Cuenta</a></th>
                    <th><a href="">Monto estimado</a></th>
                    <th><a href="">Monto operación</a></th>
                    <th><a href="">Acciones</a></th>
                </tr>
            </thead>
            <tbody>`;
        table.forEach((row, i) => {
            html += '<tr>';
            html += '<td><input type="checkbox" class="checkbox" name="checkbox[]" value="' + row.id + '"/></td>'
            html += '<td>' + row.estimated_date + '</td>';
            html += '<td>' + row.operation_date + '</td>';
            html += '<td>' + row.entry_type + '</td>';
            html += '<td>' + row.concept + '</td>';
            html += '<td>' + row.detail + '</td>';
            html += '<td>' + row.name + '</td>';
            html += '<td>' + row.estimated_amount + '</td>';
            html += '<td>' + row.operation_amount + '</td>';
            html += '<td> ACCIONES </td>';
            html += '</tr>';
            estimated_amount += row.estimated_amount;
            operation_amount += row.operation_amount;
        });
        html += `<tr>
                    <td></td>
                    <td></td>
                    <td>Fecha operación</td>
                    <td>Tipo</td>
                    <td>Concepto</td>
                    <td>Detalle</td>
                    <td>Cuenta</td>
                    <td>` + estimated_amount + `</td>
                    <td>` + operation_amount + `</td>
                    <td>Acciones</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td>Input</td>
                    <td>Fecha estimada</td>
                    <td>Fecha operación</td>
                    <td>Tipo</td>
                    <td>Concepto</td>
                    <td>Detalle</td>
                    <td>Cuenta</td>
                    <td>Monto estimado</td>
                    <td>Monto operación</td>
                    <td>Acciones</td>
                </tr>
            </tfoot>
        </table>`;
        return html;
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