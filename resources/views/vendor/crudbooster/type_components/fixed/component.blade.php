<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}' style="{{@$form['style']}}">
    <label class='control-label col-sm-2'>
        {{$form['label']}}
    </label>

    <div class="{{$col_width?:'col-sm-10'}}">
        <div class="form-control disabled">
        <?php
        echo $queryBuilder;
        if ($form['queryBuilder']) {
            $remote_key = ($form['queryBuilder_remote_key']) ? $form['queryBuilder_remote_key'] : 'id';
            $form['queryBuilder']->where($remote_key , '=', $value);
            $data = $form['queryBuilder']->first();
            echo $data->text;
        }
        ?>
        <input type='hidden' id="{{$name}}" name="{{$name}}" value='{{$value}}' />
        </div>
    </div>
</div>