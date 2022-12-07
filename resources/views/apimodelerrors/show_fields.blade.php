<!-- Apimodel Id Field -->
<div class="col-sm-12">
    {!! Form::label('apimodel_id', 'Apimodel Id:') !!}
    <p>{{ $apimodelerror->apimodel_id }}</p>
</div>

<!-- Smtp Field -->
<div class="col-sm-12">
    {!! Form::label('smtp', 'Smtp:') !!}
    <p>{{ $apimodelerror->smtp }}</p>
</div>

<!-- Error Field -->
<div class="col-sm-12">
    {!! Form::label('error', 'Error:') !!}
    <p>{{ $apimodelerror->error }}</p>
</div>

