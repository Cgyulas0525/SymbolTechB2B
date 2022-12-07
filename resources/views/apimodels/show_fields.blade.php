<!-- Api Id Field -->
<div class="col-sm-12">
    {!! Form::label('api_id', 'Api Id:') !!}
    <p>{{ $apimodel->api_id }}</p>
</div>

<!-- Model Field -->
<div class="col-sm-12">
    {!! Form::label('model', 'Model:') !!}
    <p>{{ $apimodel->model }}</p>
</div>

<!-- Recordnumber Field -->
<div class="col-sm-12">
    {!! Form::label('recordnumber', 'Recordnumber:') !!}
    <p>{{ $apimodel->recordnumber }}</p>
</div>

<!-- Insertednumber Field -->
<div class="col-sm-12">
    {!! Form::label('insertednumber', 'Insertednumber:') !!}
    <p>{{ $apimodel->insertednumber }}</p>
</div>

<!-- Updatednumber Field -->
<div class="col-sm-12">
    {!! Form::label('updatednumber', 'Updatednumber:') !!}
    <p>{{ $apimodel->updatednumber }}</p>
</div>

<!-- Errornumber Field -->
<div class="col-sm-12">
    {!! Form::label('errornumber', 'Errornumber:') !!}
    <p>{{ $apimodel->errornumber }}</p>
</div>

