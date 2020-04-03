<div class='form-group {{$header_group_class}}' id='form-group-{{$name}}'> 
Hola {{$form['controller']}}

</div>

<div style="display: block" id="submodule-temp"></div>
@push('bottom')
    <script type="text/javascript">
        $(function(){
            
            $.ajax("/admin/app_operations/")
                .done(function (res) {
                    
                    $('#form-group-{{$name}}').html(res);
            })
            .fail(function() {
                alert("error");
            });
        })
    </script>
@endpush