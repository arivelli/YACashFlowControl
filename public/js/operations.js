$(document).ready( () => {
    if(select3) {
        $('#account_id').on('change',(e)=>{
            $('#currency').val( 
                account_id_data.find(
                    function (elem){ return elem.id == $('#account_id').val() }).currency
            )
        })
    }
});