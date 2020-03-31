<?php
$name = str_slug($form['label'], '');
?>
@push('bottom')
    <script type="text/javascript">
        $(function () {
            $('#form-group-{{$name}} .select2').select2();
        })
    </script>
@endpush
<div class='form-group {{$header_group_class}}' id='form-group-{{$name}}'> 

    @if($form['columns'])
        <div class="col-sm-12">

            <div id='panel-form-{{$name}}' class="panel panel-default">
                <div class="panel-heading">
                    <i class='fa fa-bars'></i> {{$form['label']}} 
                <input type="button" name="add" value="Añadir" class="btn btn-success" onclick="resetForm{{$name}}();$('#child2_form{{$form['table']}}').show()">
                </div>
                <div class="panel-body">

                    @include('/vendor/crudbooster/type_components/child2/component_index')
                    
                    @include('/vendor/crudbooster/type_components/child2/component_form')
                    

                </div>
                <!-- /.box-body -->
            </div>
        </div>

    @extends('/vendor/crudbooster/type_components/child2/component_previewOperation')
    

    @else

        <div style="border:1px dashed #c41300;padding:20px;margin:20px">
            <span style="background: yellow;color: black;font-weight: bold">CHILD {{$name}} : COLUMNS ATTRIBUTE IS MISSING !</span>
            <p>You need to set the "columns" attribute manually</p>
        </div>
    @endif
</div>
