@extends('backend.layouts.app')

@section('title',$formData['title'] . ' | '. $formData['subTitle'])

@section('content')
    <?php

    if(isset($formData) && isset($formData['inputs'])){
        foreach($formData['inputs'] as $k => $v){
            if(isset($data)){
                $formData['inputs'][$k]['value'] = $data->$k;
            }
            if(isset($formData['inputs'][$k]['type']) && $formData['inputs'][$k]['type'] == 'select'){
                $string = $formData['inputs'][$k]['selectOptions'];
                $formData['inputs'][$k]['selectOptions'] = ${$string};
            }
        }
    }

    ?>
    {!! generate_form($formData); !!}
@endsection