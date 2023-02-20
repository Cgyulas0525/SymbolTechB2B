@for ( $i = 0; $i < count($akcio); $i++)
    {{ Form::button(env('PICTOGRAM') == 1 ? '<i class="'. $favIcon[$i] . '"></i>' :  langClass::trans($btnName[$i]) ,
                    array('class' => $akcio[$i], 'type' => 'button', 'title' => langClass::trans($title[$i]))) }}
@endfor
