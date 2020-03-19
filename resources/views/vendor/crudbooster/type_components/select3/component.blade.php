<div class="form-group {{$header_group_class}} {{ ($errors->first($name))?'has-error': '' }}" id="form-group-{{$name}}" style="{{@$form['style']}}">
    <label class="control-label col-sm-2">{{$form['label']}}
        @if($required)
            <span class='text-danger' title='{!! trans('crudbooster.this_field_is_required') !!}'>*</span>
        @endif
    </label>

    <div class="{{$col_width?:'col-sm-10'}}">

        <select id='{{$form["name"]}}' name='{{$form["name"]}}'
        class='form-control select {{$col['required']?"required":""}}'
        {{($col['readonly']===true)?"readonly":""}}
>
    <option value=''>{{ $form["default"] }}</option>
    <?php
    if ($form['queryBuilder']) {
        
        $data = $form['queryBuilder']->get(); ?>
        @push('bottom')
            <script type="text/javascript">
                let {{$form["name"]}}_data = {!! json_encode($data) !!}
                let select3 = true;
            </script>
        @endpush
        <?php
        foreach ($data as $d) {
            $selected = ($d->id == $value) ? ' selected' : '';
            echo "<option value='{$d->id}'$selected>{$d->title}</option>";
        }
    } else {
        $data = $form['dataenum'];
        foreach ($data as $d) {
            $enum = explode('|', $d);
            if (count($enum) == 2) {
                $opt_value = $enum[0];
                $opt_label = $enum[1];
            } else {
                $opt_value = $opt_label = $enum[0];
            }
            $selected = ($opt_value == $value) ? ' selected' : '';
            echo "<option value='$opt_value'$selected>$opt_label</option>";
        }
    }
    ?>
</select>
        <div class="text-danger">{!! $errors->first($name)?'<i class="fa fa-info-circle"></i> '.$errors->first($name):'' !!}</div>
        <p class="help-block">{{ @$form['help'] }}</p>
    </div>
</div>
