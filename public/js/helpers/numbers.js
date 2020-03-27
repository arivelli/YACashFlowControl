let format = {
    'prefix' 				: '$ ',
    'thousandsSeparator'    : '.',
    'centsSeparator'        : ',',
    'centsLimit'          	: 2,
    'clearOnEmpty'         	: true,
    'limit'                 : false,
    'allowNegative'         : true
}

const numberPattern = /\d+/g;

if (typeof window.YACFC === 'undefined') {
    window.YACFC = {};
}
    
function money2int(money) {
    return parseInt( ('0' + money).match(numberPattern).join(''));
}
function int2money() { 

}

function get_dollar_value_of(quote_date) {
    let YACFC = window.YACFC;
    if (typeof YACFC.dollar_value === 'undefined') {
        YACFC.dollar_value = {};
    }
    if (typeof YACFC.dollar_value[quote_date] !== 'undefined') {
        return YACFC.dollar_value[quote_date];
    } else {
        $.get({
            url: '/admin/dollarValue/getvalueof/' + quote_date,
            success: (data) => {
                YACFC.dollar_value[quote_date] = data;
                return YACFC.dollar_value[quote_date];
            }
        });
    }

}