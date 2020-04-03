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
$(function () {
    if ($('form#form.form-horizontal').length > 0) {
        $('.accountAmount').priceFormat({
            'prefix': "$ ",
            'centsSeparator': ",",
            'thousandsSeparator': ".",
            'centsLimit': 2
        });
        $('#type').on('change', () => {
            showHideEntries();
        });
        $('#entry_id').on('change', () => {
            
            $.ajax("/admin/app_plans/getPlanByEntryId/" + $('#entry_id').val())
                .done(function (res) {
                    let html = '';
                    res.forEach((p) => {
                        html += '<option value="'+p.value+'">'+p.label+'</option>';
                    });
                    $('#plan_id').html(html);
            })
            .fail(function() {
                alert("error");
            });
        });
        showHideEntries();
    }
})

function showHideEntries() {
    if ($('#type').val() === "4" || $('#type').val() === "5" ) {
        $($('#entry_id')[0].parentNode.parentNode).show();
        $($('#plan_id')[0].parentNode.parentNode).show();
    } else { 
        $($('#entry_id')[0].parentNode.parentNode).hide();
        $($('#plan_id')[0].parentNode.parentNode).hide();
    }
}
