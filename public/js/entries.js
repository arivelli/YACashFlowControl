
$(document).ready(function(){
    if ($('form#form.form-horizontal').length > 0) {
 
        //Set default props
        $($('input[type=radio][name=child-currency_plan]')[0].parentNode.parentNode.parentNode).hide();
        //Capture all form events and adjust the form
        $(":input").on('change', (e) => {
            adjustForm();
        })

        window.entries = {};
        $($('#real_amount')[0]).on("keyup keydown", (e) => {
            const we = window.entries;
            if (Number.isInteger(parseInt(e.key))) {
                switch (e.type) {
                    case 'keydown':
                        let real_amount = money2int($('#real_amount').val());
                        let one_pay_amount = money2int($('#one_pay_amount').val());
                        we.one_pay_amount = ($('#one_pay_amount').val() == '' || one_pay_amount == real_amount);
                        if ($('#planesplan').val() == 1) {
                            let planesamount = money2int($('#planesamount').val());
                            we.planesamount = ($('#planesamount').val() == '' || planesamount == real_amount);
                        }
                        break;
                    case 'keyup':
                        if (we.one_pay_amount === true) {
                            $('#one_pay_amount').val($('#real_amount').val());
                            we.one_pay_amount = false;
                        }
                        if (we.planesamount === true) {
                            $('#planesamount').val($('#real_amount').val());
                            we.planesamount = false;
                        }
                        break;
                    default:
                        break;
                }
            }
        });
        adjustForm();
        //$('#concept').focus();
    }
})



function filterAccounts(currency, type) {
    if (typeof currency != 'undefined' && typeof type != 'undefined' && (currency != window.entries.currency || type != window.entries.type)) {
        window.entries.currency = currency;
        window.entries.type = type;

        let accountList = '';
        let selected = ' selected';
        child_account_id.forEach( (account, i) => {
            if(account.currency == currency && account.type == type) {
                accountList += '<option value="'+account.id+'"'+selected+'>'+account.title+'</option>';
                selected = '';
            }
        });
        $('#planesaccount_id').html(accountList);
    }
}

function adjustForm() {
    
    //Show/Hide area/category depending on entryType
    switch ($('#entry_type').val()) {
        //Ingreso
        case '1':
            $('#form-group-area_id').show();
            $('#form-group-category_id').hide();
            $('#form-group-affect_capital').show();
            $('#form-group-is_extraordinary').show();
            $('#form-group-planes').show();
            break;
        //Egreso
        case '2':
        //Pasivo
        case '3':
            $('#form-group-area_id').show();
            //Show Categories only for area = Personal
            if($('#area_id').val() === '6' || $('#area_id').val() === '10'){
                $('#form-group-category_id').show();
            } else {
                $('#form-group-category_id').hide();
            }
            $('#form-group-affect_capital').show();
            $('#form-group-is_extraordinary').show();
            $('#form-group-planes').show();
            break;
        //Hide both on Movimiento and Ajuste
        case '4':
        case '5':
            $('#form-group-area_id').show();
            $('#form-group-category_id').hide();
            $('#form-group-affect_capital').hide();
            $('#form-group-is_extraordinary').hide();
            $('#form-group-planes').hide();
            break;
    }

    
    //Set the right money format to all the amount fields
    let currency = $('input[type=radio][name=currency]:checked').val();
    format.prefix = currency + ' ';
    $('#real_amount').priceFormat(format);
    $('#one_pay_amount').priceFormat(format);
    $('#planesamount').priceFormat(format);
    
    //Update the proper field inside plan form
    $('input[type=radio][name=child-currency_plan][value="'+currency+'"]').prop("checked",true)

    //Based on date update the dollar value
    $('#dollar_value').val( get_dollar_value_of( $('#date').val() ) )
    $('#dollar_value').priceFormat(format);

    //PLANS

    //Filter available account types based on entry types
    //'1|Caja de ahorro;2|Cuenta corriente;3|Efectivo;4|Tarjeta;5|Pasivo';
    //In case of 'Ingreso' or 'Egreso' only allow 1,2,3
    if ($('#entry_type').val() == 1 || $('#entry_type').val() == 2) {
        $($('input[type=radio][name=child-account_type][value=1]')[0].parentNode).show()
        $($('input[type=radio][name=child-account_type][value=2]')[0].parentNode).show()
        $($('input[type=radio][name=child-account_type][value=3]')[0].parentNode).show()
        $($('input[type=radio][name=child-account_type][value=4]')[0].parentNode).hide()
        $($('input[type=radio][name=child-account_type][value=5]')[0].parentNode).hide()
    //In case of Pasivo only allow 4,5
    } else if ($('#entry_type').val() == 3) {
        $($('input[type=radio][name=child-account_type][value=1]')[0].parentNode).hide()
        $($('input[type=radio][name=child-account_type][value=2]')[0].parentNode).hide()
        $($('input[type=radio][name=child-account_type][value=3]')[0].parentNode).hide()
        $($('input[type=radio][name=child-account_type][value=4]')[0].parentNode).show()
        $($('input[type=radio][name=child-account_type][value=5]')[0].parentNode).show()
    //In case of 'Movimiento' or 'Ajuste' allow all
    } else {
        $($('input[type=radio][name=child-account_type][value=1]')[0].parentNode).show()
        $($('input[type=radio][name=child-account_type][value=2]')[0].parentNode).show()
        $($('input[type=radio][name=child-account_type][value=3]')[0].parentNode).show()
        $($('input[type=radio][name=child-account_type][value=4]')[0].parentNode).show()
        $($('input[type=radio][name=child-account_type][value=5]')[0].parentNode).show()
    }

    //Filter available accounts based on currency and account type selected
    filterAccounts($('input[type=radio][name=currency]:checked').val(), $('input[type=radio][name=child-account_type]:checked').val());

    //Show/Hide Frequency and Detail format fields depending on Plan 
    if($('#planesplan').val() == 1){
        $($('#planesfrequency_id')[0].parentNode.parentNode).hide();
        $($('#planesdetail_format')[0].parentNode.parentNode).hide();
    } else if ($('#planesplan').val() == -1) {
        $($('#planesfrequency_id')[0].parentNode.parentNode).show();
        $($('#planesdetail_format')[0].parentNode.parentNode).show();
    } else {
        $($('#planesfrequency_id')[0].parentNode.parentNode).show();
        $($('#planesdetail_format')[0].parentNode.parentNode).hide();
    }
    
    //
    
    //Show/Hide notification fields depending on planesnotification_to 
    if ($('.planesnotification_to').serializeArray().length > 0) {
        $($('#planesnotification_offset').parents('div.form-group')[0]).show()
    } else {
        $($('#planesnotification_offset').parents('div.form-group')[0]).hide()
    }
}

/**
 * PREVIEW OF OPERATIONS
 */

function showModalPreviewOperation(id) {

    $.post("/admin/app_entries/preview_plan/", $('form#form').serialize())
        .done(function (res) {
            $('#preview-operation_detail').html(res);
            $('#modal-datamodal-preview-operation').modal('show');
        })
        .fail(function() {
            alert("error");
        });

}

//Agregar filtro por tipo de cuenta: efectivo, tarjetas o bancos (incluye CA, CC, tanto para transferencias como debitos, depositos  y cheques)
    //Hacer vista de saldos por cuentas
