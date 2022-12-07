<!-- Apimodel Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('apimodel_id', 'Apimodel Id:') !!}
    {!! Form::number('apimodel_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Smtp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('smtp', 'Smtp:') !!}
    {!! Form::text('smtp', null, ['class' => 'form-control','maxlength' => 2000,'maxlength' => 2000]) !!}
</div>

<!-- Error Field -->
<div class="form-group col-sm-6">
    {!! Form::label('error', 'Error:') !!}
    {!! Form::text('error', null, ['class' => 'form-control','maxlength' => 2000,'maxlength' => 2000]) !!}
</div>