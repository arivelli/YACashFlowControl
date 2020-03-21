function setLastUpdateAmount(account_id, amount) {
    let numberPattern = /\d+/g;

    let data = {
        account_id : account_id,
        amount : amount.match(numberPattern).join('')
    }
    $.post($(location).attr('href') + '/setLastUpdateAmount', 
    data,
    (res) => {
        location.reload();
    });
    
}
$(function() {
    $('.accountAmount').priceFormat({
        'prefix': "$ ",
        'centsSeparator': ",",
        'thousandsSeparator': ".",
        'centsLimit': 2
    });
})
