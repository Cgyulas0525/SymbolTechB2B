{{--@extends('layouts.app')--}}

{{--@section('content')--}}
{{--    <section class="content-header">--}}
{{--        <h1>--}}
{{--            <img class="brand-image img-circle elevation-3 picture-small" src="data:image/png;base64, {{ utilityClass::echoPicture(utilityClass::getEmployeePicture($users->id)) }}">--}}
{{--            {{ $users->name }} / {{ $users->customerName }}--}}
{{--        </h1>--}}
{{--    </section>--}}
{{--    <div class="content">--}}
{{--        <div class="box box-primary">--}}
{{--            <div class="box-body">--}}
{{--                <div class="row" style="padding-left: 20px">--}}
{{--                    {!! Form::model($users, ['route' => ['users.destroy', $users->id], 'method' => 'patch']) !!}--}}

{{--                    @include('users.showFields')--}}

{{--                    {!! Form::close() !!}--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--@endsection--}}



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
                @if ( $users->id != myUser::user()->id)
                    {!! Form::submit(\App\Classes\langClass::trans('Töröl'), ['class' => 'btn btn-danger', 'id' => 'saveBtn']) !!}
                @endif
                @if ( $users->rendszergazda == 0 )
                    <a href="{{ route('B2BCustomerUserIndex') }}" class="btn btn-default">{{ \App\Classes\langClass::trans('Kilép') }}</a>
                @else
                    <a href="{{ route('users.index') }}" class="btn btn-default">{{ \App\Classes\langClass::trans('Kilép') }}</a>
                @endif
            </div>

            {!! Form::close() !!}

        </div>
    </div>
@endsection
