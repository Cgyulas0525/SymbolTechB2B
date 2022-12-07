@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1>
                        <img class="brand-image img-circle elevation-3 picture-small" src="data:image/png;base64, {{ utilityClass::echoPicture(utilityClass::getEmployeePicture($users->id)) }}">
                        {{ $users->name }} / {{ $users->customerName }}
                    </h1>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('adminlte-templates::common.errors')

        <div class="card">

            {!! Form::model($users, ['route' => ['users.update', $users->id], 'method' => 'patch']) !!}

            <div class="card-body">
                <div class="row">
                    @if ($users->rendszergazda > 0)
                        @include('users.editFields')
                    @else
                        @include('users.B2BCustomerUserEditFields')
                    @endif
                </div>
            </div>

            <div class="card-footer">
                {!! Form::submit(\App\Classes\langClass::trans('Ment'), ['class' => 'btn btn-primary', 'id' => 'saveBtn']) !!}
                @if ($users->rendszergazda > 0)
                    <a href="{{ route('users.index') }}" class="btn btn-default">{{ \App\Classes\langClass::trans('Kilép') }}</a>
                @else
                    <a href="{{ route('B2BCustomerUserIndex') }}" class="btn btn-default">{{ \App\Classes\langClass::trans('Kilép') }}</a>
                @endif
            </div>

           {!! Form::close() !!}

        </div>
    </div>
@endsection
