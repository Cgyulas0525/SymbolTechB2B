@for ( $i = 0; $i < count($akcio); $i++)
    {{ Form::button(env('PICTOGRAM') == 1 ? '<i class="'. $favIcon[$i] . '"></i>' :  \App\Classes\langClass::trans($btnName[$i]) ,
                    array('class' => $akcio[$i], 'type' => 'button', 'title' => \App\Classes\langClass::trans($title[$i]))) }}
@endfor
