<!-- Api Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('api_id', 'Api Id:') !!}
    {!! Form::number('api_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Model Field -->
<div class="form-group col-sm-6">
    {!! Form::label('model', 'Model:') !!}
    {!! Form::text('model', null, ['class' => 'form-control','maxlength' => 100,'maxlength' => 100]) !!}
</div>

<!-- Recordnumber Field -->
<div class="form-group col-sm-6">
    {!! Form::label('recordnumber', 'Recordnumber:') !!}
    {!! Form::number('recordnumber', null, ['class' => 'form-control']) !!}
</div>

<!-- Insertednumber Field -->
<div class="form-group col-sm-6">
    {!! Form::label('insertednumber', 'Insertednumber:') !!}
    {!! Form::number('insertednumber', null, ['class' => 'form-control']) !!}
</div>

<!-- Updatednumber Field -->
<div class="form-group col-sm-6">
    {!! Form::label('updatednumber', 'Updatednumber:') !!}
    {!! Form::number('updatednumber', null, ['class' => 'form-control']) !!}
</div>

<!-- Errornumber Field -->
<div class="form-group col-sm-6">
    {!! Form::label('errornumber', 'Errornumber:') !!}
    {!! Form::number('errornumber', null, ['class' => 'form-control']) !!}
</div>