@for ( $i = 0; $i < count($akcio); $i++)
    {{ Form::button(langClass::trans($btnName[$i]), array('class' => $akcio[$i], 'type' => 'button', 'title' => langClass::trans($title[$i]))) }}
@endfor
