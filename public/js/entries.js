let format = {
    'prefix' 				: '$ ',
    'thousandsSeparator'    : '.',
    'centsSeparator'        : ',',
    'centsLimit'          	: 2,
    'clearOnEmpty'         	: false,
    'limit'                 : false,
    'allowNegative'         : true
}
$(document).ready(function(){

    
    $('input[type=radio][name=currency]').on('change', function(){
        format.prefix = $(this).val() + ' ';
        $('#real_amount').priceFormat(format);
        $('#one_pay_amount').priceFormat(format);
    });
 
    $('#entry_type').on('change', function(){
        switch($(this).val())  {
            case '1':
                $($('#area_id')[0].parentNode.parentNode).show();
                $($('#category_id')[0].parentNode.parentNode).hide();
                break;
            case '2':
                $($('#area_id')[0].parentNode.parentNode).show();
                if($('#area_id').val() === '6'){
                    $($('#category_id')[0].parentNode.parentNode).show();
                } else {
                    $($('#category_id')[0].parentNode.parentNode).hide();
                }
                break;
            case '3':
            case '4':
            case '5':
                $($('#area_id')[0].parentNode.parentNode).hide();
                $($('#category_id')[0].parentNode.parentNode).hide();
                break;
        }

    });

    $('#area_id').on('change', function(){
        if($(this).val() === '6'){
            $($('#category_id')[0].parentNode.parentNode).show();
        } else {
            $($('#category_id')[0].parentNode.parentNode).hide();
        }
    });
    
    $('input[type=radio][name=child-currency_plan]').on('change', function(){
        format.prefix = $(this).val() + ' ';
        $('#planamount').priceFormat(format);
        filterAccounts( $('input[type=radio][name=child-currency_plan]:checked').val(), $('input[type=radio][name=child-account_type]:checked').val());
    });

    $('input[type=radio][name=child-account_type]').on('change', function(){
        filterAccounts( $('input[type=radio][name=child-currency_plan]:checked').val(), $('input[type=radio][name=child-account_type]:checked').val());
    });
    
    $('#planplan').on('change', function(){
        if($(this).val() == 1){
            $($('#planfrequency')[0].parentNode.parentNode).hide();
        } else {
            $($('#planfrequency')[0].parentNode.parentNode).show();
        }
    });
    
    //$($('#planfrequency')[0].parentNode.parentNode).hide();
    $('#concept').focus();
})

function filterAccounts(currency, type) {
    if(typeof currency != 'undefined' && typeof type != 'undefined') {
        let accountList = '';
        child_account_id.forEach( (account) => {
            if(account.currency == currency && account.type == type) {
                accountList += '<option value="'+account.id+'">'+account.title+'</option>';
            }
        });
        $('#planaccount_id').html(accountList);
    }
}


//Agregar filtro por tipo de cuenta: efectivo, tarjetas o bancos (incluye CA, CC, tanto para transferencias como debitos, depositos  y cheques)
    //Hacer vista de saldos por cuentas