<div class='row' style="display:none;" id="child2_form{{$form['table']}}">
    <div class='col-sm-12'>
        <div class="panel panel-default">
            <div class="panel-heading"><i class="fa fa-pencil-square-o"></i> {{trans("crudbooster.text_form")}}</div>
            <div class="panel-body child-form-area">
                @foreach($form['columns'] as $col)
                    <?php $name_column = $name.$col['name'];?>
                    <div class='form-group'>
                        @if($col['type']!='hidden')
                            <label class="control-label col-sm-2">{{$col['label']}}
                                @if(!empty($col['required'])) <span class="text-danger"
                                                                    title="{{trans('crudbooster.this_field_is_required')}}">*</span> @endif
                            </label>
                        @endif
                        <div class="col-sm-10">
                            @if($col['type']=='text')
                                <input id='{{$name_column}}' type='text'
                                       {{ ($col['max'])?"maxlength='".$col['max']."'":"" }} name='child-{{$col["name"]}}'
                                    class='form-control {{$col['required']?"required":""}}' value='{{$col['value']}}'
                                        {{($col['readonly']===true)?"readonly":""}}
                                />
                            @elseif($col['type']=='radio' || $col['type']=='checkbox')
                                <?php
                                if($col['dataenum']):
                                $dataenum = $col['dataenum'];
                                if (strpos($dataenum, ';') !== false) {
                                    $dataenum = explode(";", $dataenum);
                                } else {
                                    $dataenum = [$dataenum];
                                }
                                array_walk($dataenum, 'trim');
                                foreach($dataenum as $e=>$enum):
                                $enum = explode('|', $enum);
                                if (count($enum) == 2) {
                                    $radio_value = $enum[0];
                                    $radio_label = $enum[1];
                                } else {
                                    $radio_value = $radio_label = $enum[0];
                                }
                                $checked = ($radio_value == $col['value']) ? 'checked' : '';
                                ?>
                                <label class="radio-inline">
                                <input type="{{$col['type']}}" name="child-{{$col['name']}}"
                                           class='{{ ($e==0 && $col['required'])?"required":""}} {{$name_column}}'
                                           {{($col['disabled']===true)?"disabled":""}}
                                           value="{{$radio_value}}"{{ $checked }}> {{$radio_label}}
                                </label>
                                <?php endforeach;?>
                                <?php endif;?>
                            @elseif($col['type']=='datamodal')

                                <div id='{{$name_column}}' class="input-group">
                                    <input type="hidden" class="input-id">
                                    <input type="text" class="form-control input-label {{$col['required']?"required":""}}" readonly>
                                    <span class="input-group-btn">
                    <button class="btn btn-primary" onclick="showModal{{$name_column}}()" type="button"><i
                                class='fa fa-search'></i> {{trans('crudbooster.datamodal_browse_data')}}</button>
                  </span>
                                </div><!-- /input-group -->

                                @push('bottom')
                                    <script type="text/javascript">
                                        var url_{{$name_column}} = "{{CRUDBooster::mainpath('modal-data')}}?table={{$col['datamodal_table']}}&columns=id,{{$col['datamodal_columns']}}&name_column={{$name_column}}&where={{urlencode($col['datamodal_where'])}}&select_to={{ urlencode($col['datamodal_select_to']) }}&columns_name_alias={{urlencode($col['datamodal_columns_alias'])}}";
                                        var url_is_setted_{{$name_column}} = false;

                                        function showModal{{$name_column}}() {
                                            if (url_is_setted_{{$name_column}} == false) {
                                                url_is_setted_{{$name_column}} = true;
                                                $('#iframe-modal-{{$name_column}}').attr('src', url_{{$name_column}});
                                            }
                                            $('#modal-datamodal-{{$name_column}}').modal('show');
                                        }

                                        function hideModal{{$name_column}}() {
                                            $('#modal-datamodal-{{$name_column}}').modal('hide');
                                        }

                                        function selectAdditionalData{{$name_column}}(select_to_json) {
                                            $.each(select_to_json, function (key, val) {
                                                console.log('#' + key + ' = ' + val);
                                                if (key == 'datamodal_id') {
                                                    $('#{{$name_column}} .input-id').val(val);
                                                }
                                                if (key == 'datamodal_label') {
                                                    $('#{{$name_column}} .input-label').val(val);
                                                }
                                                $('#{{$name}}' + key).val(val).trigger('change');
                                            })
                                            hideModal{{$name_column}}();
                                        }
                                    </script>
                                @endpush

                                <div id='modal-datamodal-{{$name_column}}' class="modal" tabindex="-1" role="dialog">
                                    <div class="modal-dialog {{ $col['datamodal_size']=='large'?'modal-lg':'' }} " role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                            aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title"><i
                                                            class='fa fa-search'></i> {{trans('crudbooster.datamodal_browse_data')}} {{$col['label']}}
                                                </h4>
                                            </div>
                                            <div class="modal-body">
                                                <iframe id='iframe-modal-{{$name_column}}' style="border:0;height: 430px;width: 100%"
                                                        src=""></iframe>
                                            </div>

                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->

                                @elseif($col['type']=='date')
                                <!--div class='form-group form-datepicker {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}'
                                    style="{{@$form['style']}}">
                                    <! --label class='control-label col-sm-2'>{{$form['label']}}
                                        @if($required)
                                            <span class='text-danger' title='{!! trans('crudbooster.this_field_is_required') !!}'>*</span>
                                        @endif
                                    </label-- >

                                    <div class="{{$col_width?:'col-sm-10'}}" -->
                                        <div class="input-group">
                                            <span class="input-group-addon open-datetimepicker"><a><i class='fa fa-calendar '></i></a></span>
                                            <input type='text' title="{{$col['label']}}" readonly
                                                {{$required}} {{$readonly}} {!!$placeholder!!} {{$disabled}} class='form-control notfocus input_date'
                                                name="child-{{$col["name"]}}" id="{{$name_column}}" value='{{ $col['value'] }}'/> 
                                        </div>
                                        <!--div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
                                        <p class='help-block'>{{ @$form['help'] }}</p-- >
                                    </div>
                                </div-->
                                @push('bottom')
                                @if (App::getLocale() != 'en')
                                    <script src="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/datepicker/locales/bootstrap-datepicker.'.App::getLocale().'.js') }}"
                                            charset="UTF-8"></script>
                                @endif
                                <script type="text/javascript">
                                    var lang = '{{App::getLocale()}}';
                                    $(function () {
                                        $('.input_date').datepicker({
                                            format: 'yyyy-mm-dd',
                                            @if (in_array(App::getLocale(), ['ar', 'fa']))
                                            rtl: true,
                                            @endif
                                            language: lang
                                        });

                                        $('.open-datetimepicker').click(function () {
                                            $(this).next('.input_date').datepicker('show');
                                        });

                                    });

                                </script>
                                @endpush

                            @elseif($col['type']=='number')
                                <input id='{{$name_column}}' type='number'
                                       {{ ($col['min'])?"min='".$col['min']."'":"" }} {{ ($col['max'])?"max='$col[max]'":"" }} name='child-{{$col["name"]}}'
                                       class='form-control {{$col['required']?"required":""}}'
                                        {{($col['readonly']===true)?"readonly":""}}
                                />
                            @elseif($col['type']=='money2')
                                <input id='{{$name_column}}' type='text' 
                                       {{ ($col['min'])?"min='".$col['min']."'":"" }} {{ ($col['max'])?"max='$col[max]'":"" }} name='child-{{$col["name"]}}'
                                       class='form-control inputMoney {{$col['required']?"required":""}}'
                                        {{($col['readonly']===true)?"readonly":""}}
                                />

                            @elseif($col['type']=='textarea')
                                <textarea id='{{$name_column}}' name='child-{{$col["name"]}}'
                                          class='form-control {{$col['required']?"required":""}}' {{($col['readonly']===true)?"readonly":""}} ></textarea>
                            @elseif($col['type']=='upload')
                                <div id='{{$name_column}}' class="input-group">
                                    <input type="hidden" class="input-id">
                                    <input type="text" class="form-control input-label {{$col['required']?"required":""}}" readonly>
                                    <span class="input-group-btn">
                    <button class="btn btn-primary" id="btn-upload-{{$name_column}}" onclick="showFakeUpload{{$name_column}}()"
                            type="button"><i class='fa fa-search'></i> {{trans('crudbooster.datamodal_browse_file')}}</button>
                  </span>
                                </div><!-- /input-group -->

                                <div id="loading-{{$name_column}}" class='text-info' style="display: none">
                                    <i class='fa fa-spin fa-spinner'></i> {{trans('crudbooster.text_loading')}}
                                </div>

                                <input type="file" id='fake-upload-{{$name_column}}' style="display: none">
                                @push('bottom')
                                    <script type="text/javascript">
                                        var file;
                                        var filename;
                                        var is_uploading = false;

                                        function showFakeUpload{{$name_column}}() {
                                            if (is_uploading) {
                                                return false;
                                            }

                                            $('#fake-upload-{{$name_column}}').click();
                                        }

                                        // Add events
                                        $('#fake-upload-{{$name_column}}').on('change', prepareUpload{{$name_column}});

                                        // Grab the files and set them to our variable
                                        function prepareUpload{{$name_column}}(event) {
                                            var max_size = {{ ($col['max'])?:2000 }};
                                            file = event.target.files[0];

                                            var filesize = Math.round(parseInt(file.size) / 1024);

                                            if (filesize > max_size) {
                                                sweetAlert('{{trans("crudbooster.alert_warning")}}', '{{trans("crudbooster.your_file_size_is_too_big")}}', 'warning');
                                                return false;
                                            }

                                            filename = $('#fake-upload-{{$name_column}}').val().replace(/C:\\fakepath\\/i, '');
                                            var extension = filename.split('.').pop().toLowerCase();
                                            var img_extension = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
                                            var available_extension = "{{config('crudbooster.UPLOAD_TYPES')}}".split(",");
                                            var is_image_only = {{ ($col['upload_type'] == 'image')?"true":"false" }};

                                            if (is_image_only) {
                                                if ($.inArray(extension, img_extension) == -1) {
                                                    sweetAlert('{{trans("crudbooster.alert_warning")}}', '{{trans("crudbooster.your_file_extension_is_not_allowed")}}', 'warning');
                                                    return false;
                                                }
                                            } else {
                                                if ($.inArray(extension, available_extension) == -1) {
                                                    sweetAlert('{{trans("crudbooster.alert_warning")}}', '{{trans("crudbooster.your_file_extension_is_not_allowed")}}!', 'warning');
                                                    return false;
                                                }
                                            }


                                            $('#{{$name_column}} .input-label').val(filename);

                                            $('#loading-{{$name_column}}').fadeIn();
                                            $('#btn-add-table-{{$name}}').addClass('disabled');
                                            $('#btn-upload-{{$name_column}}').addClass('disabled');
                                            is_uploading = true;

                                            //Upload File To Server
                                            uploadFiles{{$name_column}}(event);
                                        }

                                        function uploadFiles{{$name_column}}(event) {
                                            event.stopPropagation(); // Stop stuff happening
                                            event.preventDefault(); // Totally stop stuff happening

                                            // START A LOADING SPINNER HERE

                                            // Create a formdata object and add the files
                                            var data = new FormData();
                                            data.append('userfile', file);

                                            $.ajax({
                                                url: '{{CRUDBooster::mainpath("upload-file")}}',
                                                type: 'POST',
                                                data: data,
                                                cache: false,
                                                processData: false, // Don't process the files
                                                contentType: false, // Set content type to false as jQuery will tell the server its a query string request
                                                success: function (data, textStatus, jqXHR) {
                                                    console.log(data);
                                                    $('#btn-add-table-{{$name}}').removeClass('disabled');
                                                    $('#loading-{{$name_column}}').hide();
                                                    $('#btn-upload-{{$name_column}}').removeClass('disabled');
                                                    is_uploading = false;

                                                    var basename = data.split('/').reverse()[0];
                                                    $('#{{$name_column}} .input-label').val(basename);

                                                    $('#{{$name_column}} .input-id').val(data);
                                                },
                                                error: function (jqXHR, textStatus, errorThrown) {
                                                    $('#btn-add-table-{{$name}}').removeClass('disabled');
                                                    $('#btn-upload-{{$name_column}}').removeClass('disabled');
                                                    is_uploading = false;
                                                    // Handle errors here
                                                    console.log('ERRORS: ' + textStatus);
                                                    // STOP LOADING SPINNER
                                                    $('#loading-{{$name_column}}').hide();
                                                }
                                            });
                                        }

                                    </script>
                                @endpush

                            @elseif($col['type']=='select')

                                @if($col['parent_select'])
                                    @push('bottom')
                                        <script type="text/javascript">
                                            $(function () {
                                                $("#{{$name.$col['parent_select']}} , #{{$name.$col['name']}}").select2("destroy");

                                                $('#{{$name.$col['parent_select']}}, input:radio[name={{$name.$col['parent_select']}}]').change(function () {
                                                    var $current = $("#{{$name.$col['name']}}");
                                                    var parent_id = $(this).val();
                                                    var fk_name = "{{$col['parent_select']}}";
                                                    var fk_value = $('#{{$name.$col['parent_select']}}').val();
                                                    var datatable = "{{$col['datatable']}}".split(',');
                                                    var datatableWhere = "{{$col['datatable_where']}}";
                                                    var table = datatable[0].trim('');
                                                    var label = datatable[1].trim('');
                                                    var value = "{{$value}}";

                                                    if (fk_value != '') {
                                                        $current.html("<option value=''>{{trans('crudbooster.text_loading')}} {{$col['label']}}");
                                                        $.get("{{CRUDBooster::mainpath('data-table')}}?table=" + table + "&label=" + label + "&fk_name=" + fk_name + "&fk_value=" + fk_value + "&datatable_where=" + encodeURI(datatableWhere), function (response) {
                                                            if (response) {
                                                                $current.html("<option value=''>{{$default}}");
                                                                $.each(response, function (i, obj) {
                                                                    var selected = (value && value == obj.select_value) ? "selected" : "";
                                                                    $("<option " + selected + " value='" + obj.select_value + "'>" + obj.select_label + "</option>").appendTo("#{{$name.$col['name']}}");
                                                                });
                                                                $current.trigger('change');
                                                            }
                                                        });
                                                    } else {
                                                        $current.html("<option value=''>{{$default}}");
                                                    }
                                                });

                                                $('#{{$name.$col['parent_select']}}').trigger('change');
                                                $("#{{$name.$col['name']}}").trigger('change');

                                                $("#{{$name.$col['parent_select']}} , #{{$name.$col['name']}}").select2();

                                            })
                                        </script>
                                    @endpush
                                @endif

                                <select id='{{$name_column}}' name='child-{{$col["name"]}}'
                                        class='form-control select {{$col['required']?"required":""}}'
                                        {{($col['readonly']===true)?"readonly":""}}
                                >
                                    <option value=''>{{ $col["default"] }}</option>
                                    <?php
                                    if ($col['datatable']) {
                                        $tableJoin = explode(',', $col['datatable'])[0];
                                        $titleField = explode(',', $col['datatable'])[1];
                                        if (! $col['datatable_where']) {
                                            $data = CRUDBooster::get($tableJoin, NULL, "$titleField ASC");
                                        } else {
                                            $data = CRUDBooster::get($tableJoin, $col['datatable_where'], "$titleField ASC");
                                        }
                                        foreach ($data as $d) {
                                            $selected = ($d->id == $col['value']) ? ' selected' : '';
                                            echo "<option value='$d->id'$selected>".$d->$titleField."</option>";
                                        }
                                    } else {
                                        $data = $col['dataenum'];
                                        foreach ($data as $d) {
                                            $enum = explode('|', $d);
                                            if (count($enum) == 2) {
                                                $opt_value = $enum[0];
                                                $opt_label = $enum[1];
                                            } else {
                                                $opt_value = $opt_label = $enum[0];
                                            }
                                            $selected = ($opt_value == $col['value']) ? ' selected' : '';
                                            echo "<option value='$opt_value'$selected>$opt_label</option>";
                                        }
                                    }
                                    ?>
                                </select>

                                @elseif($col['type']=='select3')


                                <select id='{{$name_column}}' name='child-{{$col["name"]}}'
                                        class='form-control select {{$col['required']?"required":""}}'
                                        {{($col['readonly']===true)?"readonly":""}}
                                >
                                    <option value=''>{{ $col["default"] }}</option>
                                    <?php
                                    if ($col['queryBuilder']) {
                                        
                                        $data = $col['queryBuilder']->get(); ?>
                                        @push('bottom')
                                            <script type="text/javascript">
                                                let child_{{$col["name"]}} = {!! json_encode($data) !!}
                                            </script>
                                        @endpush
                                        <?php
                                        foreach ($data as $d) {
                                            $selected = ($d->id == $col['value']) ? ' selected' : '';
                                            echo "<option value='{$d->id}'$selected>{$d->title}</option>";
                                        }
                                    } else {
                                        $data = $col['dataenum'];
                                        foreach ($data as $d) {
                                            $enum = explode('|', $d);
                                            if (count($enum) == 2) {
                                                $opt_value = $enum[0];
                                                $opt_label = $enum[1];
                                            } else {
                                                $opt_value = $opt_label = $enum[0];
                                            }
                                            $selected = ($opt_value == $col['value']) ? ' selected' : '';
                                            echo "<option value='$opt_value'$selected>$opt_label</option>";
                                        }
                                    }
                                    ?>
                                </select>





                            @elseif($col['type']=='hidden')
                                <input type="{{$col['type']}}" id="{{$name.$col["name"]}}" name="child-{{$name.$col["name"]}}"
                                       value="{{$col["value"]}}">
                            @endif

                            @if($col['help'])
                                <div class='help-block'>
                                    {{$col['help']}}
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($col['formula'])
                        <?php
                        $formula = $col['formula'];
                        $formula_function_name = 'formula'.str_slug($name.$col['name'], '');
                        $script_onchange = "";
                        foreach ($form['columns'] as $c) {
                            if (strpos($formula, "[".$c['name']."]") !== false) {
                                $script_onchange .= "
                        $('#$name$c[name]').change(function() {
                            $formula_function_name();
                        });
                        ";
                            }
                            $formula = str_replace("[".$c['name']."]", "\$('#".$name.$c['name']."').val()", $formula);
                        }
                        ?>
                        @push('bottom')
                            <script type="text/javascript">
                                function {{ $formula_function_name }}() {
                                    var v = {!! $formula !!};
                                    $('#{{$name_column}}').val(v);
                                }

                                $(function () {
                                    {!! $script_onchange !!}
                                })
                            </script>
                        @endpush
                    @endif

                @endforeach

                @push('bottom')
                    <script type="text/javascript">
                        var currentRow = null;

                        function resetForm{{$name}}() {
                            $('#panel-form-{{$name}}').find("input[type=text],input[type=number],select,textarea").val('');
                            $('#panel-form-{{$name}}').find(".select2").val('').trigger('change');
                        }

                        function deleteRow{{$name}}(t) {

                            if (confirm("{{trans('crudbooster.delete_title_confirm')}}")) {
                                $(t).parent().parent().remove();
                                if ($('#table-{{$name}} tbody tr').length == 0) {
                                    var colspan = $('#table-{{$name}} thead tr th').length;
                                    $('#table-{{$name}} tbody').html("<tr class='trNull'><td colspan='" + colspan + "' align='center'>{{trans('crudbooster.table_data_not_found')}}</td></tr>");
                                }
                            }
                        }

                        function editRow{{$name}}(t) {
                            var p = $(t).parent().parent(); //parentTR
                            currentRow = p;
                            p.addClass('warning');
                            $('#btn-add-table-{{$name}}').val('{{trans("crudbooster.save_changes")}}');
                            @foreach($form['columns'] as $c)
                                @if($c['type']=='select' || $c['type']=='select3')
                                    $('#{{$name.$c["name"]}}').val(p.find(".{{$c['name']}} input").val()).trigger("change");
                                    console.log('{{$name.$c["name"]}}: ' + p.find(".{{$c['name']}} input").val())
                                @elseif($c['type']=='radio')
                                    var v = p.find(".{{$c['name']}} input").val();
                                    $('.{{$name.$c["name"]}}[value="' + v + '"]').prop('checked', true).trigger("change");
                                    
                                @elseif($c['type']=='datamodal')
                                    $('#{{$name.$c["name"]}} .input-label').val(p.find(".{{$c['name']}} .td-label").text());
                                    $('#{{$name.$c["name"]}} .input-id').val(p.find(".{{$c['name']}} input").val());
                                @elseif($c['type']=='upload')
                                    @if($c['upload_type']=='image')
                                        $('#{{$name.$c["name"]}} .input-label').val(p.find(".{{$c['name']}} img").data('label'));
                                    @else
                                        $('#{{$name.$c["name"]}} .input-label').val(p.find(".{{$c['name']}} a").data('label'));
                                    @endif
                                    $('#{{$name.$c["name"]}} .input-id').val(p.find(".{{$c['name']}} input").val());
                                @else
                                    $('#{{$name.$c["name"]}}').val(p.find(".{{$c['name']}} input").val()).trigger("keyup");
                                @endif
                            @endforeach
                            $('#child2_form{{$form['table']}}').show();
                        }

                        function validateForm{{$name}}() {
                            var is_false = 0;
                            $('#panel-form-{{$name}} .required').each(function () {
                                var v = $(this).val();
                                if (v == '') {
                                    sweetAlert("{{trans('crudbooster.alert_warning')}}", "{{trans('crudbooster.please_complete_the_form')}}", "warning");
                                    is_false += 1;
                                }
                            })

                            if (is_false == 0) {
                                return true;
                            } else {
                                return false;
                            }
                        }

                        function addToTable{{$name}}() {

                            if (validateForm{{$name}}() == false) {
                                return false;
                            }

                            var trRow = '<tr>';
                            @foreach($form['columns'] as $c)

                                @if($c['type']=='select' || $c['type']=='select3')
                                    trRow += "<td class='{{$c['name']}}'>" + $('#{{$name.$c["name"]}} option:selected').text() +
                                    "<input type='hidden' name='{{$name}}-{{$c['name']}}[]' value='" + $('#{{$name.$c["name"]}}').val() + "'/>" +
                                    "</td>";
                                @elseif($c['type']=='radio')
                                    trRow += "<td class='{{$c['name']}}'><span class='td-label'>" + $.trim($($('.{{$name.$c["name"]}}:checked')[0].parentNode).text()) + "</span>" +
                                    "<input type='hidden' name='{{$name}}-{{$c['name']}}[]' value='" + $('.{{$name.$c["name"]}}:checked').val() + "'/>" +
                                    "</td>";
                                @elseif($c['type']=='datamodal')
                                    trRow += "<td class='{{$c['name']}}'><span class='td-label'>" + $('#{{$name.$c["name"]}} .input-label').val() + "</span>" +
                                    "<input type='hidden' name='{{$name}}-{{$c['name']}}[]' value='" + $('#{{$name.$c["name"]}} .input-id').val() + "'/>" +
                                    "</td>";
                                @elseif($c['type']=='money'||$c['type']=='money2')
                                    trRow += "<td class='{{$c['name']}}'>" + $('#{{$name.$c["name"]}}').val() +
                                    "<input type='hidden' name='{{$name}}-{{$c['name']}}[]' value='" + $('#{{$name.$c["name"]}}.inputMoney').val().replace(',','').replace('U$S','').replace('$','').replace('.','') + "'/>" +
                                    "</td>";
                                @elseif($c['type']=='upload')
                                    @if($c['upload_type']=='image')
                                        trRow += "<td class='{{$c['name']}}'>" +
                                        "<a data-lightbox='roadtrip' href='{{asset('/')}}" + $('#{{$name.$c["name"]}} .input-id').val() + "'><img data-label='" + $('#{{$name.$c["name"]}} .input-label').val() + "' src='{{asset('/')}}" + $('#{{$name.$c["name"]}} .input-id').val() + "' width='50px' height='50px'/></a>" +
                                        "<input type='hidden' name='{{$name}}-{{$c['name']}}[]' value='" + $('#{{$name.$c["name"]}} .input-id').val() + "'/>" +
                                        "</td>";
                                    @else
                                        trRow += "<td class='{{$c['name']}}'><a data-label='" + $('#{{$name.$c["name"]}} .input-label').val() + "' href='{{asset('/')}}" + $('#{{$name.$c["name"]}} .input-id').val() + "'>" + $('#{{$name.$c["name"]}} .input-label').val() + "</a>" +
                                        "<input type='hidden' name='{{$name}}-{{$c['name']}}[]' value='" + $('#{{$name.$c["name"]}} .input-id').val() + "'/>" +
                                        "</td>";
                                    @endif
                                @else
                                    trRow += "<td class='{{$c['name']}}'>" + $('#{{$name.$c["name"]}}').val() +
                                    "<input type='hidden' name='{{$name}}-{{$c['name']}}[]' value='" + $('#{{$name.$c["name"]}}').val() + "'/>" +
                                    "</td>";
                                @endif
                            
                            @endforeach
                            trRow += "<td>" +
                                "<a href='#panel-form-{{$name}}' onclick='viewRow{{$name}}(this);' class='btn btn-xs btn-primary btn-detail'><i class='fa fa-eye'></i></a> " +
                                "<a href='#panel-form-{{$name}}' onclick='editRow{{$name}}(this);' class='btn btn-warning btn-xs'><i class='fa fa-pencil'></i></a> " +
                                "<a href='javascript:void(0)' onclick='deleteRow{{$name}}(this);' class='btn btn-danger btn-xs'><i class='fa fa-trash'></i></a></td>";
                            trRow += '</tr>';
                            $('#table-{{$name}} tbody .trNull').remove();
                            if (currentRow == null) {
                                $("#table-{{$name}} tbody").prepend(trRow);
                            } else {
                                currentRow.removeClass('warning');
                                currentRow.replaceWith(trRow);
                                currentRow = null;
                            }
                            $('#btn-add-table-{{$name}}').val('{{trans("crudbooster.button_add_to_table")}}');
                            $('#btn-reset-form-{{$name}}').click();
                            $('#child2_form{{$form['table']}}').hide();
                        }
                    </script>
                @endpush
            </div>
            <div class="panel-footer">
                <input type='button' class='btn btn-default' id="btn-reset-form-{{$name}}" onclick="resetForm{{$name}}()"
                       value='{{trans("crudbooster.button_reset")}}'/>
                <input type='button' id='btn-add-table-{{$name}}' class='btn btn-primary' onclick="addToTable{{$name}}();"
                       value='{{trans("crudbooster.button_add_to_table")}}'/>
                <input type='button' id='btn-preview-{{$name}}' class='btn btn-primary' onclick="showModalPreviewOperation()"
                       value='Previsualizar'/>
                    <input type='button' class='btn btn-default' id="btn-hide-form-{{$name}}" onclick="$('#child2_form{{$form['table']}}').hide();"
                       value='Cancelar'/>
            </div>
        </div>
    </div>
</div>