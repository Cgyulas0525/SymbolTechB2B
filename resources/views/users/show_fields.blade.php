<!-- Name Field -->
<div class="col-sm-12">
    {!! Form::label('name', 'Name:') !!}
    <p>{{ $users->name }}</p>
</div>

<!-- Email Field -->
<div class="col-sm-12">
    {!! Form::label('email', 'Email:') !!}
    <p>{{ $users->email }}</p>
</div>

<!-- Email Verified At Field -->
<div class="col-sm-12">
    {!! Form::label('email_verified_at', 'Email Verified At:') !!}
    <p>{{ $users->email_verified_at }}</p>
</div>

<!-- Password Field -->
<div class="col-sm-12">
    {!! Form::label('password', 'Password:') !!}
    <p>{{ $users->password }}</p>
</div>

<!-- Employee Id Field -->
<div class="col-sm-12">
    {!! Form::label('employee_id', 'Employee Id:') !!}
    <p>{{ $users->employee_id }}</p>
</div>

<!-- Customercontact Id Field -->
<div class="col-sm-12">
    {!! Form::label('customercontact_id', 'Customercontact Id:') !!}
    <p>{{ $users->customercontact_id }}</p>
</div>

<!-- Rendszergazda Field -->
<div class="col-sm-12">
    {!! Form::label('rendszergazda', 'Rendszergazda:') !!}
    <p>{{ $users->rendszergazda }}</p>
</div>

<!-- Megjegyzes Field -->
<div class="col-sm-12">
    {!! Form::label('megjegyzes', 'Megjegyzes:') !!}
    <p>{{ $users->megjegyzes }}</p>
</div>

<!-- Customeraddress Field -->
<div class="col-sm-12">
    {!! Form::label('CustomerAddress', 'Customeraddress:') !!}
    <p>{{ $users->CustomerAddress }}</p>
</div>

<!-- Transportmode Field -->
<div class="col-sm-12">
    {!! Form::label('TransportMode', 'Transportmode:') !!}
    <p>{{ $users->TransportMode }}</p>
</div>

<!-- Remember Token Field -->
<div class="col-sm-12">
    {!! Form::label('remember_token', 'Remember Token:') !!}
    <p>{{ $users->remember_token }}</p>
</div>

<!-- Image Url Field -->
<div class="col-sm-12">
    {!! Form::label('image_url', 'Image Url:') !!}
    <p>{{ $users->image_url }}</p>
</div>

