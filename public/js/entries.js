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
    $('#plancurrency_plan').on('click', function(){
        format.prefix = $(this).val() + ' ';
        $('#planamount').priceFormat(format);
    });

    $('#currency').on('click', function(){
        format.prefix = $(this).val() + ' ';
        $('#real_amount').priceFormat(format);
        $('#one_pay_amount').priceFormat(format);
    });
    $('#planplan').on('change', function(){
        if($(this).val() == 1){
            $($('#planfrequency')[0].parentNode.parentNode).hide();
        } else {
            $($('#planfrequency')[0].parentNode.parentNode).show();
        }
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
    


    $('#concept').focus();
})


//Agregar filtro por tipo de cuenta: efectivo, tarjetas o bancos (incluye CA, CC, tanto para transferencias como debitos, depositos  y cheques)
    //Hacer vista de saldos por cuentas