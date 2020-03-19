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
    window.cashFlow.settlement_date = newFilter.settlementDate;
    
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
    let links = [];
    dataKeys = Object.keys(groupedData);
    
    dataKeys.forEach((dataKey) => {
        let caption = dataKey.slice(0);
        if( newFilter.view  === 'entry_type' ){
            caption = getEntryType(caption);
        }
        if( newFilter.view  === 'settlement_week' ){
            caption = getOrdinalNumber(caption);
        }
        links.push('<a href="#' + caption + '">' + caption + '</a>');
        html += drawTable(groupedData[dataKey], caption);
        

    });
    html = links.join(' | ') + '<hr>' + html;
    
    $('#tables_group').html(html);
    $('.'+newFilter.view).hide();
}

const groupBy = key => array =>
    array.reduce((objectsByKeyValue, obj) => {
        const value = obj[key];
        objectsByKeyValue[value] = (objectsByKeyValue[value] || []).concat(obj);
        return objectsByKeyValue;
    }, {});


function showModalExecuteOperation(id){
    let operation = window.cashFlow[window.cashFlow.settlement_date].find((e) => { return e.operation_id == id });
    $('#cancel-operation_concept').html(operation.concept);
    $('#cancel-operation_detail').html(operation.detail);
    $('#cancel-operation_operation_amount').val(operation.estimated_amount);
    $('#cancel-operation_account_id').val(operation.account_id);
    $('#cancel-operation_operation_id').val(operation.operation_id);
    $('#cancel-operation_operation_date').val(operation.estimated_date);
    set_dollar_value();
    
    $('#cancel-operation_operation_amount').priceFormat({
        'prefix' : operation.currency + " ",
        'centsSeparator' : ",",
        'thousandsSeparator' : ".",
        'centsLimit'  : 2
    });
    $('#modal-datamodal-cancel-operation').modal('show');
}

function executeOperation(id){
    let data = {};
    var numberPattern = /\d+/g;

    data.operation_amount = $('#cancel-operation_operation_amount').val().match( numberPattern ).join('');
    data.dollar_value = $('#cancel-operation_dollar_value').val().match( numberPattern ).join('');
    data.account_id = $('#cancel-operation_account_id').val();
    data.operation_id = $('#cancel-operation_operation_id').val();
    data.operation_date = $('#cancel-operation_operation_date').val();


    $.post( "/admin/app_operations/execute/" + id , data)
        .done(function(res) {
            alert("ok");
        })
        .fail(function() {
            alert( "error" );
        });
    
}
function drawTable(table, caption) {
    
    let total_estimated_amount = 0;
    let total_operation_amount = 0;
    let html = `
    <br>
    <a id="` + caption + `"></a><a href="#top">Subir</a>
    <table id='table_dashboard' class="table table-hover table-striped table-bordered">
        <caption>` + caption + `</caption>
        <thead>
            <tr class="active">
                <th style="width:30px">&nbsp;</th>
                <th><a href="">Fecha estimada</a></th>
                <th><a href="">Fecha operación</a></th>
                <th class="entry_type"><a href="">Tipo</a></th>
                <th><a href="">Concepto</a></th>
                <th><a href="">Detalle</a></th>
                <th class="account_name"><a href="">Cuenta</a></th>
                <th class="area"><a href="">Area</a></th>
                <th class="category"><a href="">Categoría</a></th>
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
            <td class="entry_type">` + getEntryType(row.entry_type) + `</td>
            <td><a href="/admin/app_entries/edit/` + row.entry_id + `?return_url=/cashFlow/` + row.settlement_date + `">` + row.concept + `</a></td>
            <td>` + row.detail + `</td>
            <td class="account_name">` + row.account_name + `</td>
            <td class="area">` + row.area + `</td>
            <td class="category">` + row.category + `</td>
            <td class="right">` + estimated_amount + `</td>
            <td class="right">` + operation_amount + `</td>
            <td class="right">`;
        if(row.is_done != 1) {
            html += `
            <a href="javascript: void showModalExecuteOperation(` + row.operation_id + `)" class="btn btn-success btn-xs"><i class="fa fa-money"></i></a>`;
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
                <td class="entry_type">Tipo</td>
                <td>Concepto</td>
                <td>Detalle</td>
                <td class="account_name">Cuenta</td>
                <td class="area">Area</td>
                <td class="category">Categoría</td>
                <td class="right">Monto estimado</td>
                <td class="right">Monto operación</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td class="entry_type">&nbsp;</td>
                <td>&nbsp;</td>
                <td class="account_name">&nbsp;</td>
                <td class="area">&nbsp;</td>
                <td class="category">&nbsp;</td>
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

function set_dollar_value(){
    if (typeof window.cashFlow.dollar_value === 'undefined'){
        window.cashFlow.dollar_value = {};
    }
    let quote_date = $('#cancel-operation_operation_date').val();
    if(typeof window.cashFlow.dollar_value[quote_date] !== 'undefined') {
        $('#cancel-operation_dollar_value').val(window.cashFlow.dollar_value[quote_date]);
        $('#cancel-operation_dollar_value').trigger('keyup');
    } else {
        $.get({
            url : '/dollarValue/getvalueof/'+quote_date,
            success: (data) => {
                $('#cancel-operation_dollar_value').val(data);
                window.cashFlow.dollar_value[quote_date] = data;
                $('#cancel-operation_dollar_value').trigger('keyup');
            }
        });
    }

}