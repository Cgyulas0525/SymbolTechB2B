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

            @if ($users->rendszergazda == 0)
                {!! Form::model($users, ['route' => ['B2BUserDestroy', $users->id], 'method' => 'patch']) !!}
            @else
                {!! Form::model($users, ['route' => ['belsoUserDestroy', $users->id], 'method' => 'patch']) !!}
            @endif

            <div class="card-body">
                <div class="row">
                    @include('users.showFields')
                </div>
            </div>

            <div class="card-footer">
                <a href="{{ route('dIndex') }}" class="btn btn-primary">{{ \App\Classes\langClass::trans('Vissza') }}</a>
            </div>

            {!! Form::close() !!}

        </div>
    </div>
@endsection
