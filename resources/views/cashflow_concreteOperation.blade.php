<div id='modal-datamodal-cancel-operation' class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog large " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title"><i class='fa fa-dollar'></i> Concretar Operación: <b id="cancel-operation_concept"></b>
                </h3>
                <h4 id="cancel-operation_detail"></h4>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <div class="col-sm-12" style="padding: 5px 0 !important">
                    <label class="control-label col-sm-3">Fecha
                        <span class='text-danger' title='{!! trans('crudbooster.this_field_is_required') !!}'>*</span>
                    </label>
                    <!--div class="{{$col_width?:'col-sm-10'}}">
                        <input type="text" title="{{$form['label']}}" class="form-control "
                        name="cancel-operation_operation_date" id="cancel-operation_operation_date" value="">
                    </div-->
                    <div class="input-group col-sm-9">
                        <span class="input-group-addon open-datetimepicker"><a><i class='fa fa-calendar '></i></a></span>
                        <input type='text' title="Fecha" readonly class='form-control notfocus input_date cancel-operation'
                            name="cancel-operation_operation_date" id="cancel-operation_operation_date" value='{{ $col['value'] }}' style="width:200px" /> 
                    </div>
                    </div>
                </div>
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
                            }).on("change", function() {
                                set_dollar_value()
                            });

                            $('.open-datetimepicker').click(function () {
                                $(this).next('.input_date').datepicker('show');
                            });

                        });

                    </script>
                    @endpush

                <div class="form-group">
                    <div class="col-sm-12" style="padding: 5px 0 !important">
                    <label class="control-label col-sm-3">Cuenta
                        <span class='text-danger' title='{!! trans('crudbooster.this_field_is_required') !!}'>*</span>
                    </label>
                    <div class="input-group col-sm-9">
                    <select id='cancel-operation_account_id' name='cancel-operation_account_id' class='form-control select cancel-operation'  style="width:238px">
                        @foreach ($accounts as $account)
                            <option value='{!! $account->id !!}'>{{$account->name}}</option>
                        @endforeach
                    </select>
                    </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="col-sm-12" style="padding: 5px 0 !important">
                    <label class="control-label col-sm-3">Monto
                        <span class='text-danger' title='{!! trans('crudbooster.this_field_is_required') !!}'>*</span>
                    </label>
                    <div class="input-group col-sm-9">
                        <input type="text" title="{{$form['label']}}" class="form-control inputMoney cancel-operation"
                               name="cancel-operation_operation_amount" id="cancel-operation_operation_amount" value=""  style="width:238px" />
                        <!-- <div class="text-danger">{!! $errors->first($name)?'<i class="fa fa-info-circle"></i> '.$errors->first($name):'' !!}</div>
                        <p class="help-block">{{ @$form['help'] }}</p> -->
                    </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12" style="padding: 5px 0 !important">
                    <label class="control-label col-sm-3">Cotización Dolar
                        <span class='text-danger' title='{!! trans('crudbooster.this_field_is_required') !!}'>*</span>
                    </label>
                    <div class="input-group col-sm-9">
                        <input type="text" title="{{$form['label']}}" class="form-control cancel-operation"
                        name="cancel-operation_dollar_value" id="cancel-operation_dollar_value" value=""  style="width:238px" />
                    </div>
                    </div>
                </div>
                @push('bottom')
                <script type="text/javascript">
                    $('#cancel-operation_dollar_value').priceFormat({
                        'prefix' : "$ ",
                        'centsSeparator' : ",",
                        'thousandsSeparator' : ".",
                        'centsLimit'  : 2
                    });
                </script>
                @endpush

                <div class="form-group">
                    <div class="col-sm-12" style="padding: 5px 0 !important">
                        <label class="control-label col-sm-3">Notas</label>
                        <div class="input-group col-sm-9"><input type="text" title="{{$form['label']}}" class="form-control cancel-operation" name="cancel-operation_notes" id="cancel-operation_notes" value=""  style="width:238px" /></div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12" style="padding: 5px 0 !important">
                        <label class="control-label col-sm-3">Notas</label>
                        <div class="input-group col-sm-9"><input type="hidden" id="cancel-operation_operation_id" value="">
                <button class='btn btn-sm btn-default' class="close" data-dismiss="modal" >Cancelar</button>
                <button class='btn btn-sm btn-success' onclick="executeOperation( $('#cancel-operation_operation_id').val() ); return false">Concretar</button></div>
                    </div>
                </div>



                
              </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->