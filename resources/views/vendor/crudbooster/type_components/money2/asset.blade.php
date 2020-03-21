@push('bottom')
    <script>
        $(function () {
            @foreach($forms as $form)
            @if($form['type'] == $type)
            let format{{ $form['name'] }} = @php 
                    echo json_encode(
                    array_merge(
                        array(
                            'prefix' => $form['prefix'] ? : "$ ",
                            'suffix' => $form['suffix'] ? : "",
                            'centsSeparator' => $form['centsSeparator'] ? : ",",
                            'thousandsSeparator' => $form['thousandsSeparator'] ? : ".",
                            'limit' => $form['limit'] ? : false,
                            'centsLimit' => $form['centsLimit'] ? : 2,
                            'clearPrefix' => $form['clearPrefix'] ? : false,
                            'clearSufix' => $form['clearSufix'] ? : false,
                            'allowNegative' => $form['allowNegative'] ? : true,
                            'insertPlusSign' => $form['insertPlusSign'] ? : false,
                            'clearOnEmpty' => $form['clearOnEmpty'] ? : true
                        ), 
                            (array) $form['priceformat_parameters']
                        )
                    );
                @endphp

            $('.inputMoney#{{ $form['name'] }}').priceFormat(
                format{{ $form['name'] }}
            );

            $('#form').on('submit',()=>{ 
                
                let newValue = $('#{{ $form['name'] }}').val(); 
                newValue = newValue.replace(format{{ $form['name'] }}.prefix, '')
                newValue = newValue.replace(format{{ $form['name'] }}.suffix, '')
                newValue = newValue.replace(format{{ $form['name'] }}.centsSeparator, '')
                newValue = newValue.replace(format{{ $form['name'] }}.thousandsSeparator, '')
                $('#{{ $form['name'] }}').val( newValue );
            })

            @endif
            @endforeach
        });
    </script>
@endpush
